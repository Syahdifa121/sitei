<?php

namespace App\Http\Controllers;

use App\Models\Alumni;
use Illuminate\Http\Request;

class AlumniController extends Controller
{
    public function getMapData()
    {
        $alumni = Alumni::with('mahasiswa')->get();

        // Load JSON data
        $provinces = json_decode(file_get_contents(resource_path('data/provinces.json')), true);
        $regencies = json_decode(file_get_contents(resource_path('data/regencies.json')), true);

        // Create lookup arrays for faster access
        $provinceLookup = array_column($provinces, null, 'name');
        $regencyLookup = array_column($regencies, null, 'name');

        // Transform the data to match the expected format
        $JOB_STATUS_MAP = [
            1 => 'Bekerja',
            2 => 'Wirausaha',
            3 => 'Studi Lanjut',
            4 => 'Mencari Kerja',
            5 => 'Belum memungkinkan bekerja',
        ];
        $transformedData = $alumni->map(function ($item) use ($provinceLookup, $regencyLookup, $JOB_STATUS_MAP) {
            // Convert keahlian from string to array if it's stored as JSON
            $keahlian = is_string($item->keahlian) ? json_decode($item->keahlian, true) : [];
            
            // Get coordinates from JSON data
            $province = $provinceLookup[strtoupper($item->lokasi_pekerjaan_provinsi)] ?? null;
            $regency = $regencyLookup[strtoupper($item->lokasi_pekerjaan_kabupaten)] ?? null;
            
            // Use regency coordinates if available, otherwise use province coordinates
            $location = [0.53333, 101.46667]; // Default to Pekanbaru coordinates
            if ($regency) {
                $location = [$regency['latitude'], $regency['longitude']];
            } elseif ($province) {
                $location = [$province['latitude'], $province['longitude']];
            }
            
            // Map status pekerjaan (cast ke integer agar mapping selalu cocok)
            $job_status = $JOB_STATUS_MAP[(int)$item->status_saat_ini] ?? 'Unknown';
            
            // Determine job title based on status
            $job = '';
            if ($item->status_saat_ini == 1) { // Bekerja
                $job = $item->pekerjaan_saat_ini ?: 'Tidak disebutkan';
            } elseif ($item->status_saat_ini == 2) { // Wirausaha
                $job = $item->posisi_wirausaha ?: 'Wirausaha';
            } elseif ($item->status_saat_ini == 3) { // Studi Lanjut
                $job = 'Mahasiswa';
            } else {
                $job = '-';
            }
            
            return [
                'name' => $item->nama ?? ($item->mahasiswa->nama ?? 'Nama tidak tersedia'),
                'job' => $job,
                'company' => $item->nama_perusahaan ?: '-',
                'province' => $item->lokasi_pekerjaan_provinsi ?: '-',
                'city' => $item->lokasi_pekerjaan_kabupaten ?: '-',
                'status' => $job_status, // Use mapped status text, not number
                'salary' => $item->pendapatan_per_bulan,
                'company_type' => $item->jenis_perusahaan,
                'email' => $item->mahasiswa->email ?? $item->email,
                'no_hp' => $item->no_telepon,
                'gender' => $item->jenis_kelamin,
                'linkedin' => $item->linkedin,
                'tentangSaya' => $item->deskripsi_diri,
                'keahlian' => $keahlian,
                'graduationYear' => $item->tahun_lulus,
                'jabatanAlumni' => $item->posisi_wirausaha,
                'job_status' => $job_status,
                'location' => $location
            ];
        });

        return response()->json($transformedData);
    }
} 