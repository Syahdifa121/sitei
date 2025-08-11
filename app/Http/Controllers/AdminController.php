<?php

namespace App\Http\Controllers;

use App\Models\Alumni;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function get_admin_alumni_view()
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

        if(request()->get('tab') === 'statistik-alumni') {
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

        return view('alumni.admin.alumni')->with([
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
