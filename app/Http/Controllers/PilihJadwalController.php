<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\JadwalBimbingan;
use App\Models\UsulanBimbingan;
use App\Models\Mahasiswa;
use App\Models\Dosen;

class PilihJadwalController extends Controller
{
    protected $googleCalendarController;

    public function __construct(GoogleCalendarController $googleCalendarController)
    {
        $this->googleCalendarController = $googleCalendarController;
    }

    public function index()
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        $isConnected = false;
        if ($mahasiswa->hasGoogleCalendarConnected()) {
            $isConnected = app(GoogleCalendarController::class)->validateAndRefreshToken();
        }

        // Ambil daftar dosen
        $dosenList = DB::table('dosens')
            ->select('nip', 'nama')
            ->get()
            ->map(function ($dosen) {
                return [
                    'nip' => $dosen->nip,
                    'nama' => $dosen->nama
                ];
            })
            ->toArray();

        // Cek total jadwal dengan jenis bimbingan untuk debugging
        $totalJadwalDenganJenis = DB::table('jadwal_bimbingans')
            ->whereNotNull('jenis_bimbingan')
            ->where('jenis_bimbingan', '!=', '')
            ->count();

        Log::info('Total jadwal dengan jenis_bimbingan:', ['count' => $totalJadwalDenganJenis]);

        // Ambil jenis bimbingan yang tersedia untuk setiap dosen
        $jenisBimbinganPerDosen = [];
        foreach ($dosenList as $dosen) {
            // Query jadwal yang memiliki jenis bimbingan
            $jadwalDenganJenis = DB::table('jadwal_bimbingans')
                ->where('nip', $dosen['nip'])
                ->whereNotNull('jenis_bimbingan')
                ->where('jenis_bimbingan', '<>', '') // Pastikan tidak kosong
                ->where('status', 'tersedia')
                ->distinct()
                ->select('jenis_bimbingan') // Pilih kolom saja untuk efisiensi
                ->pluck('jenis_bimbingan')
                ->toArray();

            // Debug untuk setiap dosen
            Log::info("Jenis bimbingan untuk dosen {$dosen['nip']} ({$dosen['nama']}):", $jadwalDenganJenis);

            if (!empty($jadwalDenganJenis)) {
                $jenisBimbinganPerDosen[$dosen['nip']] = $jadwalDenganJenis;
            }
        }

        // Log untuk debugging
        Log::info('Data yang dikirim ke view:', [
            'jenisBimbinganPerDosen' => $jenisBimbinganPerDosen,
            'countDosen' => count($dosenList)
        ]);

        return view('bimbingan.mahasiswa.pilihjadwal', [
            'dosenList' => $dosenList,
            'isConnected' => $isConnected,
            'email' => $mahasiswa->email,
            'jenisBimbinganPerDosen' => $jenisBimbinganPerDosen
        ]);
    }

    public function store(Request $request)
{
    try {
        if (!$this->googleCalendarController->validateAndRefreshToken()) {
            return response()->json(['error' => 'Belum terautentikasi dengan Google Calendar'], 401);
        }
        Log::info('Request pengajuan bimbingan:', $request->all());

        // Validasi request
        $request->validate([
            'nip' => 'required|exists:dosens,nip',
            'jenis_bimbingan' => 'required|in:skripsi,kp,akademik,konsultasi,mbkm,lainnya',
            'jadwal_id' => 'required|exists:jadwal_bimbingans,id',
            'deskripsi' => 'nullable|string'
        ]);

        DB::beginTransaction();

        // Cek jadwal dan dosen
        $jadwal = DB::table('jadwal_bimbingans as jb')
            ->join('dosens as d', 'jb.nip', '=', 'd.nip')
            ->where('jb.id', $request->jadwal_id)
            ->where('jb.status', 'tersedia')
            ->where('jb.waktu_mulai', '>', now())
            ->select('jb.*', 'd.nama as dosen_nama')
            ->first();

        if (!$jadwal) {
            throw new \Exception('Jadwal tidak tersedia atau sudah penuh');
        }

        // Cek bentrok jadwal
        $bentrok = DB::table('usulan_bimbingans')
            ->where('nim', auth()->user()->nim)
            ->where('tanggal', Carbon::parse($jadwal->waktu_mulai)->toDateString())
            ->where(function ($query) use ($jadwal) {
                $query->whereBetween('waktu_mulai', [
                    Carbon::parse($jadwal->waktu_mulai)->format('H:i'),
                    Carbon::parse($jadwal->waktu_selesai)->format('H:i')
                ])
                    ->orWhereBetween('waktu_selesai', [
                        Carbon::parse($jadwal->waktu_mulai)->format('H:i'),
                        Carbon::parse($jadwal->waktu_selesai)->format('H:i')
                    ]);
            })
            ->where('status', '!=', 'DITOLAK')
            ->exists();

        if ($bentrok) {
            return response()->json([
                'available' => false,
                'message' => 'Anda sudah memiliki jadwal bimbingan di waktu yang sama'
            ]);
        }

        $mahasiswa = Auth::guard('mahasiswa')->user();
        
        // Buat event di Google Calendar dengan format datetime yang benar
        // Menggunakan Carbon::createFromFormat untuk memastikan objek Carbon valid
        $waktuMulaiCarbon = Carbon::parse($jadwal->waktu_mulai);
        $waktuSelesaiCarbon = Carbon::parse($jadwal->waktu_selesai);
        
        // Debug waktu untuk memastikan format yang benar
        Log::info('Waktu untuk Google Calendar:', [
            'waktu_mulai_original' => $jadwal->waktu_mulai,
            'waktu_selesai_original' => $jadwal->waktu_selesai,
            'waktu_mulai_carbon' => $waktuMulaiCarbon->toDateTimeString(),
            'waktu_selesai_carbon' => $waktuSelesaiCarbon->toDateTimeString(),
        ]);
        
        // Perhatikan: GoogleCalendarController mungkin mengharapkan objek DateTime
        // bukan array dengan format dateTime
        $eventData = [
            'summary' => 'Bimbingan ' . ucfirst($request->jenis_bimbingan) . ' dengan ' . $jadwal->dosen_nama,
            'description' => $request->deskripsi ?? 'Bimbingan ' . ucfirst($request->jenis_bimbingan),
            'location' => $jadwal->lokasi ?? 'Online',
            'start' => $waktuMulaiCarbon, // Objek Carbon, bukan string
            'end' => $waktuSelesaiCarbon, // Objek Carbon, bukan string
            'attendees' => [
                ['email' => $mahasiswa->email]
            ]
        ];
        
        // Membuat event di Google Calendar
        $createdEvent = $this->googleCalendarController->createEvent($eventData);
        
        // Simpan ke database
        $bimbingan = UsulanBimbingan::create([
            'nim' => $mahasiswa->nim,
            'nip' => $request->nip,
            'dosen_nama' => $jadwal->dosen_nama,
            'mahasiswa_nama' => $mahasiswa->nama,
            'jenis_bimbingan' => $request->jenis_bimbingan,
            'tanggal' => Carbon::parse($jadwal->waktu_mulai)->toDateString(),
            'waktu_mulai' => Carbon::parse($jadwal->waktu_mulai)->format('H:i'),
            'waktu_selesai' => Carbon::parse($jadwal->waktu_selesai)->format('H:i'),
            'lokasi' => $jadwal->lokasi,
            'deskripsi' => $request->deskripsi,
            'status' => 'USULAN',
            'event_id' => $jadwal->event_id,
            'nomor_antrian' => null,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Update jumlah pendaftar dan status jadwal
        $jadwalModel = JadwalBimbingan::find($request->jadwal_id);
        if ($jadwalModel) {
            $jadwalModel->jumlah_pendaftar = $jadwalModel->jumlah_pendaftar + 1;
            // Periksa apakah jadwal sudah penuh setelah pendaftaran ini
            if ($jadwalModel->has_kuota_limit && $jadwalModel->jumlah_pendaftar >= $jadwalModel->kapasitas) {
                $jadwalModel->status = JadwalBimbingan::STATUS_PENUH;
            }
            $jadwalModel->save();
        }

        DB::commit();

        Log::info('Berhasil membuat usulan bimbingan:', [
            'bimbingan_id' => $bimbingan->id,
            'event_id' => $jadwal->event_id
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Jadwal bimbingan berhasil diajukan',
            'data' => $bimbingan
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error membuat jadwal bimbingan: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
}
    public function getAvailableJadwal(Request $request)
    {
        try {
            $nip = $request->nip;
            $jenisBimbingan = $request->jenis_bimbingan;

            // Log untuk debugging
            Log::info('Request get available jadwal:', [
                'nip' => $nip,
                'jenis_bimbingan' => $jenisBimbingan
            ]);

            // Debug: Cek jadwal tersedia untuk dosen ini
            $totalJadwalDosen = DB::table('jadwal_bimbingans')
                ->where('nip', $nip)
                ->where('status', 'tersedia')
                ->where('waktu_mulai', '>', now())
                ->count();

            Log::info('Total jadwal tersedia untuk dosen:', [
                'nip' => $nip,
                'count' => $totalJadwalDosen
            ]);

            // Debug: Cek jadwal tersedia dengan jenis bimbingan ini
            $totalJadwalDenganJenis = DB::table('jadwal_bimbingans')
                ->where('nip', $nip)
                ->where('jenis_bimbingan', $jenisBimbingan)
                ->where('status', 'tersedia')
                ->where('waktu_mulai', '>', now())
                ->count();

            Log::info('Total jadwal dengan jenis spesifik:', [
                'nip' => $nip,
                'jenis_bimbingan' => $jenisBimbingan,
                'count' => $totalJadwalDenganJenis
            ]);

            Log::info(
                'Semua jadwal untuk dosen ini:',
                DB::table('jadwal_bimbingans')
                    ->where('nip', $nip)
                    ->select('id', 'event_id', 'jenis_bimbingan', 'waktu_mulai')
                    ->get()
                    ->toArray()
            );

            // Debug query SQL
            DB::enableQueryLog();


            // Query jadwal-jadwal yang tersedia
            $jadwals = DB::table('jadwal_bimbingans as jb')
                ->join('dosens as d', 'jb.nip', '=', 'd.nip')
                ->select(
                    'jb.id',
                    'jb.event_id',
                    'jb.waktu_mulai',
                    'jb.waktu_selesai',
                    'jb.catatan',
                    'jb.lokasi',
                    'jb.jenis_bimbingan',
                    'jb.kapasitas',
                    'jb.sisa_kapasitas',
                    'jb.has_kuota_limit',
                    'jb.jumlah_pendaftar',
                    'jb.status',
                    'd.nama as dosen_nama'
                )
                ->where('jb.nip', $nip)
                ->where('jb.status', 'tersedia') // Hanya tampilkan yang tersedia
                ->where('jb.waktu_mulai', '>', now())
                ->where(function ($query) use ($jenisBimbingan) {
                    $query->where('jb.jenis_bimbingan', $jenisBimbingan)
                        ->orWhereNull('jb.jenis_bimbingan');
                })
                ->get();

            Log::info('Nilai yang akan disimpan:', [
                'jenis_bimbingan' => $jenisBimbingan,
                'request_data' => $request->all()
            ]);
            Log::info('Query SQL:', ['query' => DB::getQueryLog()]);

            // Debug
            Log::info("Jumlah jadwal ditemukan: " . $jadwals->count());
            if ($jadwals->count() > 0) {
                Log::info("Contoh jadwal pertama:", (array)$jadwals->first());
            }

            // Transform jadwals untuk format yang sesuai dengan frontend
            $transformedJadwals = [];
            foreach ($jadwals as $jadwal) {
                // Hitung jumlah pendaftar dari database
                $pendaftarCount = DB::table('usulan_bimbingans')
                    ->where('event_id', $jadwal->event_id)
                    ->whereIn('status', ['USULAN', 'DITERIMA', 'DISETUJUI'])
                    ->count();

                // Update status jadwal
                $jadwalModel = JadwalBimbingan::where('event_id', $jadwal->event_id)->first();
                if ($jadwalModel) {
                    $jadwalModel->jumlah_pendaftar = $pendaftarCount;
                    $jadwalModel->updateStatus();

                    // Refresh data dari database
                    $jadwal->jumlah_pendaftar = $pendaftarCount;
                    $jadwal->status = $jadwalModel->status;
                    $jadwal->sisa_kapasitas = $jadwalModel->sisa_kapasitas;
                }
            }

            // Filter lagi jadwal yang tersedia setelah update status
            $jadwals = collect($jadwals)->filter(function ($jadwal) {
                return $jadwal->status === 'tersedia';
            })->values();

            // Transform jadwals untuk frontend
            $transformedJadwals = [];
            foreach ($jadwals as $jadwal) {
                $tanggal = Carbon::parse($jadwal->waktu_mulai)->format('Y-m-d');
                $waktuMulai = Carbon::parse($jadwal->waktu_mulai)->format('H:i');
                $waktuSelesai = Carbon::parse($jadwal->waktu_selesai)->format('H:i');

                $hari = Carbon::parse($jadwal->waktu_mulai)->locale('id')->isoFormat('dddd');
                $tanggalFormat = Carbon::parse($jadwal->waktu_mulai)->locale('id')->isoFormat('D MMMM YYYY');

                // Format display text dengan status yang akurat
                $displayText = "{$hari}, {$tanggalFormat} | {$waktuMulai}-{$waktuSelesai}";

                if ($jadwal->kapasitas > 0 && $jadwal->has_kuota_limit) {
                    // Tampilkan status kuota yang akurat
                    $displayText .= " | Kuota: {$jadwal->jumlah_pendaftar}/{$jadwal->kapasitas} terpakai";

                    // Tandai jadwal penuh jika jumlah pendaftar >= kapasitas
                    if ($jadwal->jumlah_pendaftar >= $jadwal->kapasitas) {
                        continue; // Skip jadwal yang penuh
                    }
                } else {
                    $displayText .= " | Kuota Tidak Terbatas";
                }

                $transformedJadwals[] = [
                    'id' => $jadwal->id,
                    'event_id' => $jadwal->event_id,
                    'tanggal' => $tanggal,
                    'waktu' => $waktuMulai,
                    'waktu_selesai' => $waktuSelesai,
                    'text' => $displayText,
                    'is_selected' => false,
                    'sisa_kapasitas' => $jadwal->sisa_kapasitas,
                    'jumlah_pendaftar' => $jadwal->jumlah_pendaftar,
                    'has_kuota_limit' => $jadwal->has_kuota_limit,
                    'kapasitas' => $jadwal->kapasitas,
                    'status' => $jadwal->status
                ];
            }

            return response()->json([
                'status' => 'success',
                'data' => $transformedJadwals
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting available jadwal: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memuat jadwal: ' . $e->getMessage()
            ], 400);
        }
    }

    public function checkAvailability(Request $request)
    {
        try {
            $request->validate([
                'jadwal_id' => 'required|exists:jadwal_bimbingans,id',
                'jenis_bimbingan' => 'required|in:skripsi,kp,akademik,konsultasi,mbkm,lainnya'
            ]);

            Log::info('Check Availability Request:', [
                'nim' => auth()->user()->nim,
                'jadwal_id' => $request->jadwal_id,
                'jenis_bimbingan' => $request->jenis_bimbingan
            ]);

            // Get jadwal untuk memeriksa
            $jadwal = DB::table('jadwal_bimbingans')
                ->where('id', $request->jadwal_id)
                ->first();

            if (!$jadwal) {
                Log::info('Jadwal tidak ditemukan');
                return response()->json([
                    'available' => false,
                    'message' => 'Jadwal tidak ditemukan'
                ]);
            }

            // Log untuk debugging
            Log::info('Jadwal detail for checkAvailability:', (array)$jadwal);
            Log::info('Parameter pengecekan availability:', [
                'nim_dari_auth' => auth()->user()->nim,
                'event_id' => $jadwal->event_id
            ]);

            // Periksa apakah jadwal sesuai dengan jenis bimbingan yang dipilih
            if (property_exists($jadwal, 'jenis_bimbingan') && $jadwal->jenis_bimbingan && $jadwal->jenis_bimbingan !== $request->jenis_bimbingan) {
                Log::info('Jadwal tidak tersedia untuk jenis bimbingan yang dipilih');
                return response()->json([
                    'available' => false,
                    'message' => 'Jadwal tidak tersedia untuk jenis bimbingan yang dipilih'
                ]);
            }

            if (property_exists($jadwal, 'has_kuota_limit') && $jadwal->has_kuota_limit) {
                if (property_exists($jadwal, 'sisa_kapasitas') && $jadwal->sisa_kapasitas <= 0) {
                    Log::info('Kuota jadwal sudah penuh');
                    return response()->json([
                        'available' => false,
                        'message' => 'Kuota jadwal ini sudah penuh'
                    ]);
                }

                // Jika kuota terbatas dan hanya untuk 1 mahasiswa, cek apakah sudah ada yang mengambil
                if (property_exists($jadwal, 'kapasitas') && $jadwal->kapasitas == 1) {
                    $existingBooking = DB::table('usulan_bimbingans')
                        ->where('event_id', $jadwal->event_id)
                        ->where('nim', '!=', auth()->user()->nim)
                        ->where('status', '!=', 'DITOLAK')
                        ->exists();

                    if ($existingBooking) {
                        Log::info('Jadwal sudah diambil mahasiswa lain');
                        return response()->json([
                            'available' => false,
                            'message' => 'Jadwal sudah diambil mahasiswa lain'
                        ]);
                    }
                }
            }

            // Cek existing bimbingan dengan event_id yang sama
            $existingBimbingan = DB::table('usulan_bimbingans')
                ->where('nim', auth()->user()->nim)
                ->where('event_id', $jadwal->event_id)
                ->whereIn('status', ['USULAN', 'DITERIMA', 'DISETUJUI'])
                ->get();

            Log::info('Existing bimbingan yang ditemukan:', [
                'count' => $existingBimbingan->count(),
                'data' => $existingBimbingan->toArray()
            ]);

            if ($existingBimbingan->count() > 0) {
                Log::info('Mahasiswa sudah pernah mengajukan bimbingan untuk jadwal ini');
                return response()->json([
                    'available' => false,
                    'message' => 'Anda sudah pernah mengajukan bimbingan untuk jadwal ini'
                ]);
            }

            // Debug untuk melihat bimbingan yang mungkin bentrok
            DB::enableQueryLog();
            $bentrokQuery = DB::table('usulan_bimbingans')
                ->where('nim', auth()->user()->nim)
                ->where('tanggal', Carbon::parse($jadwal->waktu_mulai)->toDateString())
                ->where('status', '!=', 'DITOLAK')
                ->where('status', '!=', 'DIBATALKAN');

            $bentrokData = $bentrokQuery->get();

            Log::info('SQL Query untuk cek bentrok:', DB::getQueryLog());
            Log::info('Data bentrok yang ditemukan:', [
                'count' => $bentrokData->count(),
                'data' => $bentrokData->toArray(),
                'tanggal_cek' => Carbon::parse($jadwal->waktu_mulai)->toDateString(),
                'waktu_mulai_cek' => Carbon::parse($jadwal->waktu_mulai)->format('H:i'),
                'waktu_selesai_cek' => Carbon::parse($jadwal->waktu_selesai)->format('H:i')
            ]);

            // Cek bentrok dengan satu per satu jadwal untuk memastikan
            $adaBentrok = false;
            $alasanBentrok = '';

            foreach ($bentrokData as $existing) {
                $existingMulai = $existing->waktu_mulai;
                $existingSelesai = $existing->waktu_selesai;
                $jadwalMulai = Carbon::parse($jadwal->waktu_mulai)->format('H:i');
                $jadwalSelesai = Carbon::parse($jadwal->waktu_selesai)->format('H:i');

                // Cek apakah jadwal baru bentrok dengan jadwal yang sudah ada
                $isBentrok = false;

                // Jadwal baru mulai di tengah jadwal lama
                if ($jadwalMulai >= $existingMulai && $jadwalMulai < $existingSelesai) {
                    $isBentrok = true;
                    $alasanBentrok = "Jadwal baru mulai saat jadwal lama belum selesai";
                }
                // Jadwal baru selesai di tengah jadwal lama
                elseif ($jadwalSelesai > $existingMulai && $jadwalSelesai <= $existingSelesai) {
                    $isBentrok = true;
                    $alasanBentrok = "Jadwal baru selesai saat jadwal lama masih berlangsung";
                }
                // Jadwal baru mencakup jadwal lama
                elseif ($jadwalMulai <= $existingMulai && $jadwalSelesai >= $existingSelesai) {
                    $isBentrok = true;
                    $alasanBentrok = "Jadwal baru mencakup seluruh jadwal lama";
                }

                Log::info('Analisis bentrok jadwal:', [
                    'existing_id' => $existing->id,
                    'existing_mulai' => $existingMulai,
                    'existing_selesai' => $existingSelesai,
                    'jadwal_baru_mulai' => $jadwalMulai,
                    'jadwal_baru_selesai' => $jadwalSelesai,
                    'is_bentrok' => $isBentrok,
                    'alasan' => $alasanBentrok
                ]);

                if ($isBentrok) {
                    $adaBentrok = true;
                    break;
                }
            }

            if ($adaBentrok) {
                Log::info('Mahasiswa sudah memiliki jadwal bimbingan di waktu yang sama. Alasan: ' . $alasanBentrok);
                return response()->json([
                    'available' => false,
                    'message' => 'Anda sudah memiliki jadwal bimbingan di waktu yang sama'
                ]);
            }

            Log::info('Pengecekan berhasil, jadwal tersedia untuk: ' . auth()->user()->nim);
            return response()->json([
                'available' => true
            ]);
        } catch (\Exception $e) {
            Log::error('Check Availability Error:', ['error' => $e->getMessage()]);
            return response()->json([
                'available' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 400);
        }
    }
    public function getJenisBimbingan($nip)
    {
        try {
            Log::info('Fetching jenis bimbingan for dosen:', ['nip' => $nip]);

            // 1. Cek jadwal dengan jenis bimbingan yang ditentukan
            DB::enableQueryLog();

            $jadwalDenganJenis = DB::table('jadwal_bimbingans')
                ->where('nip', $nip)
                ->whereNotNull('jenis_bimbingan')
                ->where('jenis_bimbingan', '!=', '')
                ->where('status', 'tersedia')
                ->where('waktu_mulai', '>', now())
                ->distinct()
                ->pluck('jenis_bimbingan')
                ->toArray();

            Log::info('SQL query jadwal dengan jenis:', [
                'query' => DB::getQueryLog(),
                'result' => $jadwalDenganJenis
            ]);
            Log::info('Jadwal dengan jenis bimbingan spesifik:', [
                'count' => count($jadwalDenganJenis),
                'jenis' => $jadwalDenganJenis
            ]);

            // 2. Cek apakah ada jadwal tanpa jenis bimbingan spesifik
            $hasUnspecifiedJadwal = DB::table('jadwal_bimbingans')
                ->where('nip', $nip)
                ->where(function ($query) {
                    $query->whereNull('jenis_bimbingan')
                        ->orWhere('jenis_bimbingan', '');
                })
                ->where('status', 'tersedia')
                ->where('waktu_mulai', '>', now())
                ->exists();

            Log::info('Ada jadwal tanpa jenis bimbingan spesifik:', [
                'exists' => $hasUnspecifiedJadwal
            ]);


            // 3. Tentukan jenis bimbingan yang akan ditampilkan
            $jenisBimbingan = [];

            // Perubahan: Tampilkan semua jenis bimbingan yang ada di sistem
            $allJenisBimbingan = ['skripsi', 'kp', 'akademik', 'konsultasi', 'mbkm', 'lainnya'];

            // Ada 2 opsi di sini - pilih salah satu sesuai kebutuhan:

            // OPSI 1: Hanya tampilkan jenis bimbingan yang tersedia dalam jadwal dosen
            if (!empty($jadwalDenganJenis)) {
                // Jika ada jadwal dengan jenis spesifik, tampilkan jenis-jenis tersebut
                $jenisBimbingan = $jadwalDenganJenis;
                Log::info('Menampilkan jenis bimbingan dari jadwal spesifik');
            }

            if ($hasUnspecifiedJadwal) {
                // Jika ada jadwal tanpa jenis spesifik, tambahkan semua jenis bimbingan
                $allJenisBimbingan = ['skripsi', 'kp', 'akademik', 'konsultasi', 'mbkm', 'lainnya'];
                $jenisBimbingan = array_merge($jenisBimbingan, $allJenisBimbingan);
                Log::info('Menambahkan semua jenis bimbingan karena ada jadwal non-spesifik');
            }

            // Hapus duplikat dan urutkan
            $jenisBimbingan = array_unique($jenisBimbingan);
            sort($jenisBimbingan);

            Log::info('Jenis bimbingan yang akan ditampilkan:', $jenisBimbingan);

            return response()->json([
                'success' => true,
                'jenisBimbingan' => $jenisBimbingan,
                'hasUnspecified' => $hasUnspecifiedJadwal,
                'jadwalDenganJenis' => $jadwalDenganJenis // Tambahkan ini untuk debugging
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting jenis bimbingan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function cancelBooking($id)
    {
        try {
            DB::beginTransaction();

            // Cari usulan bimbingan
            $usulan = UsulanBimbingan::where('id', $id)
                ->where('nim', Auth::guard('mahasiswa')->user()->nim)
                ->whereIn('status', ['USULAN', 'DISETUJUI', 'DITERIMA'])
                ->firstOrFail();

            // Update status usulan
            $usulan->status = 'DIBATALKAN';
            $usulan->keterangan = 'Dibatalkan oleh mahasiswa';
            $usulan->save();

            // Kurangi jumlah pendaftar dan update status jadwal
            $jadwal = JadwalBimbingan::where('event_id', $usulan->event_id)->first();
            if ($jadwal) {
                // Ini akan mengurangi jumlah_pendaftar dan mengupdate status
                $jadwal->incrementKapasitas();
                $jadwal->updateStatus();
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Bimbingan berhasil dibatalkan'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error canceling booking: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat membatalkan pendaftaran'
            ], 500);
        }
    }
}
