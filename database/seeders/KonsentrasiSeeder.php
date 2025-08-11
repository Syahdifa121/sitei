<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KonsentrasiSeeder extends Seeder
{
    public function run()
    {
        $konsentrasi = [
            [
                'id' => 1,
                'nama_konsentrasi' => 'Rekayasa Perangkat Lunak',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'id' => 2,
                'nama_konsentrasi' => 'Komputer Cerdas & Visualisasi',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'id' => 3,
                'nama_konsentrasi' => 'Komputer Berbasis Jaringan',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ];

        DB::table('konsentrasi')->insert($konsentrasi);
    }
}