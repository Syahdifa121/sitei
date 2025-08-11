<?php

namespace App\Http\Controllers;

use App\Models\Pesan;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\PesanBalasan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PesanController extends Controller
{
    protected function getAuthenticatedUser()
    {
        if (Auth::guard('mahasiswa')->check()) {
            return ['user' => Auth::guard('mahasiswa')->user(), 'guard' => 'mahasiswa'];
        }
        return ['user' => Auth::guard('dosen')->user(), 'guard' => 'dosen'];
    }

    public function create()
    {
        $auth = $this->getAuthenticatedUser();
        if ($auth['guard'] === 'mahasiswa') {
            $data = Dosen::all();
        } else {
            $data = Mahasiswa::orderBy('nama', 'asc')->get();
        }
        return view('pesan.mahasiswa.buatpesan', [$auth['guard'] === 'mahasiswa' ? 'dosen' : 'mahasiswas' => $data]);
    }

    public function store(Request $request)
    {
        $auth = $this->getAuthenticatedUser();
        return $auth['guard'] === 'mahasiswa' ? 
            $this->storePesanMahasiswa($request) : 
            $this->storePesanDosen($request);
    }

    protected function storePesanMahasiswa(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'recipient' => 'required|exists:dosens,nip',
            'priority' => 'required|in:mendesak,umum',
            'message' => 'required|string',
            'attachment' => 'nullable|url'
        ]);

        try {
            $user = auth()->user();
            $pesanData = [
                'subjek' => $request->subject,
                'pesan' => $request->message,
                'prioritas' => $request->priority,
                'attachment' => $request->attachment,
                'status' => 'aktif',
                'last_reply_at' => now(),
                'mahasiswa_nim' => $user->nim,
                'dosen_nip' => $request->recipient,
                'last_reply_by' => 'mahasiswa'
            ];

            Pesan::create($pesanData);
            return response()->json(['success' => true, 'message' => 'Pesan berhasil dikirim']);
        } catch (\Exception $e) {
            Log::error('Error storing message: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menyimpan pesan'], 500);
        }
    }

    protected function storePesanDosen(Request $request)
    {
        try {
            $request->validate([
                'subject' => 'required|string|max:255',
                'selected_mahasiswa' => 'required|string',
                'priority' => 'required|in:mendesak,umum',
                'message' => 'required|string',
                'attachment' => 'nullable|string|url'
            ]);

            DB::beginTransaction();
            $dosen = Auth::guard('dosen')->user();
            
            if (!$dosen) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access'], 401);
            }

            $selectedNims = explode(',', $request->selected_mahasiswa);
            $success = $failed = 0;
            $pesanCreated = [];

            foreach ($selectedNims as $nim) {
                try {
                    $pesan = Pesan::create([
                        'subjek' => $request->subject,
                        'pesan' => $request->message,
                        'prioritas' => $request->priority,
                        'status' => 'aktif',
                        'attachment' => $request->attachment,
                        'last_reply_at' => now(),
                        'last_reply_by' => 'dosen',
                        'mahasiswa_nim' => $nim,
                        'dosen_nip' => $dosen->nip
                    ]);
                    $pesanCreated[] = $pesan;
                    $success++;
                } catch (\Exception $e) {
                    Log::error("Error creating message for NIM: $nim - " . $e->getMessage());
                    $failed++;
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => "Berhasil mengirim pesan ke $success mahasiswa, $failed gagal",
                'data' => compact('success', 'failed', 'pesanCreated')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error sending messages: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function indexMahasiswa()
    {
        try {
            $user = auth()->guard('mahasiswa')->user();
            $pesanList = Pesan::where('mahasiswa_nim', $user->nim)
                ->with(['dosen', 'balasan'])
                ->orderBy('created_at', 'desc')
                ->get();

            return view('pesan.mahasiswa.dashboardpesan', [
                'pesanAktif' => $pesanList->where('status', 'aktif'),
                'pesanSelesai' => $pesanList->where('status', 'selesai')
            ]);
        } catch (\Exception $e) {
            Log::error('Error in indexMahasiswa: ' . $e->getMessage());
            throw $e;
        }
    }

    public function indexDosen()
    {
        $user = auth()->user();
        $pesanList = Pesan::where('dosen_nip', $user->nip)
            ->with(['mahasiswa', 'balasan'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pesan.mahasiswa.dashboardpesan', [
            'pesanAktif' => $pesanList->where('status', 'aktif'),
            'pesanSelesai' => $pesanList->where('status', 'selesai')
        ]);
    }

    public function show($id)
    {
        try {
            $auth = $this->getAuthenticatedUser();
            $pesan = Pesan::with([
                'mahasiswa',
                'dosen',
                'balasan' => fn($q) => $q->orderBy('created_at', 'asc'),
                'balasan.pengirim'
            ])->findOrFail($id);

            $isAuthorized = $auth['guard'] === 'mahasiswa' ? 
                $pesan->mahasiswa_nim === $auth['user']->nim : 
                $pesan->dosen_nip === $auth['user']->nip;

            if (!$isAuthorized) {
                return redirect()
                    ->route('pesan.dashboardkonsultasi')
                    ->with('error', 'Anda tidak memiliki akses ke pesan ini');
            }

            return view('pesan.mahasiswa.isipesan', compact('pesan'));
        } catch (\Exception $e) {
            Log::error('Error showing pesan: ' . $e->getMessage());
            return redirect()
                ->route('pesan.dashboardkonsultasi')
                ->with('error', 'Terjadi kesalahan saat menampilkan pesan');
        }
    }

    public function storeReply(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            
            $request->validate([
                'pesan' => 'required|string',
                'attachment' => 'nullable|string|url'
            ]);

            $pesan = Pesan::findOrFail($id);
            if ($pesan->status !== 'aktif') {
                return response()->json(['success' => false, 'message' => 'Pesan sudah tidak aktif'], 403);
            }

            $auth = $this->getAuthenticatedUser();
            $isAuthorized = $auth['guard'] === 'mahasiswa' ? 
                $pesan->mahasiswa_nim === $auth['user']->nim : 
                $pesan->dosen_nip === $auth['user']->nip;

            if (!$isAuthorized) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk membalas pesan ini'
                ], 403);
            }

            $balasan = PesanBalasan::create([
                'pesan_id' => $id,
                'role_id' => $auth['user']->role_id,
                'pengirim_id' => $auth['guard'] === 'mahasiswa' ? $auth['user']->nim : $auth['user']->nip,
                'pesan' => $request->pesan,
                'attachment' => $request->attachment,
                'is_read' => false
            ]);

            $pesan->update(['last_reply_at' => now()]);
            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $balasan->load(['role', 'pengirim'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing reply: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }

    public function getMahasiswaByAngkatan(Request $request)
    {
        try {
            $angkatanString = $request->query('angkatan');
            if (!$angkatanString) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parameter angkatan tidak ditemukan'
                ], 400);
            }

            $mahasiswa = Mahasiswa::whereIn('angkatan', explode(',', $angkatanString))
                ->select('nim', 'nama', 'angkatan')
                ->orderBy('nama')
                ->get()
                ->map(fn($m) => [
                    'id' => $m->nim,
                    'name' => $m->nama . ' - Angkatan ' . $m->angkatan,
                    'nim' => $m->nim
                ]);

            return response()->json([
                'success' => !$mahasiswa->isEmpty(),
                'data' => $mahasiswa,
                'message' => $mahasiswa->isEmpty() ? 'Tidak ada mahasiswa untuk angkatan yang dipilih' : null
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getMahasiswaByAngkatan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data mahasiswa: ' . $e->getMessage()
            ], 500);
        }
    }
}