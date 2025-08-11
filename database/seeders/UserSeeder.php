<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mahasiswa = [
            [
                'name' => 'Mahasiswa 1',
                'nim' => '2021012345', // NIM untuk mahasiswa
                'email' => 'mahasiswa1@example.com',
                'password' => Hash::make('password123'), // Password yang di-hash
            ]
        ];

        Mahasiswa::truncate();
        
        foreach ($mahasiswa as $mahasiswa) {
            Mahasiswa::create($mahasiswa);
        }
    }
}
