<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Nonaktifkan foreign key checks sementara
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Truncate semua table untuk memastikan data bersih
        \DB::table('mahasiswas')->truncate();
        \DB::table('dosens')->truncate();
        \DB::table('konsentrasi')->truncate();
        \DB::table('prodi')->truncate();
        \DB::table('role')->truncate();

        // Aktifkan kembali foreign key checks
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Jalankan seeders dalam urutan yang benar
        $this->call([
            RoleSeeder::class,
            KoordinatorProdiRoleSeeder::class,  // Pastikan ini dijalankan sebelum DosenSeeder
            ProdiSeeder::class,
            KonsentrasiSeeder::class,
            DosenSeeder::class,
            MahasiswaSeeder::class,
            AdminSeeder::class
        ]);
    }
}
