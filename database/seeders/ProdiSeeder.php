<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProdiSeeder extends Seeder
{
    public function run()
    {
        $prodis = [
            [
                'id' => 1,
                'nama_prodi' => 'Teknik Elektro',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'id' => 2,
                'nama_prodi' => 'Teknik Informatika',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ];

        DB::table('prodi')->insert($prodis);
    }
}
