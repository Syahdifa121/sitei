<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alumni;
use Illuminate\Http\Request;

class AlumniController extends Controller
{
    public function index()
    {
        $alumni = Alumni::with('mahasiswa')->get();
        
        return response()->json($alumni->map(function ($alumni) {
            return [
                'id' => $alumni->id,
                'name' => $alumni->mahasiswa->nama,
                'email' => $alumni->mahasiswa->email,
                'location' => [
                    'lat' => $alumni->lokasi_pekerjaan_provinsi ? $this->getProvinceCoordinates($alumni->lokasi_pekerjaan_provinsi)['lat'] : null,
                    'lng' => $alumni->lokasi_pekerjaan_provinsi ? $this->getProvinceCoordinates($alumni->lokasi_pekerjaan_provinsi)['lng'] : null,
                ],
                'company' => $alumni->nama_perusahaan,
                'job_status' => $alumni->status_saat_ini == 1 ? 'Bekerja' : 'Tidak Bekerja',
                'year' => $alumni->tahun_lulus,
                'province' => $alumni->lokasi_pekerjaan_provinsi,
                'city' => $alumni->lokasi_pekerjaan_kabupaten,
                'salary' => $alumni->pendapatan_per_bulan,
            ];
        }));
    }

    private function getProvinceCoordinates($provinceName)
    {
        // This is a simplified version. You should implement proper coordinate mapping
        $coordinates = [
            'ACEH' => ['lat' => 4.695135, 'lng' => 96.749399],
            'SUMATERA UTARA' => ['lat' => 2.1153547, 'lng' => 99.5450974],
            'SUMATERA BARAT' => ['lat' => -0.7399397, 'lng' => 100.8000051],
            'RIAU' => ['lat' => 0.2933469, 'lng' => 101.7068294],
            'KEPULAUAN RIAU' => ['lat' => 3.9456514, 'lng' => 108.1428669],
            'JAMBI' => ['lat' => -1.6101229, 'lng' => 103.6131203],
            'SUMATERA SELATAN' => ['lat' => -3.3194374, 'lng' => 103.914399],
            'BENGKULU' => ['lat' => -3.5778471, 'lng' => 102.3463875],
            'LAMPUNG' => ['lat' => -4.5585849, 'lng' => 105.4068079],
            'KEPULAUAN BANGKA BELITUNG' => ['lat' => -2.7410513, 'lng' => 106.4405872],
            'DKI JAKARTA' => ['lat' => -6.2087634, 'lng' => 106.845599],
            'BANTEN' => ['lat' => -6.4058172, 'lng' => 106.0640179],
            'JAWA BARAT' => ['lat' => -6.9147444, 'lng' => 107.6098111],
            'JAWA TENGAH' => ['lat' => -7.150975, 'lng' => 110.1402594],
            'DI YOGYAKARTA' => ['lat' => -7.7955798, 'lng' => 110.3694896],
            'JAWA TIMUR' => ['lat' => -7.5360639, 'lng' => 112.2384017],
            'BALI' => ['lat' => -8.3405389, 'lng' => 115.0919509],
            'NUSA TENGGARA BARAT' => ['lat' => -8.5833333, 'lng' => 116.1166667],
            'NUSA TENGGARA TIMUR' => ['lat' => -8.6573819, 'lng' => 121.0793705],
            'KALIMANTAN BARAT' => ['lat' => -0.2787808, 'lng' => 111.4752851],
            'KALIMANTAN TENGAH' => ['lat' => -1.6814878, 'lng' => 113.3823545],
            'KALIMANTAN SELATAN' => ['lat' => -3.3194374, 'lng' => 114.5943784],
            'KALIMANTAN TIMUR' => ['lat' => 1.6406296, 'lng' => 116.419389],
            'KALIMANTAN UTARA' => ['lat' => 3.0730929, 'lng' => 116.0413889],
            'SULAWESI UTARA' => ['lat' => 0.6246932, 'lng' => 123.9750018],
            'GORONTALO' => ['lat' => 0.5435442, 'lng' => 123.0627691],
            'SULAWESI TENGAH' => ['lat' => -1.4300254, 'lng' => 121.4456179],
            'SULAWESI BARAT' => ['lat' => -2.8441371, 'lng' => 119.2320784],
            'SULAWESI SELATAN' => ['lat' => -3.6687994, 'lng' => 119.9740534],
            'SULAWESI TENGGARA' => ['lat' => -4.1449101, 'lng' => 122.174605],
            'MALUKU' => ['lat' => -3.2384616, 'lng' => 130.1452734],
            'MALUKU UTARA' => ['lat' => 0.6301283, 'lng' => 127.8087693],
            'PAPUA' => ['lat' => -4.269928, 'lng' => 138.0803529],
            'PAPUA BARAT' => ['lat' => -1.3361154, 'lng' => 133.1747162],
        ];

        return $coordinates[strtoupper($provinceName)] ?? ['lat' => 0, 'lng' => 0];
    }
} 