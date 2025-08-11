<?php

namespace App\Http\Controllers;

use App\Models\Alumni;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\UsulanBimbingan;
use App\Models\JadwalBimbingan;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use Illuminate\Support\Facades\Validator;

class DosenController extends Controller
{
    protected $googleCalendarController;

    public function __construct(GoogleCalendarController $googleCalendarController)
    {
        $this->googleCalendarController = $googleCalendarController;
    }
    public function index(Request $request)
    {
        try {
            $activeTab = $request->query('tab', 'usulan');
            $perPage = $request->query('per_page', 10);
            $nip = Auth::user()->nip;
            $dosen = Auth::user();

            // Default values
            $usulan = collect();
            $jadwal = collect();
            $riwayat = collect();
            $dosenList = collect();
            $riwayatDosenList = collect();

            // Load data based on active tab
            switch ($activeTab) {
                case 'usulan':
                    $usulan = DB::table('usulan_bimbingans as ub')
        ->join('mahasiswas as m', 'ub.nim', '=', 'm.nim')
        ->join('jadwal_bimbingans as jb', function ($join) {
            $join->on('ub.event_id', '=', 'jb.event_id')
                ->on('ub.nip', '=', 'jb.nip');
        })
        ->select(
            'ub.*',
            'm.nama as mahasiswa_nama',
            'jb.lokasi as lokasi_default',
            DB::raw('(SELECT COUNT(*) FROM usulan_bimbingans 
                    WHERE event_id = ub.event_id 
                    AND status = "DISETUJUI") as total_antrian')
        )
        ->where('jb.nip', $nip)
        // DIHAPUS: ->where('jb.status', 'tersedia')
        ->where('ub.status', 'USULAN')
        ->orderBy('jb.waktu_mulai', 'asc')
        ->orderBy('ub.created_at', 'desc')
        ->paginate($perPage);
    break;

                case 'jadwal':
                    $jadwal = DB::table('usulan_bimbingans as ub')
                        ->join('mahasiswas as m', 'ub.nim', '=', 'm.nim')
                        ->where('ub.nip', $nip)
                        ->where('status', 'DISETUJUI')
                        ->select(
                            'ub.*',
                            'm.nama as mahasiswa_nama',
                            DB::raw('(SELECT COUNT(*) FROM usulan_bimbingans 
                                    WHERE event_id = ub.event_id 
                                    AND status = "DISETUJUI" 
                                    AND nomor_antrian <= ub.nomor_antrian) as posisi_antrian'),
                            DB::raw('(SELECT COUNT(*) FROM usulan_bimbingans 
                                    WHERE event_id = ub.event_id 
                                    AND status = "DISETUJUI") as total_antrian')
                        )
                        ->orderBy('ub.tanggal', 'desc')
                        ->orderBy('ub.waktu_mulai', 'asc')
                        ->paginate($perPage);
                    break;

                case 'riwayat':
                    $riwayat = DB::table('usulan_bimbingans as ub')
                        ->join('mahasiswas as m', 'ub.nim', '=', 'm.nim')
                        ->where('ub.nip', $nip)
                        ->whereIn('ub.status', ['SELESAI', 'DITOLAK', 'DIBATALKAN'])
                        ->select('ub.*', 'm.nama as mahasiswa_nama')
                        ->orderBy('ub.tanggal', 'desc')
                        ->orderBy('ub.waktu_mulai', 'desc')
                        ->paginate($perPage);
                    break;

                case 'pengelola':
                    // Tab baru untuk koordinator prodi
                    if ($dosen->isKoordinatorProdi()) {
                        $prodiId = $dosen->prodi_id;

                        // Daftar dosen dengan total bimbingan hari ini
                        $dosenList = DB::table('dosens')
                            ->leftJoin('usulan_bimbingans', function ($join) {
                                $join->on('dosens.nip', '=', 'usulan_bimbingans.nip')
                                    ->where('usulan_bimbingans.tanggal', '=', date('Y-m-d'))
                                    ->where('usulan_bimbingans.status', '=', 'DISETUJUI');
                            })
                            ->where('dosens.prodi_id', $prodiId)
                            ->select(
                                'dosens.nip',
                                'dosens.nama',
                                'dosens.nama_singkat',
                                DB::raw('COUNT(DISTINCT usulan_bimbingans.id) as total_bimbingan_hari_ini')
                            )
                            ->groupBy('dosens.nip', 'dosens.nama', 'dosens.nama_singkat')
                            ->paginate($perPage);

                        // Riwayat bimbingan semua dosen
                        $riwayatDosenList = DB::table('dosens')
                            ->leftJoin('usulan_bimbingans', 'dosens.nip', '=', 'usulan_bimbingans.nip')
                            ->where('dosens.prodi_id', $prodiId)
                            ->select(
                                'dosens.nip',
                                'dosens.nama',
                                'dosens.nama_singkat',
                                DB::raw('COUNT(DISTINCT usulan_bimbingans.id) as total_bimbingan')
                            )
                            ->groupBy('dosens.nip', 'dosens.nama', 'dosens.nama_singkat')
                            ->paginate($perPage);
                    }
                    break;
            }

            return view('bimbingan.dosen.persetujuan', compact(
                'activeTab',
                'usulan',
                'jadwal',
                'riwayat',
                'dosenList',
                'riwayatDosenList'
            ));
        } catch (\Exception $e) {
            Log::error('Error in dosen index: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data');
        }
    }

    public function getDetailBimbingan($id)
    {
        try {
            $usulan = DB::table('usulan_bimbingans as ub')
                ->join('mahasiswas as m', 'ub.nim', '=', 'm.nim')
                ->join('prodi as p', 'm.prodi_id', '=', 'p.id')
                ->join('konsentrasi as k', 'm.konsentrasi_id', '=', 'k.id')
                ->join('dosens as d', 'ub.nip', '=', 'd.nip')
                ->select(
                    'ub.*',
                    'm.nama as mahasiswa_nama',
                    'p.nama_prodi',
                    'k.nama_konsentrasi',
                    'd.nama as dosen_nama'
                )
                ->where('ub.id', $id)
                ->firstOrFail();

            // Format tanggal ke format Indonesia
            $tanggal = Carbon::parse($usulan->tanggal)->locale('id')->isoFormat('dddd, D MMMM Y');
            $waktuMulai = Carbon::parse($usulan->waktu_mulai)->format('H.i');
            $waktuSelesai = Carbon::parse($usulan->waktu_selesai)->format('H.i');

            // Set warna badge status
            switch ($usulan->status) {
                case 'DISETUJUI':
                    $statusBadgeClass = 'bg-success';
                    break;
                case 'DITOLAK':
                    $statusBadgeClass = 'bg-danger';
                    break;
                case 'USULAN':
                    $statusBadgeClass = 'bg-warning';
                    break;
                case 'SELESAI':
                    $statusBadgeClass = 'bg-primary';
                    break;
                case 'DIBATALKAN':
                    $statusBadgeClass = 'bg-secondary';
                    break;
                default:
                    $statusBadgeClass = '';
                    break;
            }
            return view('bimbingan.aksiInformasi', compact(
                'usulan',
                'tanggal',
                'waktuMulai',
                'waktuSelesai',
                'statusBadgeClass'
            ));
        } catch (\Exception $e) {
            Log::error('Error di getDetailBimbingan: ' . $e->getMessage());
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat mengambil data usulan bimbingan');
        }
    }

    public function getRiwayatDetail($id)
    {
        try {
            $riwayat = DB::table('usulan_bimbingans as ub')
                ->join('mahasiswas as m', 'ub.nim', '=', 'm.nim')
                ->where('ub.id', $id)
                ->where('ub.status', 'SELESAI')
                ->select('ub.*', 'm.nama as mahasiswa_nama')
                ->firstOrFail();

            $tanggal = Carbon::parse($riwayat->tanggal)->locale('id')->isoFormat('dddd, D MMMM Y');
            $waktuMulai = Carbon::parse($riwayat->waktu_mulai)->format('H:i');
            $waktuSelesai = Carbon::parse($riwayat->waktu_selesai)->format('H:i');

            return view('bimbingan.riwayatdosen', compact(
                'riwayat',
                'tanggal',
                'waktuMulai',
                'waktuSelesai'
            ));
        } catch (\Exception $e) {
            Log::error('Error getting riwayat detail: ' . $e->getMessage());
            return back()->with('error', 'Gagal memuat detail riwayat bimbingan');
        }
    }

    public function editUsulan($id)
    {
        try {
            $usulan = DB::table('usulan_bimbingans as ub')
                ->join('mahasiswas as m', 'ub.nim', '=', 'm.nim')
                ->where('ub.id', $id)
                ->where('ub.status', 'DISETUJUI')
                ->select('ub.*', 'm.nama as mahasiswa_nama')
                ->firstOrFail();

            return view('bimbingan.dosen.editusulan', compact('usulan'));
        } catch (\Exception $e) {
            Log::error('Error in editUsulan: ' . $e->getMessage());
            return back()->with('error', 'Gagal memuat data usulan untuk diedit');
        }
    }

    public function updateUsulan(Request $request, $id)
    {
        try {
            $request->validate([
                'tanggal' => 'required|date',
                'waktu_mulai' => 'required|date_format:H:i',
                'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
                'lokasi' => 'required|string|max:255'
            ]);

            DB::table('usulan_bimbingans')
                ->where('id', $id)
                ->update([
                    'tanggal' => $request->tanggal,
                    'waktu_mulai' => $request->waktu_mulai,
                    'waktu_selesai' => $request->waktu_selesai,
                    'lokasi' => $request->lokasi,
                    'updated_at' => now()
                ]);

            return redirect()
                ->route('dosen.persetujuanbimbingan', ['tab' => 'usulan'])
                ->with('success', 'Usulan bimbingan berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('Error in updateUsulan: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui usulan bimbingan');
        }
    }

    public function terima(Request $request, $id)
    {
        try {
            $usulan = UsulanBimbingan::with('mahasiswa')->findOrFail($id);

            $jadwal = JadwalBimbingan::where('event_id', $usulan->event_id)
                ->where('status', 'tersedia')
                ->first();

            if (!$jadwal) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jadwal bimbingan sudah tidak tersedia'
                ], 400);
            }

            DB::beginTransaction();

            if ($usulan->setujui($request->lokasi)) {
                try {
                    // Debug log untuk memeriksa event_id
                    Log::info('Mencari event dengan ID: ' . $usulan->event_id);

                    // Cari event di calendar dosen
                    $events = $this->googleCalendarController->getEvents();

                    // Debug log untuk melihat response events
                    Log::info('Events response:', ['events' => $events]);

                    if (!$events || !isset($events->original)) {
                        throw new \Exception('Tidak bisa mengambil events dari Google Calendar');
                    }

                    $event = collect($events->original)->first(function ($event) use ($usulan) {
                        return isset($event['id']) && $event['id'] === $usulan->event_id;
                    });

                    if ($event) {
                        $existingAttendees = $event['attendees'] ?? [];
                        Log::info('Existing attendees:', ['attendees' => $existingAttendees]);

                        $mahasiswaEmail = $usulan->mahasiswa->email;
                        $emailExists = collect($existingAttendees)->contains('email', $mahasiswaEmail);

                        if (!$emailExists) {
                            Log::info('Menambahkan attendee baru:', ['email' => $mahasiswaEmail]);

                            $existingAttendees[] = [
                                'email' => $mahasiswaEmail,
                                'responseStatus' => 'needsAction'
                            ];

                            $description = "Status: Disetujui\n" .
                                "Dosen: {$usulan->dosen->nama}\n" .
                                "Mahasiswa: {$usulan->mahasiswa->nama}\n" .
                                "NIM: {$usulan->nim}\n" .
                                "Nomor Antrian: {$usulan->nomor_antrian}\n" .
                                "Lokasi: {$request->lokasi}\n";

                            $this->googleCalendarController->updateEventAttendees(
                                $usulan->event_id,
                                $existingAttendees,
                                [
                                    'description' => $description,
                                    'sendUpdates' => 'all',
                                    'reminders' => [
                                        'useDefault' => false,
                                        'overrides' => [
                                            ['method' => 'email', 'minutes' => 24 * 60],
                                            ['method' => 'popup', 'minutes' => 30]
                                        ]
                                    ]
                                ]
                            );

                            Log::info('Berhasil menambahkan attendee dengan notifikasi');
                        }

                        DB::commit();
                        return response()->json([
                            'success' => true,
                            'message' => 'Usulan bimbingan berhasil disetujui dan undangan telah dikirim'
                        ]);
                    }

                    DB::commit();
                    return response()->json([
                        'success' => true,
                        'message' => 'Usulan bimbingan berhasil disetujui (tanpa notifikasi calendar)'
                    ]);
                } catch (\Exception $e) {
                    Log::error('Google Calendar Error Detail:', [
                        'message' => $e->getMessage(),
                        'event_id' => $usulan->event_id,
                        'trace' => $e->getTraceAsString()
                    ]);

                    DB::commit();
                    return response()->json([
                        'success' => true,
                        'message' => 'Usulan bimbingan berhasil disetujui (tanpa notifikasi calendar)'
                    ]);
                }
            }

            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyetujui usulan bimbingan'
            ], 500);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in approve consultation:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses usulan'
            ], 500);
        }
    }

    public function tolak(Request $request, $id)
    {
        try {
            $usulan = UsulanBimbingan::findOrFail($id);

            $usulan->update([
                'status' => 'DITOLAK',
                'keterangan' => $request->keterangan
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Usulan bimbingan berhasil ditolak'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses usulan'
            ], 500);
        }
    }
    public function selesaikan($id)
    {
        Log::info('Fungsi selesaikan dipanggil dengan ID: ' . $id);
        try {
            $usulan = UsulanBimbingan::findOrFail($id);

            if ($usulan->status !== 'DISETUJUI') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya bimbingan yang disetujui yang dapat diselesaikan'
                ], 422);
            }

            $usulan->update([
                'status' => 'SELESAI'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bimbingan berhasil diselesaikan'
            ]);
        } catch (\Exception $e) {
            Log::error('Error in selesaikan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyelesaikan bimbingan'
            ], 500);
        }
    }

    public function dosenDetail(Request $request, $nip)
    {
        try {
            $dosen = Dosen::where('nip', $nip)->firstOrFail();
            $perPage = $request->input('per_page', 10);

            // Ambil daftar bimbingan hari ini
            $bimbingan = DB::table('usulan_bimbingans as ub')
                ->join('mahasiswas as m', 'ub.nim', '=', 'm.nim')
                ->where('ub.nip', $nip)
                ->where('ub.tanggal', date('Y-m-d'))
                ->where('ub.status', 'DISETUJUI')
                ->select(
                    'ub.*',
                    'm.nama as mahasiswa_nama'
                )
                ->orderBy('ub.waktu_mulai')
                ->paginate($perPage);

            return view('bimbingan.dosen.detaildaftar', compact('dosen', 'bimbingan'));
        } catch (\Exception $e) {
            Log::error('Error in dosenDetail: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat detail dosen');
        }
    }

    public function riwayatDosenDetail(Request $request, $nip)
    {
        try {
            $dosen = Dosen::where('nip', $nip)->firstOrFail();
            $perPage = $request->input('per_page', 10);

            // Ambil semua riwayat bimbingan
            $bimbingan = DB::table('usulan_bimbingans as ub')
                ->join('mahasiswas as m', 'ub.nim', '=', 'm.nim')
                ->where('ub.nip', $nip)
                ->whereIn('ub.status', ['SELESAI', 'DISETUJUI', 'DIBATALKAN'])
                ->select(
                    'ub.*',
                    'm.nama as mahasiswa_nama'
                )
                ->orderBy('ub.tanggal', 'desc')
                ->orderBy('ub.waktu_mulai', 'desc')
                ->paginate($perPage);

            return view('bimbingan.dosen.riwayatdetail', compact('dosen', 'bimbingan'));
        } catch (\Exception $e) {
            Log::error('Error in riwayatDosenDetail: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat riwayat detail dosen');
        }
    }

    public function getRelatedSchedules($id)
    {
        try {
            DB::enableQueryLog();
            // Get the schedule to be canceled
            Log::info('getRelatedSchedules dipanggil dengan ID: ' . $id);
            $usulan = UsulanBimbingan::findOrFail($id);
            Log::info('Query parameters:', [
                'nip' => $usulan->nip,
                'tanggal' => $usulan->tanggal,
                'waktu_mulai' => $usulan->waktu_mulai,
                'waktu_selesai' => $usulan->waktu_selesai
            ]);


            // Query related schedules with proper scoping of conditions
            $relatedSchedules = DB::table('usulan_bimbingans as ub')
                ->leftJoin('mahasiswas as m', 'ub.nim', '=', 'm.nim')
                ->where('ub.id', '!=', $id)
                ->where('ub.nip', $usulan->nip)
                ->where('ub.tanggal', $usulan->tanggal)
                ->where('ub.status', 'DISETUJUI')
                ->where(function ($query) use ($usulan) {
                    // Either exact same time or overlapping time
                    $query->where(function ($q) use ($usulan) {
                        // Exact same time
                        $q->where('ub.waktu_mulai', $usulan->waktu_mulai)
                            ->where('ub.waktu_selesai', $usulan->waktu_selesai);
                    })
                        ->orWhere(function ($q) use ($usulan) {
                            // Start time overlaps
                            $q->where('ub.waktu_mulai', '>=', $usulan->waktu_mulai)
                                ->where('ub.waktu_mulai', '<', $usulan->waktu_selesai);
                        })
                        ->orWhere(function ($q) use ($usulan) {
                            // End time overlaps
                            $q->where('ub.waktu_selesai', '>', $usulan->waktu_mulai)
                                ->where('ub.waktu_selesai', '<=', $usulan->waktu_selesai);
                        })
                        ->orWhere(function ($q) use ($usulan) {
                            // Session completely encompasses current session
                            $q->where('ub.waktu_mulai', '<=', $usulan->waktu_mulai)
                                ->where('ub.waktu_selesai', '>=', $usulan->waktu_selesai);
                        });
                })
                ->select(
                    'ub.id',
                    'ub.nim',
                    'm.nama as mahasiswa_nama',
                    'ub.jenis_bimbingan',
                    'ub.waktu_mulai',
                    'ub.waktu_selesai'
                )
                ->get();


            Log::info('Related schedules count: ' . $relatedSchedules->count());
            // You can also log the actual SQL query for debugging
            $query = DB::getQueryLog();
            Log::info('Last executed query:', end($query) ?: ['No query logged']);


            return response()->json([
                'success' => true,
                'schedules' => $relatedSchedules
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal mendapatkan jadwal terkait: ' . $e->getMessage(), [
                'id' => $id,
                'exception' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendapatkan jadwal terkait: ' . $e->getMessage()
            ], 500);
        }
    }

    public function batalkanPersetujuan($id, Request $request)
    {
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'alasan' => 'required|string',
                'related_schedules' => 'nullable|array',
                'related_schedules.*' => 'integer'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal: ' . implode(', ', $validator->errors()->all())
                ], 422);
            }

            DB::beginTransaction();

            // Cari data bimbingan utama
            $bimbingan = UsulanBimbingan::findOrFail($id);

            // Pastikan status saat ini adalah DISETUJUI
            if ($bimbingan->status !== 'DISETUJUI') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya bimbingan yang telah disetujui yang dapat dibatalkan'
                ], 400);
            }

            // Update status dan tambahkan alasan untuk bimbingan utama
            $bimbingan->status = 'DIBATALKAN';
            $bimbingan->keterangan = $request->alasan;
            $bimbingan->updated_at = now();
            $bimbingan->save();

            // Hitung jumlah total pembatalan (mulai dari 1 untuk bimbingan utama)
            $totalBatalkan = 1;

            // Jika ada jadwal terkait yang dipilih, batalkan juga
            if ($request->filled('related_schedules') && count($request->related_schedules) > 0) {
                $relatedIds = $request->related_schedules;

                // Batch update untuk jadwal terkait
                $updated = UsulanBimbingan::whereIn('id', $relatedIds)
                    ->where('nip', $bimbingan->nip) // Pastikan hanya jadwal dosen yang sama
                    ->where('status', 'DISETUJUI') // Pastikan hanya yang statusnya DISETUJUI
                    ->update([
                        'status' => 'DIBATALKAN',
                        'keterangan' => $request->alasan,
                        'updated_at' => now()
                    ]);

                $totalBatalkan += $updated;

                // Log pembatalan massal
                Log::info('Pembatalan bimbingan massal:', [
                    'id_utama' => $id,
                    'jadwal_terkait' => $relatedIds,
                    'dosen' => Auth::user()->nip,
                    'alasan' => $request->alasan,
                    'total_dibatalkan' => $totalBatalkan
                ]);

                // Implementasi notifikasi ke mahasiswa dapat ditambahkan di sini
                // ...
            } else {
                // Log pembatalan tunggal
                Log::info('Persetujuan bimbingan dibatalkan:', [
                    'id' => $id,
                    'dosen' => Auth::user()->nip,
                    'alasan' => $request->alasan
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $totalBatalkan > 1
                    ? "Berhasil membatalkan $totalBatalkan jadwal bimbingan"
                    : "Persetujuan bimbingan berhasil dibatalkan"
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat membatalkan persetujuan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function get_dosen_alumni_view()
    {
        // Get query parameters
        $search = request()->get('search', '');
        $sort = request()->get('sort', 'created_at');
        $order = request()->get('order', 'desc');
        $perPage = request()->get('per_page', 10);

        // Query for all alumni with search and sort
        $query = Alumni::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', '%'.$search.'%')
                ->orWhere('email', 'like', '%'.$search.'%')
                ->orWhere('tahun_lulus', 'like', '%'.$search.'%');
            });
        }

        // Validate and apply sorting
        $validSorts = ['nama', 'email', 'tahun_lulus', 'created_at'];
        $sort = in_array($sort, $validSorts) ? $sort : 'created_at';
        $order = in_array(strtolower($order), ['asc', 'desc']) ? $order : 'desc';

        $allAlumni = $query->orderBy($sort, $order)
                        ->paginate($perPage)
                        ->appends(request()->query());

        // Check if page exists, if not redirect to first page
        if ($allAlumni->currentPage() > $allAlumni->lastPage()) {
            return redirect(request()->fullUrlWithQuery(['page' => 1]));
        }

        $provinces = json_decode(file_get_contents(resource_path('data/provinces.json')), true);
        $regencies = json_decode(file_get_contents(resource_path('data/regencies.json')), true);

        $currentTab = request()->get('tab');
        $currentStep = request()->get('step');

        // Data untuk performa alumni (hanya diambil jika tab=performa-alumni)
        $pekerjaanUtama = [];
        $masaTunggu = [];
        $totalAlumni = 0;
        $jenisPerusahaan = [];
        $totalJenisPerusahaan = 0;
        $tingkatTempatKerja = [];
        $totalTingkatTempatKerja = 0;
        $penghasilanAlumni = [];
        $totalAlumniBekerja = 0;
        $jabatanAlumni = [];
        $totalAlumniWiraswasta = 0;
        $sumberPembiayaan = [];
        $totalSumberPembiayaan = 0;
        $hubunganStudiPekerjaan = [];
        $totalHubunganStudiPekerjaan = 0;
        $pendidikanSesuaiPekerjaan = [];
        $totalPendidikanSesuaiPekerjaan = 0;
        $metodePembelajaran = [];
        $kompetensiAlumni = [];
        $kompetensiRadar = [];

        if(request()->get('tab') === 'performa-alumni') {
            // Data pekerjaan utama
            $statusCounts = Alumni::selectRaw('status_saat_ini, COUNT(*) as count')
                                ->groupBy('status_saat_ini')
                                ->pluck('count', 'status_saat_ini')
                                ->toArray();

            $totalAlumni = array_sum($statusCounts);
            
            $pekerjaanUtama = [
                'Bekerja (full time/part time)' => ($statusCounts[1] ?? 0) / max($totalAlumni, 1) * 100,
                'Wiraswasta' => ($statusCounts[2] ?? 0) / max($totalAlumni, 1) * 100,
                'Melanjutkan Pendidikan' => ($statusCounts[3] ?? 0) / max($totalAlumni, 1) * 100,
                'Tidak Kerja tetapi sedang mencari kerja' => ($statusCounts[4] ?? 0) / max($totalAlumni, 1) * 100,
                'Belum memungkinkan bekerja' => ($statusCounts[5] ?? 0) / max($totalAlumni, 1) * 100,
            ];

            // Data masa tunggu pekerjaan
            $masaTungguCounts = Alumni::selectRaw('bekerja_6_bulan_setelah_lulus, COUNT(*) as count')
                                    ->whereNotNull('bekerja_6_bulan_setelah_lulus')
                                    ->groupBy('bekerja_6_bulan_setelah_lulus')
                                    ->pluck('count', 'bekerja_6_bulan_setelah_lulus')
                                    ->toArray();

            $totalBekerja = array_sum($masaTungguCounts);
            
            $masaTunggu = [
                '≤ 6 bulan' => ($masaTungguCounts[1] ?? 0) / max($totalBekerja, 1) * 100,
                '> 6 bulan' => ($masaTungguCounts[0] ?? 0) / max($totalBekerja, 1) * 100,
            ];

            // Data jenis perusahaan
            $jenisPerusahaanCounts = Alumni::selectRaw('jenis_perusahaan, COUNT(*) as count')
                ->whereNotNull('jenis_perusahaan')
                ->groupBy('jenis_perusahaan')
                ->pluck('count', 'jenis_perusahaan')
                ->toArray();

            $totalJenisPerusahaan = array_sum($jenisPerusahaanCounts);

            $jenisPerusahaanLabels = [
                1 => 'Instansi pemerintah',
                2 => 'BUMN/BUMD',
                3 => 'Institusi/Organisasi Multilateral',
                4 => 'Organisasi non-profit/LSM',
                5 => 'Perusahaan swasta',
                6 => 'Wiraswasta/perusahaan sendiri',
                7 => 'Lainnya',
            ];

            foreach ($jenisPerusahaanLabels as $key => $label) {
                $jenisPerusahaan[$label] = ($jenisPerusahaanCounts[$key] ?? 0) / max($totalJenisPerusahaan, 1) * 100;
            }

            // Data tingkat tempat kerja
            $tingkatTempatKerjaCounts = Alumni::selectRaw('tingkat_tempat_kerja, COUNT(*) as count')
                ->whereNotNull('tingkat_tempat_kerja')
                ->groupBy('tingkat_tempat_kerja')
                ->pluck('count', 'tingkat_tempat_kerja')
                ->toArray();

            $totalTingkatTempatKerja = array_sum($tingkatTempatKerjaCounts);

            $tingkatTempatKerjaLabels = [
                'Lokal/wilayah/wiraswasta tidak berbadan hukum',
                'Nasional/wiraswasta berbadan hukum',
                'Multinasional/internasional',
            ];

            foreach ($tingkatTempatKerjaLabels as $label) {
                $tingkatTempatKerja[$label] = ($tingkatTempatKerjaCounts[$label] ?? 0) / max($totalTingkatTempatKerja, 1) * 100;
            }

            // Data penghasilan alumni
            $alumniBekerja = Alumni::where('status_saat_ini', 1)
                ->orWhere('status_saat_ini', 2) 
                ->whereNotNull('pendapatan_per_bulan')
                ->get();

            $totalAlumniBekerja = $alumniBekerja->count();

            $penghasilanAlumni = [
                '< 3 Juta' => ['jumlah' => 0, 'persentase' => 0],
                '3 Juta - 5 Juta' => ['jumlah' => 0, 'persentase' => 0],
                '> 5 Juta' => ['jumlah' => 0, 'persentase' => 0]
            ];
            
            if ($totalAlumniBekerja > 0) {
                foreach ($alumniBekerja as $alumni) {
                    $gaji = $alumni->pendapatan_per_bulan;
                    
                    if ($gaji < 3000000) {
                        $penghasilanAlumni['< 3 Juta']['jumlah']++;
                    } elseif ($gaji >= 3000000 && $gaji <= 5000000) {
                        $penghasilanAlumni['3 Juta - 5 Juta']['jumlah']++;
                    } else {
                        $penghasilanAlumni['> 5 Juta']['jumlah']++;
                    }
                }
            
                foreach ($penghasilanAlumni as $kategori => $data) {
                    $penghasilanAlumni[$kategori]['persentase'] = ($data['jumlah'] / $totalAlumniBekerja) * 100;
                }
            }

            // Label jabatan alumni
            $jabatanLabels = [
                'Founder' => 'Founder',
                'Co-Founder' => 'Co-Founder',
                'CEO/Direktur' => 'CEO/Direktur',
                'Manager' => 'Manager',
                'Supervisor' => 'Supervisor',
                'Staff' => 'Staff',
                'Owner' => 'Owner',
            ];

            // Hitung jumlah alumni wiraswasta per jabatan
            $jabatanCounts = Alumni::where('status_saat_ini', 2)
                ->whereNotNull('posisi_wirausaha')
                ->selectRaw("CASE 
                                WHEN posisi_wirausaha IN ('Founder', 'Co-Founder', 'CEO/Direktur', 'Manager', 'Supervisor', 'Staff', 'Owner') 
                                THEN posisi_wirausaha 
                            END as jabatan, COUNT(*) as count")
                ->groupBy('jabatan')
                ->pluck('count', 'jabatan')
                ->toArray();

            $totalJabatan = array_sum($jabatanCounts);

            // Hitung persentase
            $jabatanAlumni = [];
            foreach ($jabatanLabels as $key => $label) {
                $jabatanAlumni[$label] = ($jabatanCounts[$key] ?? 0) / max($totalJabatan, 1) * 100;
            }

            // Data sumber pembiayaan kuliah
            $sumberPembiayaanCounts = Alumni::selectRaw('sumber_pembiayaan_kuliah, COUNT(*) as count')
                ->whereNotNull('sumber_pembiayaan_kuliah')
                ->groupBy('sumber_pembiayaan_kuliah')
                ->pluck('count', 'sumber_pembiayaan_kuliah')
                ->toArray();

            $totalSumberPembiayaan = array_sum($sumberPembiayaanCounts);

            $sumberPembiayaanLabels = [
                1 => 'Biaya Sendiri / Keluarga',
                2 => 'Beasiswa ADIK',
                3 => 'Beasiswa BIDIKMISI',
                4 => 'Beasiswa PPA',
                5 => 'Beasiswa AFIRMASI',
                6 => 'Beasiswa Perusahaan/Swasta',
                7 => 'Lainnya',
            ];

            foreach ($sumberPembiayaanLabels as $key => $label) {
                $sumberPembiayaan[$label] = ($sumberPembiayaanCounts[$key] ?? 0) / max($totalSumberPembiayaan, 1) * 100;
            }

            // Data hubungan studi dan pekerjaan
            $hubunganStudiCounts = Alumni::selectRaw('hubungan_studi_pekerjaan, COUNT(*) as count')
                ->whereNotNull('hubungan_studi_pekerjaan')
                ->groupBy('hubungan_studi_pekerjaan')
                ->pluck('count', 'hubungan_studi_pekerjaan')
                ->toArray();

            $totalHubunganStudiPekerjaan = array_sum($hubunganStudiCounts);

            $hubunganStudiLabels = [
                1 => 'Sangat Erat',
                2 => 'Erat',
                3 => 'Cukup Erat',
                4 => 'Kurang Erat',
                5 => 'Tidak Sama Sekali',
            ];

            foreach ($hubunganStudiLabels as $key => $label) {
                $hubunganStudiPekerjaan[$label] = ($hubunganStudiCounts[$key] ?? 0) / max($totalHubunganStudiPekerjaan, 1) * 100;
            }

            // Data dari kolom pendidikan_sesuai_pekerjaan
            $pendidikanSesuaiCounts = Alumni::selectRaw('pendidikan_sesuai_pekerjaan, COUNT(*) as count')
                ->whereNotNull('pendidikan_sesuai_pekerjaan')
                ->groupBy('pendidikan_sesuai_pekerjaan')
                ->pluck('count', 'pendidikan_sesuai_pekerjaan')
                ->toArray();

            $totalPendidikanSesuaiPekerjaan = array_sum($pendidikanSesuaiCounts);

            $pendidikanSesuaiLabels = [
                1 => 'Setingkat Lebih Tinggi',
                2 => 'Tingkat yang Sama',
                3 => 'Setingkat Lebih Rendah',
                4 => 'Tidak Perlu Pendidikan Tinggi',
            ];

            foreach ($pendidikanSesuaiLabels as $key => $label) {
                $pendidikanSesuaiPekerjaan[$label] = ($pendidikanSesuaiCounts[$key] ?? 0) / max($totalPendidikanSesuaiPekerjaan, 1) * 100;
            }

            // Data dari kolom penilaian
            $skalaPenilaian = [
                1 => 'Sangat Besar',
                2 => 'Besar',
                3 => 'Cukup Besar', 
                4 => 'Kurang',
                5 => 'Tidak Sama Sekali'
            ];

            // Daftar metode pembelajaran yang akan diambil datanya
            $daftarMetode = [
                'penekanan_perkuliahan' => 'Perkuliahan',
                'penekanan_demontrasi' => 'Demonstrasi',
                'penekanan_proyek_riset' => 'Partisipasi dalam proyek riset',
                'penekanan_magang' => 'Magang',
                'penekanan_praktikum' => 'Praktikum',
                'penekanan_kerja_lapangan' => 'Kerja Lapangan',
                'penekanan_diskusi' => 'Diskusi'
            ];

            // Inisialisasi array untuk menyimpan data
            $metodePembelajaran = [
                'labels' => array_values($daftarMetode),
                'datasets' => []
            ];

            // Untuk setiap skala penilaian, buat dataset
            foreach ($skalaPenilaian as $nilai => $label) {
                $dataset = [
                    'label' => $label,
                    'data' => [],
                ];

                foreach ($daftarMetode as $field => $namaMetode) {
                    $count = Alumni::where($field, $nilai)->count();
                    $dataset['data'][] = $count;
                }

                $metodePembelajaran['datasets'][] = $dataset;
            }

            $totalPerMetode = [];
            foreach ($daftarMetode as $field => $namaMetode) {
                $totalPerMetode[$field] = Alumni::whereNotNull($field)->count();
            }

            // Data dari kolom kompetensi
            $daftarKompetensi = [
                'etika' => 'Etika',
                'keahlian_bidang' => 'Keahlian berdasarkan bidang ilmu',
                'bahasa_inggris' => 'Bahasa Inggris',
                'ti' => 'Penggunaan Teknologi Informasi',
                'komunikasi' => 'Komunikasi',
                'kerjasama' => 'Kerja sama tim', 
                'pengembangan_diri' => 'Pengembangan Diri'
            ];

            // Hitung rata-rata untuk setiap kompetensi
            $kompetensiAlumni = [];
            foreach ($daftarKompetensi as $key => $label) {
                $avgLulus = Alumni::whereNotNull("kompetensi_{$key}_lulus")->avg("kompetensi_{$key}_lulus");
                $avgSaatIni = Alumni::whereNotNull("kompetensi_{$key}_saat_ini")->avg("kompetensi_{$key}_saat_ini");

                $kompetensiAlumni[] = [
                    'kompetensi' => $label,
                    'rata_lulus' => round($avgLulus, 2),
                    'rata_saat_ini' => round($avgSaatIni, 2),
                    'selisih' => round($avgSaatIni - $avgLulus, 2)
                ];
            }

            // Data untuk chart radar - gunakan semua kompetensi
            $kompetensiRadar = [
                'labels' => array_values($daftarKompetensi),
                'rata_lulus' => [],
                'rata_saat_ini' => []
            ];

            // Mengambil data untuk radar chart dengan urutan yang sama dengan labels
            foreach ($daftarKompetensi as $key => $label) {
                $avgLulus = Alumni::whereNotNull("kompetensi_{$key}_lulus")->avg("kompetensi_{$key}_lulus");
                $avgSaatIni = Alumni::whereNotNull("kompetensi_{$key}_saat_ini")->avg("kompetensi_{$key}_saat_ini");

                $kompetensiRadar['rata_lulus'][] = round($avgLulus, 2);
                $kompetensiRadar['rata_saat_ini'][] = round($avgSaatIni, 2);
            }
        }

        return view('alumni.kaprodi.alumni')->with([
            'allAlumni' => $allAlumni,
            'provinces' => $provinces,
            'regencies' => $regencies,
            'currentSort' => $sort,
            'currentOrder' => $order,
            'currentSearch' => $search,
            'currentPerPage' => $perPage,
            'currentTab' => $currentTab,
            'currentStep' => $currentStep,
            'pekerjaanUtama' => $pekerjaanUtama,
            'masaTunggu' => $masaTunggu,
            'totalAlumni' => $totalAlumni,
            'jenisPerusahaan' => $jenisPerusahaan,
            'totalJenisPerusahaan' => $totalJenisPerusahaan,
            'tingkatTempatKerja' => $tingkatTempatKerja,
            'totalTingkatTempatKerja' => $totalTingkatTempatKerja,
            'penghasilanAlumni' => $penghasilanAlumni,
            'totalAlumniBekerja' => $totalAlumniBekerja,
            'jabatanAlumni' => $jabatanAlumni,
            'totalAlumniWiraswasta' => $totalAlumniWiraswasta,
            'sumberPembiayaan' => $sumberPembiayaan,
            'totalSumberPembiayaan' => $totalSumberPembiayaan,
            'hubunganStudiPekerjaan' => $hubunganStudiPekerjaan,
            'totalHubunganStudiPekerjaan' => $totalHubunganStudiPekerjaan,
            'pendidikanSesuaiPekerjaan' => $pendidikanSesuaiPekerjaan,
            'totalPendidikanSesuaiPekerjaan' => $totalPendidikanSesuaiPekerjaan,
            'metodePembelajaran' => $metodePembelajaran,
            'kompetensiAlumni' => $kompetensiAlumni,
            'kompetensiRadar' => $kompetensiRadar,
        ]);
    }

    public function downloadData(Request $request)
    {
        $format = $request->input('format', 'xlsx');
        
        // Ambil semua data alumni dari database
        $allAlumni = Alumni::orderBy('nama', 'asc')->get();
        
        // Kolom yang akan dikecualikan
        $excludedColumns = ['id', 'created_at', 'updated_at'];
        
        // Ambil semua kolom dari tabel alumni
        $allColumns = [];
        if ($allAlumni->isNotEmpty()) {
            // Ambil kolom dari model alumni pertama
            $firstAlumni = $allAlumni->first();
            $allColumns = array_keys($firstAlumni->getAttributes());
            
            // Filter kolom yang tidak dikecualikan
            $allowedColumns = array_filter($allColumns, function($column) use ($excludedColumns) {
                return !in_array($column, $excludedColumns);
            });
        } else {
            // Jika tidak ada data, ambil kolom dari skema tabel
            $allowedColumns = \Schema::getColumnListing('alumni');
            $allowedColumns = array_filter($allowedColumns, function($column) use ($excludedColumns) {
                return !in_array($column, $excludedColumns);
            });
        }
        
        // Siapkan data untuk export
        $alumniData = [];
        
        // Header untuk file (gunakan nama kolom asli atau bisa disesuaikan)
        $headers = ['No']; // Mulai dengan nomor urut
        foreach ($allowedColumns as $column) {
            // Ubah nama kolom menjadi label yang lebih readable (opsional)
            $headers[] = ucwords(str_replace('_', ' ', $column));
        }
        $alumniData[] = $headers;
        
        // Data alumni
        foreach ($allAlumni as $index => $alumni) {
            $row = [$index + 1]; // Nomor urut
            
            foreach ($allowedColumns as $column) {
                $value = $alumni->{$column} ?? '-';
                
                // Format khusus untuk tipe tanggal jika diperlukan
                if ($value instanceof \Carbon\Carbon) {
                    $value = $value->format('d/m/Y H:i');
                } elseif (is_null($value) || $value === '') {
                    $value = '-';
                }
                
                $row[] = $value;
            }
            
            $alumniData[] = $row;
        }
        
        if ($format === 'csv') {
            return $this->downloadCSV($alumniData);
        } 
    }
    
    private function downloadCSV($data)
    {
        $filename = 'data-alumni-' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];
        
        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            foreach ($data as $row) {
                fputcsv($file, $row);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

}
