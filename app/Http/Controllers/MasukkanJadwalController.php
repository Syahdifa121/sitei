<?php

namespace App\Http\Controllers;

use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\JadwalBimbingan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class MasukkanJadwalController extends Controller
{
    protected $googleCalendarController;

    public function __construct(GoogleCalendarController $googleCalendarController)
    {
        $this->googleCalendarController = $googleCalendarController;
    }

    /**
     * Menampilkan halaman masukkan jadwal
     */
    public function index()
    {
        $dosen = Auth::guard('dosen')->user();

        // Coba refresh token terlebih dahulu
        $isConnected = false;
        if ($dosen->hasGoogleCalendarConnected()) {
            $isConnected = app(GoogleCalendarController::class)->validateAndRefreshToken();
        }

        // Logging untuk membantu debug
        Log::info('Google Calendar Status (Dosen):', [
            'has_tokens' => $dosen->hasGoogleCalendarConnected(),
            'is_expired' => $dosen->isGoogleTokenExpired(),
            'token_created' => $dosen->google_token_created_at,
            'expires_in' => $dosen->google_token_expires_in,
            'expiry_time' => $dosen->getTokenExpiryTime()?->format('Y-m-d H:i:s'),
            'has_access_token' => !empty($dosen->google_access_token),
            'has_refresh_token' => !empty($dosen->google_refresh_token),
            'is_connected' => $isConnected,
            'dosen_nip' => $dosen->nip
        ]);

        return view('bimbingan.dosen.masukkanjadwal', [
            'isConnected' => $isConnected,
            'email' => $dosen->email
        ]);
    }

    /**
     * Menyimpan jadwal baru
     */
    public function store(Request $request)
    {
        try {
            // Log semua data request di awal
            Log::info('Request data untuk jadwal baru:', $request->all());

            // Refresh token jika diperlukan
            if (!$this->googleCalendarController->validateAndRefreshToken()) {
                Log::error('Google Calendar authentication failed for dosen:', [
                    'nip' => Auth::guard('dosen')->user()->nip
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat terhubung ke Google Calendar. Silakan hubungkan ulang.'
                ], 401);
            }

            // Validasi request
            $validated = $request->validate([
                'start' => 'required|date',
                'end' => 'required|date|after:start',
                'description' => 'nullable|string',
                'has_kuota_limit' => 'boolean',
                'kuota' => 'nullable|numeric|min:1',
                'jenis_bimbingan' => 'nullable|string|in:skripsi,kp,akademik,konsultasi,mbkm,lainnya',
                'lokasi' => 'nullable|string|max:255',
            ]);

            // Validasi jenis bimbingan jika enableJenisBimbingan diaktifkan
            if ($request->enableJenisBimbingan && empty($request->jenis_bimbingan)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jenis bimbingan harus dipilih jika opsi "Tentukan Jenis Bimbingan" diaktifkan'
                ], 422);
            }

            // Parse dates with explicit timezone
            $start = Carbon::parse($request->start)->setTimezone('Asia/Jakarta');
            $end = Carbon::parse($request->end)->setTimezone('Asia/Jakarta');

            $dosen = Auth::guard('dosen')->user();

            // Perbaiki logika kapasitas
            $kapasitas = 0; // Default untuk kuota tidak terbatas
            if ($request->has_kuota_limit) {
                $kapasitas = intval($request->kuota ?? 1);
            }

            Log::info('Kapasitas yang akan digunakan:', [
                'has_kuota_limit' => $request->has_kuota_limit,
                'kuota_request' => $request->kuota,
                'kapasitas_final' => $kapasitas
            ]);

            // Secara eksplisit tentukan nilai jenis_bimbingan
            $jenisBimbingan = null;
            if ($request->enableJenisBimbingan === true || $request->enableJenisBimbingan === "true" || $request->enableJenisBimbingan === 1) {
                $jenisBimbingan = $request->jenis_bimbingan;
                Log::info("Checkbox enableJenisBimbingan aktif, menggunakan nilai:", ['jenis_bimbingan' => $jenisBimbingan]);
            } else {
                Log::info("Checkbox enableJenisBimbingan tidak aktif, jenis_bimbingan akan null");
            }

            // Tambahkan debug sebelum menyimpan
            Log::info('Nilai yang akan disimpan ke database:', [
                'enableJenisBimbingan' => $request->enableJenisBimbingan,
                'jenis_bimbingan_dari_request' => $request->jenis_bimbingan,
                'jenis_bimbingan_final' => $jenisBimbingan
            ]);

            // Buat event di Google Calendar
            $description = "Status: Tersedia\n" .
                "Dosen: {$dosen->nama}\n" .
                "NIP: {$dosen->nip}\n" .
                "Email: {$dosen->email}\n";

            // Tambahkan informasi kuota
            if ($request->has_kuota_limit) {
                $description .= "Kuota: Terbatas ({$kapasitas} mahasiswa)\n";
            } else {
                $description .= "Kuota: Tidak terbatas\n";
            }

            // Tambahkan informasi jenis bimbingan jika ada
            if ($jenisBimbingan) {
                $jenisBimbinganText = match ($jenisBimbingan) {
                    'skripsi' => 'Bimbingan Skripsi',
                    'kp' => 'Bimbingan KP',
                    'akademik' => 'Bimbingan Akademik',
                    'konsultasi' => 'Konsultasi Pribadi',
                    'mbkm' => 'Bimbingan MBKM',
                    'lainnya' => 'Lainnya',
                    default => 'Bimbingan'
                };
                $description .= "Jenis: {$jenisBimbinganText}\n";
            }

            // Tambahkan lokasi jika ada
            if ($request->lokasi) {
                $description .= "Lokasi: {$request->lokasi}\n";
            }

            // Tambahkan catatan jika ada
            if ($request->description) {
                $description .= "Catatan: {$request->description}";
            }

            $eventData = [
                'summary' => 'Jadwal Bimbingan',
                'description' => $description,
                'start' => $start,
                'end' => $end,
                'reminders' => [
                    'useDefault' => false,
                    'overrides' => [
                        ['method' => 'email', 'minutes' => 24 * 60],
                        ['method' => 'popup', 'minutes' => 30],
                    ],
                ],
            ];

            DB::beginTransaction();
            try {
                // Buat event di Google Calendar
                $createdEvent = $this->googleCalendarController->createEvent($eventData);

                // Simpan ke database dengan pendekatan yang berbeda
                $jadwal = new JadwalBimbingan();
                $jadwal->event_id = $createdEvent->id;
                $jadwal->nip = $dosen->nip;
                $jadwal->waktu_mulai = $start;
                $jadwal->waktu_selesai = $end;
                $jadwal->catatan = $request->description;
                $jadwal->status = JadwalBimbingan::STATUS_TERSEDIA;
                $jadwal->kapasitas = $kapasitas;
                $jadwal->sisa_kapasitas = $kapasitas;
                $jadwal->lokasi = $request->lokasi;
                $jadwal->jenis_bimbingan = $jenisBimbingan; // Pastikan ini terisi dengan benar
                $jadwal->has_kuota_limit = $request->has_kuota_limit;
                $jadwal->jumlah_pendaftar = 0;
                $jadwal->save();

                // Log setelah menyimpan untuk memastikan
                Log::info('Jadwal berhasil disimpan:', [
                    'id' => $jadwal->id,
                    'jenis_bimbingan_tersimpan' => $jadwal->jenis_bimbingan,
                    'event_id' => $jadwal->event_id
                ]);

                $verifyJadwal = JadwalBimbingan::find($jadwal->id);
                Log::info('Verifikasi data tersimpan:', [
                    'jenis_bimbingan' => $verifyJadwal->jenis_bimbingan
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Jadwal berhasil ditambahkan!',
                    'data' => [
                        'id' => $createdEvent->id,
                        'title' => 'Jadwal Bimbingan',
                        'start' => $start->toIso8601String(),
                        'end' => $end->toIso8601String(),
                        'description' => $request->description,
                        'has_kuota_limit' => $request->has_kuota_limit,
                        'jenis_bimbingan' => $jenisBimbingan,
                        'lokasi' => $request->lokasi,
                        'status' => 'Tersedia',
                    ]
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('Error adding event: ' . $e->getMessage(), [
                'dosen_nip' => Auth::guard('dosen')->user()->nip,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan jadwal: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Menghapus jadwal
     */
    public function destroy($eventId)
    {
        try {
            // Refresh token jika diperlukan
            if (!$this->googleCalendarController->validateAndRefreshToken()) {
                Log::error('Google Calendar authentication failed for dosen on delete:', [
                    'nip' => Auth::guard('dosen')->user()->nip,
                    'event_id' => $eventId
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat terhubung ke Google Calendar. Silakan hubungkan ulang.'
                ], 401);
            }

            $jadwal = JadwalBimbingan::where('event_id', $eventId)->first();
            if (!$jadwal) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jadwal tidak ditemukan dalam sistem'
                ], 404);
            }

            // Cek apakah jadwal sudah lewat
            $now = Carbon::now('Asia/Jakarta');
            $jadwalWaktu = Carbon::parse($jadwal->waktu_mulai)->setTimezone('Asia/Jakarta');

            if ($jadwalWaktu->isPast()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jadwal tidak dapat dihapus karena waktu sudah lewat'
                ], 400);
            }

            // Cek apakah jadwal sudah digunakan (ada usulan bimbingan)
            $usulanCount = DB::table('usulan_bimbingans')
                ->where('event_id', $eventId)
                ->whereIn('status', ['USULAN', 'DITERIMA', 'DISETUJUI'])
                ->count();

            if ($usulanCount > 0) {
                // Ada usulan bimbingan, jadwal tidak bisa dihapus
                $usulanInfo = DB::table('usulan_bimbingans')
                    ->where('event_id', $eventId)
                    ->select(
                        DB::raw('COUNT(*) as total_usulan'),
                        DB::raw('SUM(CASE WHEN status = "DISETUJUI" THEN 1 ELSE 0 END) as disetujui'),
                        DB::raw('SUM(CASE WHEN status = "USULAN" THEN 1 ELSE 0 END) as menunggu'),
                        DB::raw('SUM(CASE WHEN status = "SELESAI" THEN 1 ELSE 0 END) as selesai')
                    )
                    ->first();

                $message = 'Jadwal tidak dapat dihapus karena sudah terdapat ';
                $details = [];

                if ($usulanInfo->disetujui > 0) {
                    $details[] = $usulanInfo->disetujui . ' usulan yang sudah disetujui';
                }
                if ($usulanInfo->menunggu > 0) {
                    $details[] = $usulanInfo->menunggu . ' usulan yang menunggu persetujuan';
                }
                if ($usulanInfo->selesai > 0) {
                    $details[] = $usulanInfo->selesai . ' bimbingan yang sudah selesai';
                }

                $message .= implode(', ', $details);
                $message .= '.';

                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 400);
            }

            DB::beginTransaction();
            try {
                // Coba hapus dari Google Calendar
                try {
                    $this->googleCalendarController->deleteEvent($eventId);
                    Log::info('Jadwal berhasil dihapus dari Google Calendar:', [
                        'event_id' => $eventId
                    ]);
                } catch (\Google_Service_Exception $e) {
                    // Jika error 404 (event tidak ditemukan), lanjutkan
                    if ($e->getCode() == 404) {
                        Log::warning('Event tidak ditemukan di Google Calendar, melanjutkan penghapusan dari database:', [
                            'event_id' => $eventId
                        ]);
                    } else {
                        // Untuk error lain, throw exception
                        throw $e;
                    }
                }

                // Update status jadwal menjadi 'dibatalkan' jika ingin menyimpan history
                // $jadwal->update(['status' => 'dibatalkan']);

                // Atau hapus dari database jika tidak perlu history
                $jadwal->delete();

                DB::commit();

                Log::info('Successfully deleted schedule:', [
                    'event_id' => $eventId,
                    'dosen_nip' => Auth::guard('dosen')->user()->nip
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Jadwal berhasil dihapus dari sistem!'
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('Error deleting schedule: ' . $e->getMessage(), [
                'event_id' => $eventId,
                'dosen_nip' => Auth::guard('dosen')->user()->nip,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem saat menghapus jadwal. Silakan coba lagi.'
            ], 500);
        }
    }
    // Di MasukkanJadwalController
    public function debugStore(Request $request)
    {
        return [
            'received_data' => $request->all(),
            'jenis_bimbingan' => $request->jenis_bimbingan,
            'has_kuota_limit' => $request->has_kuota_limit
        ];
    }

    // Tambahkan method untuk mendapatkan events dengan status dinamis
    public function getEvents()
    {
        try {
            // Update status semua jadwal terlebih dahulu
            $jadwals = JadwalBimbingan::where('nip', auth()->user()->nip)
            ->where(function($query) {
                // Hanya tampilkan jadwal yang belum selesai atau di masa depan
                $query->where('waktu_selesai', '>=', Carbon::now('Asia/Jakarta'))
                      ->orWhere('status', '!=', JadwalBimbingan::STATUS_SELESAI);
            })
            ->get();
            foreach ($jadwals as $jadwal) {
                // Hitung jumlah pendaftar dari usulan yang aktif
                $pendaftarCount = DB::table('usulan_bimbingans')
                    ->where('event_id', $jadwal->event_id)
                    ->whereIn('status', ['USULAN', 'DITERIMA', 'DISETUJUI'])
                    ->count();
    
                // Update jumlah pendaftar
                if ($jadwal->jumlah_pendaftar != $pendaftarCount) {
                    $jadwal->jumlah_pendaftar = $pendaftarCount;
    
                    // Update status berdasarkan pendaftar dan kapasitas
                    if ($jadwal->has_kuota_limit && $pendaftarCount >= $jadwal->kapasitas) {
                        $jadwal->status = JadwalBimbingan::STATUS_PENUH;
                    } elseif (Carbon::parse($jadwal->waktu_selesai)->isPast()) {
                        $jadwal->status = JadwalBimbingan::STATUS_SELESAI;
                    } elseif ($jadwal->status !== JadwalBimbingan::STATUS_DIBATALKAN) {
                        $jadwal->status = JadwalBimbingan::STATUS_TERSEDIA;
                    }
    
                    $jadwal->save();
                }
            }

            // Format events untuk calendar seperti sebelumnya
            $formattedEvents = $jadwals->map(function ($jadwal) {
                return [
                    'id' => $jadwal->id,
                    'title' => 'Bimbingan - ' . ($jadwal->jenis_bimbingan ? ucfirst($jadwal->jenis_bimbingan) : 'Umum'),
                    'start' => $jadwal->waktu_mulai,
                    'end' => $jadwal->waktu_selesai,
                    'extendedProps' => [
                        'has_kuota_limit' => $jadwal->has_kuota_limit,
                        'kuota' => $jadwal->kapasitas,
                        'jumlah_pendaftar' => $jadwal->jumlah_pendaftar,
                        'jenis_bimbingan' => $jadwal->jenis_bimbingan,
                        'status' => $jadwal->status,
                        'description' => $this->generateDescription($jadwal)
                    ]
                ];
            });
    
            // DEBUG: Tambahkan log untuk melihat events yang akan dikirim ke frontend
            Log::info('Events yang dikirim ke frontend:', [
                'count' => $formattedEvents->count(),
                'events' => $formattedEvents->toArray()
            ]);
    
            return response()->json($formattedEvents);
        } catch (\Exception $e) {
            Log::error('Error fetching events: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function generateDescription($jadwal)
    {
        // Hitung jumlah pendaftar aktif
        $pendaftarCount = DB::table('usulan_bimbingans')
            ->where('event_id', $jadwal->event_id)
            ->whereIn('status', ['USULAN', 'DITERIMA', 'DISETUJUI'])
            ->count();

        // Update jumlah pendaftar dan status jadwal
        if ($jadwal->jumlah_pendaftar != $pendaftarCount) {
            $jadwal->jumlah_pendaftar = $pendaftarCount;
            if ($jadwal->has_kuota_limit && $pendaftarCount >= $jadwal->kapasitas) {
                $jadwal->status = JadwalBimbingan::STATUS_PENUH;
            }
            $jadwal->save();
        }

        // Tentukan status yang benar untuk ditampilkan
        $statusLabel = $jadwal->status_label;

        $description = "Status: " . $statusLabel . "\n";
        $description .= "Jenis Bimbingan: " . ($jadwal->jenis_bimbingan ? ucfirst($jadwal->jenis_bimbingan) : 'Umum') . "\n";

        if ($jadwal->has_kuota_limit) {
            $description .= "Kapasitas: {$pendaftarCount}/{$jadwal->kapasitas}\n";
        } else {
            $description .= "Kapasitas: Tidak Terbatas\n";
        }

        if ($jadwal->lokasi) {
            $description .= "Lokasi: {$jadwal->lokasi}\n";
        }

        if ($jadwal->catatan) {
            $description .= "Catatan: {$jadwal->catatan}\n";
        }

        return $description;
    }
}