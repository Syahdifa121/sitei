<?php

namespace Database\Seeders;

use Carbon\Carbon;
use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = [
            [
                'nip' => '198501012015041111',
                'nama' => 'Admin 1',
                'nama_singkat' => 'AD',
                'email' => 'admin1@example.com',
                'password' => Hash::make('pw123'), // Password yang di-hash
                'role_id' => 3,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ];

        DB::table('admin')->insert($admin);
    }
}
