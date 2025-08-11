<?php

namespace App\Http\Controllers;

use App\Models\Alumni;
use App\Models\Mahasiswa;
use App\Models\Profil;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\UsulanBimbingan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MahasiswaController extends Controller
{
    public function index(Request $request)
    {
        try {
            $activeTab = $request->query('tab', 'usulan');
            $perPage = $request->query('per_page', 10);
            $nim = Auth::user()->nim;

            // Default values
            $usulan = collect();
            $daftarDosen = collect();
            $riwayat = collect();

            // Load data based on active tab
            switch ($activeTab) {
                case 'usulan':
                    $usulan = DB::table('usulan_bimbingans as ub')
                        ->join('mahasiswas as m', 'ub.nim', '=', 'm.nim')
                        ->where('ub.nim', $nim)
                        ->whereIn('ub.status', ['USULAN', 'DISETUJUI'])
                        ->select('ub.*', 'm.nama as mahasiswa_nama')
                        ->orderBy('ub.created_at', 'desc')
                        ->paginate($perPage);
                    break;

                case 'jadwal':
                    $daftarDosen = DB::table('dosens as d')
                        ->leftJoin('usulan_bimbingans as ub', function ($join) {
                            $join->on('d.nip', '=', 'ub.nip')
                                ->where('ub.status', 'DISETUJUI');
                        })
                        ->select(
                            'd.nip',
                            'd.nama_singkat',
                            'd.nama',
                            DB::raw('COUNT(ub.id) as total_bimbingan')
                        )
                        ->groupBy('d.nip', 'nama_singkat', 'd.nama')
                        ->orderBy('d.nama')
                        ->paginate($perPage);
                    break;

                case 'riwayat':
                    $riwayat = DB::table('usulan_bimbingans as ub')
                        ->join('mahasiswas as m', 'ub.nim', '=', 'm.nim')
                        ->join('dosens as d', 'ub.nip', '=', 'd.nip')
                        ->where('ub.nim', $nim)
                        ->whereIn('ub.status', ['SELESAI', 'DITOLAK', 'DIBATALKAN'])
                        ->select('ub.*', 'm.nama as mahasiswa_nama', 'd.nama as dosen_nama')
                        ->orderBy('ub.tanggal', 'desc')
                        ->paginate($perPage);
                    break;
            }

            return view('bimbingan.mahasiswa.usulanbimbingan', compact(
                'activeTab',
                'usulan',
                'daftarDosen',
                'riwayat'
            ));
        } catch (\Exception $e) {
            Log::error('Error in index: ' . $e->getMessage());
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
                ->select(
                    'ub.*',
                    'p.nama_prodi',
                    'k.nama_konsentrasi'
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
                    $statusBadgeClass = 'bg-info';
                    break;
                case 'SELESAI':
                    $statusBadgeClass = 'bg-primary';
                    break;
                case 'DIBATALKAN':  // Tambahkan kasus untuk DIBATALKAN
                    $statusBadgeClass = 'bg-secondary';  // Gunakan warna abu-abu untuk status dibatalkan
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

    public function getDetailDaftar($nip, Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);

            $dosen = DB::table('dosens')
                ->where('nip', $nip)
                ->firstOrFail();

            // Ambil detail bimbingan yang disetujui untuk dosen ini
            $bimbingan = DB::table('usulan_bimbingans as ub')
                ->join('mahasiswas as m', 'ub.nim', '=', 'm.nim')
                ->where('ub.nip', $nip)
                ->where('ub.status', 'DISETUJUI')
                ->select(
                    'ub.*',
                    'm.nama as mahasiswa_nama'
                )
                ->orderBy('ub.tanggal', 'desc')
                ->orderBy('ub.waktu_mulai', 'asc')
                ->paginate($perPage);

            return view('bimbingan.mahasiswa.detaildaftar', compact('dosen', 'bimbingan'));
        } catch (\Exception $e) {
            Log::error('Error getting detail dosen: ' . $e->getMessage());
            return back()->with('error', 'Gagal memuat detail dosen');
        }
    }

    public function getRiwayatDetail($id)
    {
        try {
            $riwayat = DB::table('usulan_bimbingans as ub')
                ->join('mahasiswas as m', 'ub.nim', '=', 'm.nim')
                ->join('dosens as d', 'ub.nip', '=', 'd.nip')
                ->where('ub.id', $id)
                ->where('ub.status', 'SELESAI')
                ->select('ub.*', 'm.nama as mahasiswa_nama', 'd.nama as dosen_nama')
                ->firstOrFail();

            $tanggal = Carbon::parse($riwayat->tanggal)->locale('id')->isoFormat('dddd, D MMMM Y');
            $waktuMulai = Carbon::parse($riwayat->waktu_mulai)->format('H:i');
            $waktuSelesai = Carbon::parse($riwayat->waktu_selesai)->format('H:i');

            return view('bimbingan.mahasiswa.riwayatDetail', compact(
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

    public function selesaiBimbingan(Request $request, $id)
    {
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
            Log::error('Error in selesaiBimbingan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyelesaikan bimbingan'
            ], 500);
        }
    }

    public function get_alumni_view()
    {
        $user = Auth::guard('mahasiswa')->user();
        $alumni = Alumni::where('user_nim', $user->nim)->first();
        $profil = Profil::where('user_nim', $user->nim)->first();

        $pengalaman = json_decode($profil->pengalaman ?? '[]', true);
        $pendidikan = json_decode($profil->pendidikan ?? '[]', true);
        $organisasi = json_decode($profil->organisasi ?? '[]', true);

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

        // Check form completion status
        $isStep1Complete = $alumni && $alumni->user_nim && $alumni->nama && $alumni->email && $alumni->no_telepon && $alumni->nomor_induk_kependudukan;
        $isStep2Complete = $alumni && $alumni->status_saat_ini && $alumni->hubungan_studi_pekerjaan && $alumni->pendidikan_sesuai_pekerjaan;
        $isStep3Complete = $alumni && $alumni->penekanan_perkuliahan && $alumni->penekanan_demontrasi && $alumni->penekanan_proyek_riset && $alumni->penekanan_magang && $alumni->penekanan_praktikum && $alumni->penekanan_kerja_lapangan && $alumni->penekanan_diskusi && $alumni->waktu_mulai_mencari_kerja
            && $alumni->jumlah_instansi_dilamar && $alumni->jumlah_instansi_merespons && $alumni->jumlah_instansi_wawancara && $alumni->situasi_saat_ini && $alumni->aktif_mencari_pekerjaan_4_minggu && $alumni->beasiswa_masa_kuliah && $alumni->saran_untuk_universitas;

        $isAllStepsComplete = $isStep1Complete && $isStep2Complete && $isStep3Complete;

        $currentTab = request()->get('tab');
        $currentStep = request()->get('step');

        // Redirect logic
        if ($isAllStepsComplete && $currentTab === 'form-alumni') {
            return redirect('/alumni?tab=profil');
        }

        if ($currentTab === 'form-alumni') {
            if ($isStep1Complete && $isStep2Complete && !$currentStep) {
                return redirect('/alumni?tab=form-alumni&step=3');
            }

            if ($isStep1Complete && !$currentStep) {
                return redirect('/alumni?tab=form-alumni&step=2');
            }
        }

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

        return view('alumni.mahasiswa.alumni')->with([
            'user' => $user,
            'alumni' => $alumni,
            'profil' => $profil,
            'allAlumni' => $allAlumni,
            'provinces' => $provinces,
            'regencies' => $regencies,
            'isStep1Complete' => $isStep1Complete,
            'isStep2Complete' => $isStep2Complete,
            'isStep3Complete' => $isStep3Complete,
            'isAllStepsComplete' => $isAllStepsComplete,
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
            'pengalaman' => $pengalaman,
            'pendidikan' => $pendidikan,
            'organisasi' => $organisasi,
        ]);
    }

    public function biodata_step_store(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_nim' => ['required', 'numeric'],
                'nama' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email'],
                'no_telepon' => ['required', 'numeric', 'digits_between:10,15'],
                'nomor_induk_kependudukan' => ['required', 'digits:16'],
                'nomor_pokok_wajib_pajak' => ['nullable', 'digits_between:15,16'],
                'tahun_lulus' => ['required', 'integer', 'digits:4'],
            ], [
                'user_nim.required' => 'NIM wajib diisi.',
                'user_nim.numeric' => 'NIM harus berupa angka.',
                'nama.required' => 'Nama lengkap wajib diisi.',
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Email tidak valid.',
                'no_telepon.required' => 'Nomor telepon wajib diisi.',
                'no_telepon.numeric' => 'Nomor telepon harus berupa angka.',
                'no_telepon.digits_between' => 'Nomor telepon harus antara 10 sampai 15 digit.',
                'nomor_induk_kependudukan.required' => 'NIK wajib diisi.',
                'nomor_induk_kependudukan.digits' => 'NIK harus 16 digit.',
                'nomor_pokok_wajib_pajak.digits_between' => 'NPWP harus antara 15 sampai 16 digit.',
                'tahun_lulus.required' => 'Tahun lulus wajib diisi.',
                'tahun_lulus.integer' => 'Tahun lulus harus berupa angka.',
                'tahun_lulus.digits' => 'Tahun lulus harus 4 digit.',
            ]);

            $alumni = Alumni::where('user_nim', $validated['user_nim'])->first();

            if (!$alumni) {
                $alumni = new Alumni();
            }

            $alumni->user_nim = $validated['user_nim'];
            $alumni->nama = $validated['nama'];
            $alumni->email = $validated['email'];
            $alumni->no_telepon = $validated['no_telepon'];
            $alumni->nomor_induk_kependudukan = $validated['nomor_induk_kependudukan'];
            $alumni->nomor_pokok_wajib_pajak = $validated['nomor_pokok_wajib_pajak'] ?? null;
            $alumni->kode_pt = '001017';
            $alumni->kode_prodi = '55202';
            $alumni->tahun_lulus = $validated['tahun_lulus'];
            $alumni->save();

            return redirect('/alumni?tab=form-alumni&step=2')->with('success', 'Berhasil menyimpan biodata.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors()->all();
            $hasRequiredError = false;
            $hasValidationError = false;

            // Cek jenis error
            foreach ($errors as $error) {
                if (str_contains($error, 'wajib diisi')) {
                    $hasRequiredError = true;
                } else {
                    $hasValidationError = true;
                }
            }

            // Tentukan pesan error
            $errorMessage = $hasRequiredError
                ? 'Lengkapi semua data yang diperlukan.'
                : 'Isi data sesuai aturan yang ditetapkan.';

            return redirect('/alumni?tab=form-alumni&step=1')
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', $errorMessage);
        }
    }

    public function kuisioner_wajib_step_store(Request $request)
    {
        try {
            $rules = [
                'status_saat_ini' => 'required|integer|between:1,5',
                'bekerja_6_bulan_setelah_lulus' => 'nullable|integer',
                'bulan_mendapat_pekerjaan_ya' => 'nullable|integer',
                'bulan_mendapat_pekerjaan_tidak' => 'nullable|integer',
                'pendapatan_per_bulan' => 'nullable|numeric',
                'lokasi_pekerjaan_provinsi' => 'nullable|string',
                'lokasi_pekerjaan_kabupaten' => 'nullable|string',
                'jenis_perusahaan' => 'nullable|integer',
                'jenis_perusahaan_lainnya' => 'nullable|string',
                'nama_perusahaan' => 'nullable|string',
                'tingkat_tempat_kerja' => 'nullable|string',
                'posisi_wirausaha' => 'nullable|string',
                'studi_lanjut_sumber_biaya' => 'nullable|string',
                'studi_lanjut_tanggal_masuk' => 'nullable|date',
                'studi_lanjut_kode_pt' => 'nullable|string',
                'studi_lanjut_program_studi' => 'nullable|string',
                'sumber_pembiayaan_kuliah' => 'required|integer',
                'sumber_pembiayaan_kuliah_lainnya' => 'nullable|string',
                'hubungan_studi_pekerjaan' => 'required|integer|between:1,5',
                'pendidikan_sesuai_pekerjaan' => 'required|integer|between:1,4',
            ];

            // Validasi jika user bekerja atau berwiraswasta
            $status = $request->input('status_saat_ini');

            if (in_array($status, ['1', '2'])) {
                // Wajib untuk semua yang bekerja atau wiraswasta
                $rules['bekerja_6_bulan_setelah_lulus'] = 'required|integer';
                $rules['lokasi_pekerjaan_kabupaten'] = 'required|string';
                $rules['lokasi_pekerjaan_provinsi'] = 'required|string';
                $rules['jenis_perusahaan'] = 'required|integer';
                $rules['nama_perusahaan'] = 'required|string';
                $rules['tingkat_tempat_kerja'] = 'required|string';

                // Tambahan khusus untuk wiraswasta
                if ($status === '2') {
                    $rules['posisi_wirausaha'] = 'required|string';
                }
            }

            // Tambahkan validasi bersyarat berdasarkan input `bekerja_6_bulan_setelah_lulus`
            if ($request->input('bekerja_6_bulan_setelah_lulus') === '1') {
                $rules['bulan_mendapat_pekerjaan_ya'] = 'required|integer|between:1,6';
                $rules['pendapatan_per_bulan'] = 'required|numeric';
            } elseif ($request->input('bekerja_6_bulan_setelah_lulus') === '0') {
                $rules['bulan_mendapat_pekerjaan_tidak'] = 'required|integer';
            }

            // Validasi jika jenis perusahaan adalah lainnya
            if ($request->input('jenis_perusahaan') === '7') {
                $rules['jenis_perusahaan_lainnya'] = 'required|string';
            }

            // Validasi jika sumber pembiayaan kuliah adalah lainnya
            if ($request->input('sumber_pembiayaan_kuliah') === '7') {
                $rules['sumber_pembiayaan_kuliah_lainnya'] = 'required|string';
            }

            // Aturan dan pesan untuk kompetensi
            $competencies = ['etika', 'keahlian_bidang', 'bahasa_inggris', 'ti', 'komunikasi', 'kerjasama', 'pengembangan_diri'];
            foreach ($competencies as $competency) {
                $rules["kompetensi_{$competency}_lulus"] = 'required|integer|between:1,5';
                $rules["kompetensi_{$competency}_saat_ini"] = 'required|integer|between:1,5';
            }

            $messages = [
                'status_saat_ini.required' => 'Status saat ini wajib diisi.',
                'status_saat_ini.integer' => 'Status saat ini harus berupa angka.',
                'status_saat_ini.between' => 'Status saat ini harus antara 1 sampai 5.',
                'bekerja_6_bulan_setelah_lulus.required' => 'Bekerja 6 bulan setelah lulus wajib diisi',
                'bekerja_6_bulan_setelah_lulus.integer' => 'Bekerja 6 bulan setelah lulus harus berupa angka.',
                'bulan_mendapat_pekerjaan_ya.required' => 'Bulan mendapat pekerjaan wajib diisi jika Anda memilih "Ya".',
                'bulan_mendapat_pekerjaan_ya.integer' => 'Bulan mendapat pekerjaan harus berupa angka.',
                'bulan_mendapat_pekerjaan_ya.between' => 'Bulan mendapat pekerjaan harus antara 1 sampai 6.',
                'bulan_mendapat_pekerjaan_tidak.required' => 'Bulan mendapat pekerjaan wajib diisi jika Anda memilih "Tidak".',
                'bulan_mendapat_pekerjaan_tidak.integer' => 'Bulan mendapat pekerjaan harus berupa angka.',
                'pendapatan_per_bulan.required' => 'Pendapatan per bulan wajib diisi jika Anda memilih "Ya".',
                'pendapatan_per_bulan.numeric' => 'Pendapatan per bulan harus berupa angka.',
                'lokasi_pekerjaan_provinsi.required' => 'Lokasi pekerjaan provinsi wajib diisi.',
                'lokasi_pekerjaan_provinsi.string' => 'Lokasi pekerjaan provinsi harus berupa teks.',
                'lokasi_pekerjaan_kabupaten.required' => 'Lokasi pekerjaan kabupaten wajib diisi.',
                'lokasi_pekerjaan_kabupaten.string' => 'Lokasi pekerjaan kabupaten harus berupa teks.',
                'jenis_perusahaan.required' => 'Jenis perusahaan wajib diisi.',
                'jenis_perusahaan.integer' => 'Jenis perusahaan harus berupa angka.',
                'jenis_perusahaan_lainnya.required' => 'Jenis perusahaan lainnya wajib diisi jika Anda memilih "Lainnya".',
                'jenis_perusahaan_lainnya.string' => 'Jenis perusahaan lainnya harus berupa teks.',
                'nama_perusahaan.required' => 'Nama perusahaan wajib diisi.',
                'nama_perusahaan.string' => 'Nama perusahaan harus berupa teks.',
                'tingkat_tempat_kerja.required' => 'Tingkat tempat kerja wajib diisi.',
                'tingkat_tempat_kerja.string' => 'Tingkat tempat kerja harus berupa teks.',
                'posisi_wirausaha.required' => 'Posisi wirausaha wajib diisi.',
                'posisi_wirausaha.string' => 'Posisi wirausaha harus berupa teks.',
                'studi_lanjut_sumber_biaya.string' => 'Sumber biaya studi lanjut harus berupa teks.',
                'studi_lanjut_tanggal_masuk.date' => 'Tanggal masuk studi lanjut harus berupa tanggal.',
                'studi_lanjut_kode_pt.string' => 'Kode PT studi lanjut harus berupa teks.',
                'studi_lanjut_program_studi.string' => 'Program studi studi lanjut harus berupa teks.',
                'sumber_pembiayaan_kuliah.required' => 'Sumber pembiayaan kuliah wajib diisi.',
                'sumber_pembiayaan_kuliah.integer' => 'Sumber pembiayaan kuliah harus berupa angka.',
                'sumber_pembiayaan_kuliah_lainnya.required' => 'Sumber pembiayaan kuliah lainnya wajib diisi jika Anda memilih "Lainnya".',
                'sumber_pembiayaan_kuliah_lainnya.string' => 'Sumber pembiayaan kuliah lainnya harus berupa teks.',
                'hubungan_studi_pekerjaan.required' => 'Hubungan studi dan pekerjaan wajib diisi.',
                'hubungan_studi_pekerjaan.integer' => 'Hubungan studi dan pekerjaan harus berupa angka.',
                'hubungan_studi_pekerjaan.between' => 'Hubungan studi dan pekerjaan harus antara 1 sampai 5.',
                'pendidikan_sesuai_pekerjaan.required' => 'Pendidikan sesuai pekerjaan wajib diisi.',
                'pendidikan_sesuai_pekerjaan.integer' => 'Pendidikan sesuai pekerjaan harus berupa angka.',
                'pendidikan_sesuai_pekerjaan.between' => 'Pendidikan sesuai pekerjaan harus antara 1 sampai 4.',
            ];

            // Tambahkan pesan untuk kompetensi
            foreach ($competencies as $competency) {
                $messages["kompetensi_{$competency}_lulus.required"] = "Kompetensi {$competency} saat lulus wajib diisi.";
                $messages["kompetensi_{$competency}_lulus.integer"] = "Kompetensi {$competency} saat lulus harus berupa angka.";
                $messages["kompetensi_{$competency}_lulus.between"] = "Kompetensi {$competency} saat lulus harus antara 1 sampai 5.";
                $messages["kompetensi_{$competency}_saat_ini.required"] = "Kompetensi {$competency} saat ini wajib diisi.";
                $messages["kompetensi_{$competency}_saat_ini.integer"] = "Kompetensi {$competency} saat ini harus berupa angka.";
                $messages["kompetensi_{$competency}_saat_ini.between"] = "Kompetensi {$competency} saat ini harus antara 1 sampai 5.";
            }

            $validated = $request->validate($rules, $messages);

            $user = Auth::guard('mahasiswa')->user();
            $alumni = Alumni::where('user_nim', $user->nim)->firstOrFail();

            $alumni->update([
                'status_saat_ini' => $validated['status_saat_ini'],
                'bekerja_6_bulan_setelah_lulus' => $validated['bekerja_6_bulan_setelah_lulus'] ?? null,
                'bulan_mendapat_pekerjaan' => $validated['bulan_mendapat_pekerjaan_ya'] ?? $validated['bulan_mendapat_pekerjaan_tidak'] ?? null,
                'pendapatan_per_bulan' => $validated['pendapatan_per_bulan'] ?? null,
                'lokasi_pekerjaan_provinsi' => $validated['lokasi_pekerjaan_provinsi'] ?? null,
                'lokasi_pekerjaan_kabupaten' => $validated['lokasi_pekerjaan_kabupaten'] ?? null,
                'jenis_perusahaan' => $validated['jenis_perusahaan'] ?? null,
                'jenis_perusahaan_lainnya' => $validated['jenis_perusahaan_lainnya'] ?? null,
                'nama_perusahaan' => $validated['nama_perusahaan'] ?? null,
                'tingkat_tempat_kerja' => $validated['tingkat_tempat_kerja'] ?? null,
                'posisi_wirausaha' => $validated['posisi_wirausaha'] ?? null,
                'studi_lanjut_sumber_biaya' => $validated['studi_lanjut_sumber_biaya'] ?? null,
                'studi_lanjut_tanggal_masuk' => $validated['studi_lanjut_tanggal_masuk'] ?? null,
                'studi_lanjut_kode_pt' => $validated['studi_lanjut_kode_pt'] ?? null,
                'studi_lanjut_program_studi' => $validated['studi_lanjut_program_studi'] ?? null,
                'sumber_pembiayaan_kuliah' => $validated['sumber_pembiayaan_kuliah'],
                'sumber_pembiayaan_kuliah_lainnya' => $validated['sumber_pembiayaan_kuliah_lainnya'] ?? null,
                'hubungan_studi_pekerjaan' => $validated['hubungan_studi_pekerjaan'],
                'pendidikan_sesuai_pekerjaan' => $validated['pendidikan_sesuai_pekerjaan'],
            ]);

            // Update kompetensi
            foreach ($competencies as $competency) {
                $alumni->update([
                    "kompetensi_{$competency}_lulus" => $validated["kompetensi_{$competency}_lulus"],
                    "kompetensi_{$competency}_saat_ini" => $validated["kompetensi_{$competency}_saat_ini"],
                ]);
            }

            return redirect('/alumni?tab=form-alumni&step=3')->with('success', 'Data kuisioner wajib berhasil disimpan.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors()->all();
            $hasRequiredError = false;
            $hasValidationError = false;

            // Cek jenis error
            foreach ($errors as $error) {
                if (str_contains($error, 'wajib diisi')) {
                    $hasRequiredError = true;
                } else {
                    $hasValidationError = true;
                }
            }

            // Tentukan pesan error berdasarkan jenis error yang ditemukan
            if ($hasRequiredError) {
                $errorMessage = 'Lengkapi semua data yang diperlukan.';
            } elseif ($hasValidationError) {
                $errorMessage = 'Isi data sesuai aturan yang ditetapkan.';
            } else {
                $errorMessage = 'Terjadi kesalahan dalam pengisian data.';
            }

            return redirect('/alumni?tab=form-alumni&step=2')
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', $errorMessage);
        }
    }

    public function kuisioner_lainnya_step_store(Request $request)
    {
        $rules = [
            'penekanan_perkuliahan' => 'required|integer|between:1,5',
            'penekanan_demontrasi' => 'required|integer|between:1,5',
            'penekanan_proyek_riset' => 'required|integer|between:1,5',
            'penekanan_magang' => 'required|integer|between:1,5',
            'penekanan_praktikum' => 'required|integer|between:1,5',
            'penekanan_kerja_lapangan' => 'required|integer|between:1,5',
            'penekanan_diskusi' => 'required|integer|between:1,5',
            'waktu_mulai_mencari_kerja' => 'required|integer|between:1,3',
            'bulan_sebelum_lulus' => 'nullable|integer|min:1|required_if:waktu_mulai_mencari_kerja,1',
            'bulan_sesudah_lulus' => 'nullable|integer|min:1|required_if:waktu_mulai_mencari_kerja,2',
            'cari_kerja_iklan_koran' => 'sometimes|boolean',
            'cari_kerja_tanpa_lowongan' => 'sometimes|boolean',
            'cari_kerja_pameran' => 'sometimes|boolean',
            'cari_kerja_internet' => 'sometimes|boolean',
            'cari_kerja_dihubungi_perusahaan' => 'sometimes|boolean',
            'cari_kerja_kemenakertrans' => 'sometimes|boolean',
            'cari_kerja_agen_tenaga_kerja' => 'sometimes|boolean',
            'cari_kerja_pusat_karir' => 'sometimes|boolean',
            'cari_kerja_kemahasiswaan_alumni' => 'sometimes|boolean',
            'cari_kerja_jejaring' => 'sometimes|boolean',
            'cari_kerja_relasi' => 'sometimes|boolean',
            'cari_kerja_bisnis_sendiri' => 'sometimes|boolean',
            'cari_kerja_magang' => 'sometimes|boolean',
            'cari_kerja_tempat_kerja_sama' => 'sometimes|boolean',
            'cari_kerja_lainnya_pilih' => 'sometimes|boolean',
            'cari_kerja_lainnya_isi' => 'nullable|string|required_if:cari_kerja_lainnya_pilih,1',
            'jumlah_instansi_dilamar' => 'required|integer|min:0',
            'jumlah_instansi_merespons' => 'required|integer|min:0|lte:jumlah_instansi_dilamar',
            'jumlah_instansi_wawancara' => 'required|integer|min:0|lte:jumlah_instansi_merespons',
            'situasi_saat_ini' => 'required|integer|between:1,5',
            'situasi_saat_ini_lainnya' => 'nullable|string|required_if:situasi_saat_ini,5',
            'aktif_mencari_pekerjaan_4_minggu' => 'required|integer|between:1,5',
            'aktif_mencari_pekerjaan_lainnya' => 'nullable|string|required_if:aktif_mencari_pekerjaan_4_minggu,5',
            'alasan_pekerjaan_sesuai_saat_ini' => 'sometimes|boolean',
            'alasan_mudah_dapat_kerja' => 'sometimes|boolean',
            'alasan_prospek_baik' => 'sometimes|boolean',
            'alasan_bidang_berbeda_tapi_sesuai' => 'sometimes|boolean',
            'alasan_promosi_posisi' => 'sometimes|boolean',
            'alasan_penghasilan_lebih_tinggi' => 'sometimes|boolean',
            'alasan_pekerjaan_aman' => 'sometimes|boolean',
            'alasan_pekerjaan_menarik' => 'sometimes|boolean',
            'alasan_pekerjaan_fleksibel' => 'sometimes|boolean',
            'alasan_dekat_dengan_rumah' => 'sometimes|boolean',
            'alasan_kebutuhan_keluarga' => 'sometimes|boolean',
            'alasan_karir_lain' => 'sometimes|boolean',
            'alasan_lainnya_pilih' => 'sometimes|boolean',
            'alasan_lainnya_isi' => 'nullable|string|required_if:alasan_lainnya_pilih,1',
            'beasiswa_masa_kuliah' => 'required|integer|between:1,22',
            'beasiswa_lainnya' => 'nullable|string|required_if:beasiswa_masa_kuliah,22',
            'org_bem_universitas' => 'sometimes|boolean',
            'org_bem_fakultas' => 'sometimes|boolean',
            'org_dpm_universitas' => 'sometimes|boolean',
            'org_dpm_fakultas' => 'sometimes|boolean',
            'org_ukm_universitas' => 'sometimes|boolean',
            'org_lso_fakultas' => 'sometimes|boolean',
            'org_hmj' => 'sometimes|boolean',
            'org_hmprodi' => 'sometimes|boolean',
            'org_hmi' => 'sometimes|boolean',
            'org_gmki' => 'sometimes|boolean',
            'org_pmkri' => 'sometimes|boolean',
            'org_pmii' => 'sometimes|boolean',
            'org_kammi' => 'sometimes|boolean',
            'org_cimsa' => 'sometimes|boolean',
            'org_lainnya_pilih' => 'sometimes|boolean',
            'org_lainnya_isi' => 'nullable|string|required_if:org_lainnya_pilih,1',
            'saran_untuk_universitas' => 'required|string|min:10|max:1000',
        ];

        $messages = [
            'penekanan_perkuliahan.required' => 'Penekanan perkuliahan wajib diisi.',
            'penekanan_perkuliahan.between' => 'Penekanan perkuliahan harus antara 1 sampai 5.',
            'penekanan_demontrasi.required' => 'Penekanan demonstrasi wajib diisi.',
            'penekanan_demontrasi.between' => 'Penekanan demonstrasi harus antara 1 sampai 5.',
            'penekanan_proyek_riset.required' => 'Penekanan proyek riset wajib diisi.',
            'penekanan_proyek_riset.between' => 'Penekanan proyek riset harus antara 1 sampai 5.',
            'penekanan_magang.required' => 'Penekanan magang wajib diisi.',
            'penekanan_magang.between' => 'Penekanan magang harus antara 1 sampai 5.',
            'penekanan_praktikum.required' => 'Penekanan praktikum wajib diisi.',
            'penekanan_praktikum.between' => 'Penekanan praktikum harus antara 1 sampai 5.',
            'penekanan_kerja_lapangan.required' => 'Penekanan kerja lapangan wajib diisi.',
            'penekanan_kerja_lapangan.between' => 'Penekanan kerja lapangan harus antara 1 sampai 5.',
            'penekanan_diskusi.required' => 'Penekanan diskusi wajib diisi.',
            'penekanan_diskusi.between' => 'Penekanan diskusi harus antara 1 sampai 5.',
            'waktu_mulai_mencari_kerja.required' => 'Waktu mulai mencari kerja wajib diisi.',
            'waktu_mulai_mencari_kerja.between' => 'Waktu mulai mencari kerja harus antara 1 sampai 3.',
            'bulan_sebelum_lulus.required_if' => 'Bulan sebelum lulus wajib diisi jika memilih opsi ini.',
            'bulan_sebelum_lulus.integer' => 'Bulan sebelum lulus harus berupa angka.',
            'bulan_sebelum_lulus.min' => 'Bulan sebelum lulus minimal 1.',
            'bulan_sesudah_lulus.required_if' => 'Bulan sesudah lulus wajib diisi jika memilih opsi ini.',
            'bulan_sesudah_lulus.integer' => 'Bulan sesudah lulus harus berupa angka.',
            'bulan_sesudah_lulus.min' => 'Bulan sesudah lulus minimal 1.',
            'cari_kerja_lainnya_isi.required_if' => 'Isi cari kerja lainnya jika memilih opsi ini.',
            'jumlah_instansi_dilamar.required' => 'Jumlah instansi yang dilamar wajib diisi.',
            'jumlah_instansi_dilamar.integer' => 'Jumlah instansi yang dilamar harus berupa angka.',
            'jumlah_instansi_dilamar.min' => 'Jumlah instansi yang dilamar minimal 0.',
            'jumlah_instansi_merespons.required' => 'Jumlah instansi yang merespons wajib diisi.',
            'jumlah_instansi_merespons.integer' => 'Jumlah instansi yang merespons harus berupa angka.',
            'jumlah_instansi_merespons.min' => 'Jumlah instansi yang merespons minimal 0.',
            'jumlah_instansi_merespons.lte' => 'Jumlah instansi yang merespons tidak boleh lebih besar dari yang dilamar.',
            'jumlah_instansi_wawancara.required' => 'Jumlah instansi yang mengundang wawancara wajib diisi.',
            'jumlah_instansi_wawancara.integer' => 'Jumlah instansi yang mengundang wawancara harus berupa angka.',
            'jumlah_instansi_wawancara.min' => 'Jumlah instansi yang mengundang wawancara minimal 0.',
            'jumlah_instansi_wawancara.lte' => 'Jumlah instansi yang mengundang wawancara tidak boleh lebih besar dari yang merespons.',
            'situasi_saat_ini.required' => 'Situasi saat ini wajib diisi.',
            'situasi_saat_ini.between' => 'Situasi saat ini harus antara 1 sampai 5.',
            'situasi_saat_ini_lainnya.required_if' => 'Isi situasi saat ini lainnya jika memilih opsi ini.',
            'aktif_mencari_pekerjaan_4_minggu.required' => 'Status pencarian kerja aktif wajib diisi.',
            'aktif_mencari_pekerjaan_4_minggu.between' => 'Status pencarian kerja aktif harus antara 1 sampai 5.',
            'aktif_mencari_pekerjaan_lainnya.required_if' => 'Isi status pencarian kerja aktif lainnya jika memilih opsi ini.',
            'alasan_lainnya_isi.required_if' => 'Isi alasan lainnya jika memilih opsi ini.',
            'beasiswa_masa_kuliah.required' => 'Beasiswa masa kuliah wajib diisi.',
            'beasiswa_masa_kuliah.between' => 'Beasiswa masa kuliah harus antara 1 sampai 22.',
            'beasiswa_lainnya.required_if' => 'Isi beasiswa lainnya jika memilih opsi ini.',
            'org_lainnya_isi.required_if' => 'Isi organisasi lainnya jika memilih opsi ini.',
            'saran_untuk_universitas.required' => 'Saran untuk universitas wajib diisi.',
            'saran_untuk_universitas.min' => 'Saran untuk universitas minimal 10 karakter.',
            'saran_untuk_universitas.max' => 'Saran untuk universitas maksimal 1000 karakter.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        // Validasi custom untuk checkbox
        $validator->after(function ($validator) use ($request) {
            // 1. Validasi cara mencari pekerjaan (minimal pilih 1)
            $caraCariKerjaChecked = $request->cari_kerja_iklan_koran ||
                $request->cari_kerja_tanpa_lowongan ||
                $request->cari_kerja_pameran ||
                $request->cari_kerja_internet ||
                $request->cari_kerja_dihubungi_perusahaan ||
                $request->cari_kerja_kemenakertrans ||
                $request->cari_kerja_agen_tenaga_kerja ||
                $request->cari_kerja_pusat_karir ||
                $request->cari_kerja_kemahasiswaan_alumni ||
                $request->cari_kerja_jejaring ||
                $request->cari_kerja_relasi ||
                $request->cari_kerja_bisnis_sendiri ||
                $request->cari_kerja_magang ||
                $request->cari_kerja_tempat_kerja_sama ||
                $request->cari_kerja_lainnya_pilih;

            if (!$caraCariKerjaChecked) {
                $validator->errors()->add('cari_kerja_lainnya_pilih', 'Pilih minimal 1 cara mencari pekerjaan');
            }

            // 2. Validasi alasan pekerjaan tidak sesuai (minimal pilih 1)
            $alasanChecked = $request->alasan_pekerjaan_sesuai_saat_ini ||
                $request->alasan_mudah_dapat_kerja ||
                $request->alasan_prospek_baik ||
                $request->alasan_bidang_berbeda_tapi_sesuai ||
                $request->alasan_promosi_posisi ||
                $request->alasan_penghasilan_lebih_tinggi ||
                $request->alasan_pekerjaan_aman ||
                $request->alasan_pekerjaan_menarik ||
                $request->alasan_pekerjaan_fleksibel ||
                $request->alasan_dekat_dengan_rumah ||
                $request->alasan_kebutuhan_keluarga ||
                $request->alasan_karir_lain ||
                $request->alasan_lainnya_pilih;

            if (!$alasanChecked) {
                $validator->errors()->add('alasan_lainnya_pilih', 'Pilih minimal 1 alasan');
            }

            // 3. Validasi organisasi (minimal pilih 1)
            $organisasiChecked = $request->org_bem_universitas ||
                $request->org_bem_fakultas ||
                $request->org_dpm_universitas ||
                $request->org_dpm_fakultas ||
                $request->org_ukm_universitas ||
                $request->org_lso_fakultas ||
                $request->org_hmj ||
                $request->org_hmprodi ||
                $request->org_hmi ||
                $request->org_gmki ||
                $request->org_pmkri ||
                $request->org_pmii ||
                $request->org_kammi ||
                $request->org_cimsa ||
                $request->org_lainnya_pilih;

            if (!$organisasiChecked) {
                $validator->errors()->add('org_lainnya_pilih', 'Pilih minimal 1 organisasi');
            }
        });

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $hasRequiredError = false;
            $hasValidationError = false;

            foreach ($errors as $error) {
                if (str_contains($error, 'wajib') || str_contains($error, 'harus diisi')) {
                    $hasRequiredError = true;
                } else {
                    $hasValidationError = true;
                }
            }

            $errorMessage = 'Terjadi kesalahan dalam pengisian data.';
            if ($hasRequiredError) {
                $errorMessage = 'Lengkapi semua data yang diperlukan.';
            } elseif ($hasValidationError) {
                $errorMessage = 'Isi data sesuai aturan yang ditetapkan.';
            }

            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', $errorMessage);
        }

        try {
            $validated = $validator->validated();

            $user = Auth::guard('mahasiswa')->user();
            $alumni = Alumni::where('user_nim', $user->nim)->firstOrFail();

            $updateData = [
                'penekanan_perkuliahan' => $validated['penekanan_perkuliahan'],
                'penekanan_demontrasi' => $validated['penekanan_demontrasi'],
                'penekanan_proyek_riset' => $validated['penekanan_proyek_riset'],
                'penekanan_magang' => $validated['penekanan_magang'],
                'penekanan_praktikum' => $validated['penekanan_praktikum'],
                'penekanan_kerja_lapangan' => $validated['penekanan_kerja_lapangan'],
                'penekanan_diskusi' => $validated['penekanan_diskusi'],
                'waktu_mulai_mencari_kerja' => $validated['waktu_mulai_mencari_kerja'],
                'bulan_sebelum_lulus_mencari_kerja' => $validated['bulan_sebelum_lulus'] ?? null,
                'bulan_setelah_lulus_mencari_kerja' => $validated['bulan_sesudah_lulus'] ?? null,
                'cari_kerja_iklan_koran' => $validated['cari_kerja_iklan_koran'] ?? false,
                'cari_kerja_tanpa_lowongan' => $validated['cari_kerja_tanpa_lowongan'] ?? false,
                'cari_kerja_pameran' => $validated['cari_kerja_pameran'] ?? false,
                'cari_kerja_internet' => $validated['cari_kerja_internet'] ?? false,
                'cari_kerja_dihubungi_perusahaan' => $validated['cari_kerja_dihubungi_perusahaan'] ?? false,
                'cari_kerja_kemenakertrans' => $validated['cari_kerja_kemenakertrans'] ?? false,
                'cari_kerja_agen_tenaga_kerja' => $validated['cari_kerja_agen_tenaga_kerja'] ?? false,
                'cari_kerja_pusat_karir' => $validated['cari_kerja_pusat_karir'] ?? false,
                'cari_kerja_kemahasiswaan_alumni' => $validated['cari_kerja_kemahasiswaan_alumni'] ?? false,
                'cari_kerja_jejaring' => $validated['cari_kerja_jejaring'] ?? false,
                'cari_kerja_relasi' => $validated['cari_kerja_relasi'] ?? false,
                'cari_kerja_bisnis_sendiri' => $validated['cari_kerja_bisnis_sendiri'] ?? false,
                'cari_kerja_magang' => $validated['cari_kerja_magang'] ?? false,
                'cari_kerja_tempat_kerja_sama' => $validated['cari_kerja_tempat_kerja_sama'] ?? false,
                'cari_kerja_lainnya_pilih' => $validated['cari_kerja_lainnya_pilih'] ?? false,
                'cari_kerja_lainnya_isi' => $validated['cari_kerja_lainnya_isi'] ?? null,
                'jumlah_instansi_dilamar' => $validated['jumlah_instansi_dilamar'],
                'jumlah_instansi_merespons' => $validated['jumlah_instansi_merespons'],
                'jumlah_instansi_wawancara' => $validated['jumlah_instansi_wawancara'],
                'situasi_saat_ini' => $validated['situasi_saat_ini'],
                'situasi_saat_ini_lainnya' => $validated['situasi_saat_ini_lainnya'] ?? null,
                'aktif_mencari_pekerjaan_4_minggu' => $validated['aktif_mencari_pekerjaan_4_minggu'],
                'aktif_mencari_pekerjaan_lainnya' => $validated['aktif_mencari_pekerjaan_lainnya'] ?? null,
                'alasan_pekerjaan_sesuai_saat_ini' => $validated['alasan_pekerjaan_sesuai_saat_ini'] ?? false,
                'alasan_mudah_dapat_kerja' => $validated['alasan_mudah_dapat_kerja'] ?? false,
                'alasan_prospek_baik' => $validated['alasan_prospek_baik'] ?? false,
                'alasan_bidang_berbeda_tapi_sesuai' => $validated['alasan_bidang_berbeda_tapi_sesuai'] ?? false,
                'alasan_promosi_posisi' => $validated['alasan_promosi_posisi'] ?? false,
                'alasan_penghasilan_lebih_tinggi' => $validated['alasan_penghasilan_lebih_tinggi'] ?? false,
                'alasan_pekerjaan_aman' => $validated['alasan_pekerjaan_aman'] ?? false,
                'alasan_pekerjaan_menarik' => $validated['alasan_pekerjaan_menarik'] ?? false,
                'alasan_pekerjaan_fleksibel' => $validated['alasan_pekerjaan_fleksibel'] ?? false,
                'alasan_dekat_dengan_rumah' => $validated['alasan_dekat_dengan_rumah'] ?? false,
                'alasan_kebutuhan_keluarga' => $validated['alasan_kebutuhan_keluarga'] ?? false,
                'alasan_karir_lain' => $validated['alasan_karir_lain'] ?? false,
                'alasan_lainnya_pilih' => $validated['alasan_lainnya_pilih'] ?? false,
                'alasan_lainnya_isi' => $validated['alasan_lainnya_isi'] ?? null,
                'beasiswa_masa_kuliah' => $validated['beasiswa_masa_kuliah'],
                'beasiswa_lainnya' => $validated['beasiswa_lainnya'] ?? null,
                'org_bem_universitas' => $validated['org_bem_universitas'] ?? false,
                'org_bem_fakultas' => $validated['org_bem_fakultas'] ?? false,
                'org_dpm_universitas' => $validated['org_dpm_universitas'] ?? false,
                'org_dpm_fakultas' => $validated['org_dpm_fakultas'] ?? false,
                'org_ukm_universitas' => $validated['org_ukm_universitas'] ?? false,
                'org_lso_fakultas' => $validated['org_lso_fakultas'] ?? false,
                'org_hmj' => $validated['org_hmj'] ?? false,
                'org_hmprodi' => $validated['org_hmprodi'] ?? false,
                'org_hmi' => $validated['org_hmi'] ?? false,
                'org_gmki' => $validated['org_gmki'] ?? false,
                'org_pmkri' => $validated['org_pmkri'] ?? false,
                'org_pmii' => $validated['org_pmii'] ?? false,
                'org_kammi' => $validated['org_kammi'] ?? false,
                'org_cimsa' => $validated['org_cimsa'] ?? false,
                'org_lainnya_pilih' => $validated['org_lainnya_pilih'] ?? false,
                'org_lainnya_isi' => $validated['org_lainnya_isi'] ?? null,
                'saran_untuk_universitas' => $validated['saran_untuk_universitas'],
            ];

            $alumni->update($updateData);

            return redirect('/alumni?tab=profil')->with('success', 'Data kuisioner berhasil disubmit.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function input_profil(Request $request)
    {
        try {
            $user = Auth::guard('mahasiswa')->user();
            
            // Filter array kosong sebelum validasi
            $requestData = $request->all();
            
            // Filter pengalaman - hapus item yang semua field utamanya kosong
            if (isset($requestData['pengalaman'])) {
                $requestData['pengalaman'] = array_filter($requestData['pengalaman'], function($item) {
                    return !empty($item['nama_perusahaan']) || !empty($item['jabatan']);
                });
            }
            
            // Filter pendidikan - hapus item yang semua field utamanya kosong
            if (isset($requestData['pendidikan'])) {
                $requestData['pendidikan'] = array_filter($requestData['pendidikan'], function($item) {
                    return !empty($item['nama_pendidikan']) || !empty($item['tingkat_pendidikan']);
                });
            }
            
            // Filter organisasi - hapus item yang semua field utamanya kosong
            if (isset($requestData['organisasi'])) {
                $requestData['organisasi'] = array_filter($requestData['organisasi'], function($item) {
                    return !empty($item['nama_organisasi']) || !empty($item['posisi']);
                });
            }
            
            // Buat request baru dengan data yang sudah difilter
            $filteredRequest = new Request($requestData);
            $filteredRequest->setMethod($request->getMethod());
            
            $validated = $filteredRequest->validate([
                'nama' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'no_telepon' => 'nullable|numeric|digits_between:10,15',
                'linkedin' => 'nullable|url|max:255',
                'portfolio' => 'nullable|url|max:255',
                'deskripsi_diri' => 'nullable|string|min:50|max:1000',
                'hard_skill' => 'nullable|string|min:10|max:500',
                'soft_skill' => 'nullable|string|min:10|max:500',
                'foto' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',

                // Validasi array pengalaman - hanya untuk item yang tidak kosong
                'pengalaman' => 'nullable|array',
                'pengalaman.*.nama_perusahaan' => 'required|string|max:255',
                'pengalaman.*.jabatan' => 'required|string|max:255',
                'pengalaman.*.lokasi_perusahaan' => 'nullable|string|max:255',
                'pengalaman.*.deskripsi_perusahaan' => 'nullable|string|max:1000',
                'pengalaman.*.tanggal_mulai' => 'nullable|date',
                'pengalaman.*.tanggal_selesai' => 'nullable|date|after_or_equal:pengalaman.*.tanggal_mulai',
                'pengalaman.*.masih_bekerja' => 'nullable|boolean',
                'pengalaman.*.portofolio_prestasi' => 'nullable|array',
                'pengalaman.*.portofolio_prestasi.*' => 'string|max:255',

                // Validasi array pendidikan - hanya untuk item yang tidak kosong
                'pendidikan' => 'nullable|array',
                'pendidikan.*.nama_pendidikan' => 'required|string|max:255',
                'pendidikan.*.tingkat_pendidikan' => 'required|string|in:SMA/SMK,D3,S1,S2,S3',
                'pendidikan.*.lokasi_pendidikan' => 'nullable|string|max:255',
                'pendidikan.*.tanggal_mulai' => 'nullable|date',
                'pendidikan.*.tanggal_selesai' => 'nullable|date|after_or_equal:pendidikan.*.tanggal_mulai',
                'pendidikan.*.grade' => 'nullable|numeric|between:0.00,4.00',
                'pendidikan.*.aktivitas_pencapaian' => 'nullable|array',
                'pendidikan.*.aktivitas_pencapaian.*' => 'string|max:255',

                // Validasi array organisasi - hanya untuk item yang tidak kosong
                'organisasi' => 'nullable|array',
                'organisasi.*.nama_organisasi' => 'required|string|max:255',
                'organisasi.*.posisi' => 'required|string|max:255',
                'organisasi.*.lokasi_organisasi' => 'nullable|string|max:255',
                'organisasi.*.deskripsi_organisasi' => 'nullable|string|max:1000',
                'organisasi.*.tanggal_mulai' => 'nullable|date',
                'organisasi.*.tanggal_selesai' => 'nullable|date|after_or_equal:organisasi.*.tanggal_mulai',
                'organisasi.*.masih_aktif' => 'nullable|boolean',
                'organisasi.*.deskripsi_pekerjaan' => 'nullable|array',
                'organisasi.*.deskripsi_pekerjaan.*' => 'string|max:255',
            ], [
                'nama.string' => 'Nama lengkap harus berupa teks.',
                'nama.max' => 'Nama lengkap maksimal 255 karakter.',
                'email.email' => 'Format email tidak valid.',
                'email.max' => 'Email maksimal 255 karakter.',
                'no_telepon.numeric' => 'Nomor telepon harus berupa angka.',
                'no_telepon.digits_between' => 'Nomor telepon harus antara 10 sampai 15 digit.',
                'linkedin.url' => 'Format LinkedIn URL tidak valid. Contoh: https://linkedin.com/in/username',
                'linkedin.max' => 'LinkedIn URL maksimal 255 karakter.',
                'portfolio.url' => 'Format Portfolio URL tidak valid. Contoh: https://example.com',
                'portfolio.max' => 'Portfolio URL maksimal 255 karakter.',
                'deskripsi_diri.string' => 'Deskripsi diri harus berupa teks.',
                'deskripsi_diri.min' => 'Deskripsi diri minimal 50 karakter.',
                'deskripsi_diri.max' => 'Deskripsi diri maksimal 1000 karakter.',
                'hard_skill.string' => 'Hard skills harus berupa teks.',
                'hard_skill.min' => 'Hard skills minimal 10 karakter.',
                'hard_skill.max' => 'Hard skills maksimal 500 karakter.',
                'soft_skill.string' => 'Soft skills harus berupa teks.',
                'soft_skill.min' => 'Soft skills minimal 10 karakter.',
                'soft_skill.max' => 'Soft skills maksimal 500 karakter.',
                'foto.image' => 'File yang diupload harus berupa gambar.',
                'foto.mimes' => 'Format foto harus: jpeg, jpg, png, atau gif.',
                'foto.max' => 'Ukuran foto maksimal 2MB.',

                // message validasi array pengalaman
                'pengalaman.*.nama_perusahaan.required' => 'Nama perusahaan wajib diisi.',
                'pengalaman.*.jabatan.required' => 'Jabatan wajib diisi.',
                'pengalaman.*.tanggal_mulai.date' => 'Tanggal mulai harus berupa tanggal.',
                'pengalaman.*.tanggal_selesai.date' => 'Tanggal selesai harus berupa tanggal.',
                'pengalaman.*.tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
                'pengalaman.*.masih_bekerja.boolean' => 'Masih bekerja harus bernilai true atau false.',
                'pengalaman.*.portofolio_prestasi.array' => 'Portofolio prestasi harus berupa array.',
                'pengalaman.*.portofolio_prestasi.*.string' => 'Portofolio prestasi harus berupa teks.',
                'pengalaman.*.portofolio_prestasi.*.max' => 'Portofolio prestasi maksimal 255 karakter.',

                // message validasi array pendidikan
                'pendidikan.*.nama_pendidikan.required' => 'Nama pendidikan wajib diisi.',
                'pendidikan.*.nama_pendidikan.string' => 'Nama pendidikan harus berupa teks.',
                'pendidikan.*.nama_pendidikan.max' => 'Nama pendidikan maksimal 255 karakter.',
                'pendidikan.*.lokasi_pendidikan.string' => 'Lokasi pendidikan harus berupa teks.',
                'pendidikan.*.lokasi_pendidikan.max' => 'Lokasi pendidikan maksimal 255 karakter.',
                'pendidikan.*.tanggal_mulai.date' => 'Tanggal mulai harus berupa tanggal.',
                'pendidikan.*.tanggal_selesai.date' => 'Tanggal selesai harus berupa tanggal.',
                'pendidikan.*.tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
                'pendidikan.*.tingkat_pendidikan.required' => 'Tingkat pendidikan wajib diisi.',
                'pendidikan.*.tingkat_pendidikan.string' => 'Tingkat pendidikan harus berupa teks.',
                'pendidikan.*.tingkat_pendidikan.in' => 'Tingkat pendidikan harus salah satu dari: SMA/SMK, D3, S1, S2, S3.',
                'pendidikan.*.grade.numeric' => 'Grade/IPK harus berupa angka.',
                'pendidikan.*.grade.between' => 'Grade/IPK harus antara 0.00 sampai 4.00.',
                'pendidikan.*.aktivitas_pencapaian.array' => 'Aktivitas & pencapaian harus berupa array.',
                'pendidikan.*.aktivitas_pencapaian.*.string' => 'Aktivitas & pencapaian harus berupa teks.',
                'pendidikan.*.aktivitas_pencapaian.*.max' => 'Aktivitas & pencapaian maksimal 255 karakter.',

                // message validasi array organisasi
                'organisasi.*.nama_organisasi.required' => 'Nama organisasi wajib diisi.',
                'organisasi.*.nama_organisasi.string' => 'Nama organisasi harus berupa teks.',
                'organisasi.*.nama_organisasi.max' => 'Nama organisasi maksimal 255 karakter.',
                'organisasi.*.posisi.required' => 'Posisi wajib diisi.',
                'organisasi.*.posisi.string' => 'Posisi harus berupa teks.',
                'organisasi.*.posisi.max' => 'Posisi maksimal 255 karakter.',
                'organisasi.*.lokasi_organisasi.string' => 'Lokasi organisasi harus berupa teks.',
                'organisasi.*.lokasi_organisasi.max' => 'Lokasi organisasi maksimal 255 karakter.',
                'organisasi.*.deskripsi_organisasi.string' => 'Deskripsi organisasi harus berupa teks.',
                'organisasi.*.deskripsi_organisasi.max' => 'Deskripsi organisasi maksimal 1000 karakter.',
                'organisasi.*.tanggal_mulai.date' => 'Tanggal mulai harus berupa tanggal.',
                'organisasi.*.tanggal_selesai.date' => 'Tanggal selesai harus berupa tanggal.',
                'organisasi.*.tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
                'organisasi.*.masih_aktif.boolean' => 'Status masih aktif harus benar atau salah.',
                'organisasi.*.deskripsi_pekerjaan.array' => 'Deskripsi pekerjaan harus berupa array.',
                'organisasi.*.deskripsi_pekerjaan.*.string' => 'Deskripsi pekerjaan harus berupa teks.',
                'organisasi.*.deskripsi_pekerjaan.*.max' => 'Deskripsi pekerjaan maksimal 255 karakter.',
            ]);

            $profil = Profil::firstOrNew(['user_nim' => $user->nim]);

            // Handle file upload
            if ($request->hasFile('foto')) {
                $foto = $request->file('foto');
                $fileName = time().'_'.Str::random(10).'.'.$foto->getClientOriginalExtension();
                $fotoPath = $foto->storeAs('profil_foto', $fileName, 'public');
                
                // Delete old photo if exists
                if ($profil->foto) {
                    Storage::disk('public')->delete($profil->foto);
                }
                
                $profil->foto = $fotoPath;
            }

            $pengalamanData = [];
            if (isset($validated['pengalaman']) && !empty($validated['pengalaman'])) {
                foreach ($validated['pengalaman'] as $id => $item) {
                    $pengalamanItem = [
                        'jabatan' => $item['jabatan'] ?? null,
                        'tanggal_mulai' => $item['tanggal_mulai'] ?? null,
                        'nama_perusahaan' => $item['nama_perusahaan'] ?? null,
                        'tanggal_selesai' => $item['tanggal_selesai'] ?? null,
                        'masih_bekerja' => $item['masih_bekerja'] ?? 0,
                        'lokasi_perusahaan' => $item['lokasi_perusahaan'] ?? null,
                        'deskripsi_perusahaan' => $item['deskripsi_perusahaan'] ?? null,
                        'portofolio_prestasi' => isset($item['portofolio_prestasi']) 
                            ? array_values(array_filter($item['portofolio_prestasi'])) 
                            : [] 
                    ];
                    $pengalamanData[] = $pengalamanItem;
                }
            }

            $pendidikanData = [];
            if (isset($validated['pendidikan']) && !empty($validated['pendidikan'])) {
                foreach ($validated['pendidikan'] as $id => $item) {
                    $pendidikanItem = [
                        'nama_pendidikan' => $item['nama_pendidikan'] ?? null,
                        'lokasi_pendidikan' => $item['lokasi_pendidikan'] ?? null,
                        'tanggal_mulai' => $item['tanggal_mulai'] ?? null,
                        'tanggal_selesai' => $item['tanggal_selesai'] ?? null,
                        'tingkat_pendidikan' => $item['tingkat_pendidikan'] ?? null,
                        'grade' => $item['grade'] ?? null,
                        'aktivitas_pencapaian' => isset($item['aktivitas_pencapaian'])
                            ? array_values(array_filter($item['aktivitas_pencapaian']))
                            : []
                    ];
                    $pendidikanData[] = $pendidikanItem;
                }
            }

            $organisasiData = [];
            if (isset($validated['organisasi']) && !empty($validated['organisasi'])) {
                foreach ($validated['organisasi'] as $id => $item) {
                    $organisasiItem = [
                        'nama_organisasi' => $item['nama_organisasi'] ?? null,
                        'posisi' => $item['posisi'] ?? null,
                        'lokasi_organisasi' => $item['lokasi_organisasi'] ?? null,
                        'deskripsi_organisasi' => $item['deskripsi_organisasi'] ?? null,
                        'tanggal_mulai' => $item['tanggal_mulai'] ?? null,
                        'tanggal_selesai' => $item['tanggal_selesai'] ?? null,
                        'masih_aktif' => $item['masih_aktif'] ?? 0,
                        'deskripsi_pekerjaan' => isset($item['deskripsi_pekerjaan']) 
                            ? array_values(array_filter($item['deskripsi_pekerjaan'])) 
                            : []
                    ];
                    $organisasiData[] = $organisasiItem;
                }
            }

            // Update data
            $profil->user_nim = $user->nim;
            $profil->nama = $validated['nama'];
            $profil->email = $validated['email'];
            $profil->no_telepon = $validated['no_telepon'] ?? null;
            $profil->linkedin = $validated['linkedin'] ?? null;
            $profil->portfolio = $validated['portfolio'] ?? null;
            $profil->deskripsi_diri = $validated['deskripsi_diri'] ?? null;
            $profil->hard_skill = $validated['hard_skill'] ?? null;
            $profil->soft_skill = $validated['soft_skill'] ?? null;
            $profil->pengalaman = !empty($pengalamanData) ? json_encode($pengalamanData) : null;
            $profil->pendidikan = !empty($pendidikanData) ? json_encode($pendidikanData) : null;
            $profil->organisasi = !empty($organisasiData) ? json_encode($organisasiData) : null;

            $profil->save();
            
            return back()->with('success', 'Berhasil menyimpan profil.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors()->all();
            $errorType = collect($errors)->contains(fn($e) => str_contains($e, 'wajib diisi')) 
                ? 'Lengkapi semua data yang diperlukan.' 
                : 'Isi data sesuai aturan yang ditetapkan.';

            return back()->withErrors($e->validator)->withInput()->with('error', $errorType);

        } catch (\Exception $e) {
            \Log::error('ProfilController error: '.$e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    public function generateCV()
    {
        try {
            $user = Auth::guard('mahasiswa')->user();
            $profil = Profil::where('user_nim', $user->nim)->first(); 

            // Hapus file CV lama jika ada
            if ($profil->cv_path && Storage::disk('public')->exists($profil->cv_path)) {
                Storage::disk('public')->delete($profil->cv_path);
            }
            
            // Format data untuk CV
            $data = [
                'nama' => $profil->nama,
                'nim' => $profil->user_nim,
                'email' => $profil->email,
                'no_telepon' => $profil->no_telepon,
                'linkedin' => $profil->linkedin,
                'portfolio' => $profil->portfolio,
                'deskripsi_diri' => $profil->deskripsi_diri,
                'hard_skill' => $profil->hard_skill ? explode(',', $profil->hard_skill) : [],
                'soft_skill' => $profil->soft_skill ? explode(',', $profil->soft_skill) : [],
                'pengalaman' => $profil->pengalaman ? json_decode($profil->pengalaman, true) : [],
                'pendidikan' => $profil->pendidikan ? json_decode($profil->pendidikan, true) : [],
                'organisasi' => $profil->organisasi ? json_decode($profil->organisasi, true) : [],
            ];

            // Generate PDF
            $pdf = Pdf::loadView('pdf.cv', $data)
                ->setOption([
                    'fontDir' => public_path('/fonts'),
                    'fontCache' => public_path('/fonts'),
                    'defaultFont' => 'Arial',
                    'isRemoteEnabled' => true,
                ]);
            
            $pdf->setPaper('a4', 'portrait');

            $filename = 'cv-' . Str::slug($user->nama) . '-' . time() . '.pdf';
            $path = 'cv/' . $filename;  

            // Simpan ke storage/app/public/cv
            Storage::disk('public')->put($path, $pdf->output());

            $profil->cv_path = $path;
            $profil->save();

            return response()->json([
                'success' => true,
                'path' => Storage::url($path),
                'message' => 'CV berhasil dibuat'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat CV: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadCv()
    {
        $user = Auth::guard('mahasiswa')->user();
        $profil = Profil::where('user_nim', $user->nim)->first();

        if (!$profil || !$profil->cv_path || !Storage::disk('public')->exists($profil->cv_path)) {
            return redirect()->back()->with('error', 'File CV tidak ditemukan.');
        }
        
        return Storage::disk('public')->download($profil->cv_path, 'CV_' . $user->nama . '.pdf');        
    }
}
