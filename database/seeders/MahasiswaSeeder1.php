<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class MahasiswaSeeder1 extends Seeder
{
    public function run()
    {
        $mahasiswa = [
            [
                'nim' => '2107112734',
                'nama' => 'Nurul Nyi Qoniah',
                'angkatan' => 2021,
                'email' => 'nurul.nyi2734@student.unri.ac.id',
                'password' => Hash::make('2107112734'),
                'prodi_id' => 2,
                'konsentrasi_id' => 1,
                'role_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nim' => '2107110261',
                'nama' => 'Salwa Madihah Syahnevi',
                'angkatan' => 2021,
                'email' => 'salwa.madihah0261@student.unri.ac.id',
                'password' => Hash::make('2107110261'),
                'prodi_id' => 2,
                'konsentrasi_id' => 1,
                'role_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nim' => '2107135422',
                'nama' => 'Salman Al Haritsi',
                'angkatan' => 2021,
                'email' => 'salman.al5422@student.unri.ac.id',
                'password' => Hash::make('2107135422'),
                'prodi_id' => 2,
                'konsentrasi_id' => 1,
                'role_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nim' => '2107110260',
                'nama' => 'Egi Erlangga',
                'angkatan' => 2021,
                'email' => 'egi.erlangga0260@student.unri.ac.id',
                'password' => Hash::make('2107110260'),
                'prodi_id' => 2,
                'konsentrasi_id' => 1,
                'role_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nim' => '2107125642',
                'nama' => 'Gilang Kurnia Mandari',
                'angkatan' => 2021,
                'email' => 'gilang.kurnia5642@student.unri.ac.id',
                'password' => Hash::make('2107125642'),
                'prodi_id' => 2,
                'konsentrasi_id' => 1,
                'role_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nim' => '2107112738',
                'nama' => 'Fajar Rahmat',
                'angkatan' => 2021,
                'email' => 'fajar.rahmat2738@student.unri.ac.id',
                'password' => Hash::make('2107112738'),
                'prodi_id' => 2,
                'konsentrasi_id' => 1,
                'role_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nim' => '2107113602',
                'nama' => 'Ranto Anjasmara Marpaung',
                'angkatan' => 2021,
                'email' => 'ranto.anjasmara3602@student.unri.ac.id',
                'password' => Hash::make('2107113602'),
                'prodi_id' => 2,
                'konsentrasi_id' => 1,
                'role_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nim' => '2107112730',
                'nama' => 'Satria Julhendra',
                'angkatan' => 2021,
                'email' => 'satria.julhendra2730@student.unri.ac.id',
                'password' => Hash::make('2107112730'),
                'prodi_id' => 2,
                'konsentrasi_id' => 1,
                'role_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nim' => '2107110257',
                'nama' => 'Cut Muthia Ramadhani',
                'angkatan' => 2021,
                'email' => 'cut.muthia0257@student.unri.ac.id',
                'password' => Hash::make('2107110257'),
                'prodi_id' => 2,
                'konsentrasi_id' => 1,
                'role_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nim' => '2107110516',
                'nama' => 'Yudhitya M. Renandra',
                'angkatan' => 2021,
                'email' => 'yudhitya.m0516@student.unri.ac.id',
                'password' => Hash::make('2107110516'),
                'prodi_id' => 2,
                'konsentrasi_id' => 1,
                'role_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nim' => '2107125643',
                'nama' => 'Muhammad Haikal Fikri',
                'angkatan' => 2021,
                'email' => 'muhammad.haikal5643@student.unri.ac.id',
                'password' => Hash::make('2107125643'),
                'prodi_id' => 2,
                'konsentrasi_id' => 1,
                'role_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nim' => '2107110255',
                'nama' => 'Syahirah Tri Meilina',
                'angkatan' => 2021,
                'email' => 'syahirah.tri0255@student.unri.ac.id',
                'password' => Hash::make('2107110255'),
                'prodi_id' => 2,
                'konsentrasi_id' => 1,
                'role_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nim' => '2107110665',
                'nama' => 'Desi Maya Sari',
                'angkatan' => 2021,
                'email' => 'desi.maya0665@student.unri.ac.id',
                'password' => Hash::make('2107110665'),
                'prodi_id' => 2,
                'konsentrasi_id' => 1,
                'role_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        foreach ($mahasiswa as $data) {
            DB::table('mahasiswas')->updateOrInsert(
                ['nim' => $data['nim']], // Key untuk mencari record yang sudah ada
                $data // Data yang akan diinsert atau diupdate
            );
        }
    }
}
