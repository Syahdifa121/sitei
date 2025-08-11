<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DosenSeeder extends Seeder
{
    public function run()
    {
        $koordinatorProdiRoleId = DB::table('role')
        ->where('role_akses', 'koordinator_prodi')
        ->value('id');

            // Jika tidak ditemukan, gunakan default
            if (!$koordinatorProdiRoleId) {
                // Buat role koordinator prodi jika belum ada
                $koordinatorProdiRoleId = DB::table('role')->insertGetId([
                    'role_akses' => 'koordinator_prodi',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }
        $dosen = [
            [
                'nip' => '198501012015041001',
                'nama' => 'Contoh Dosen 1',
                'nama_singkat' => 'CD',
                'email' => 'ummul.azhari4051@student.unri.ac.id',
                'password' => Hash::make('password123'),
                'prodi_id' => 1, // Sesuaikan dengan ID prodi yang sesuai
                'role_id' => 1,  // Role dosen
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'nip' => '198501012015041002',
                'nama' => 'Contoh Dosen 2',
                'nama_singkat' => 'CD',
                'email' => 'Contoh Dosen 2',
                'password' => Hash::make('password123'),
                'prodi_id' => 1, // Sesuaikan dengan ID prodi yang sesuai
                'role_id' => 1,  // Role dosen
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'nip' => '198501012015041025',
                'nama' => 'Contoh Dosen 3',
                'nama_singkat' => 'CD',
                'email' => 'adrian.marchel@student.unri.ac.id',
                'password' => Hash::make('pw123'),
                'prodi_id' => 1, // Sesuaikan dengan ID prodi yang sesuai
                'role_id' => $koordinatorProdiRoleId,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ];

        DB::table('dosens')->insert($dosen);
    }
}