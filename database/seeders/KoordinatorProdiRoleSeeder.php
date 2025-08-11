<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class KoordinatorProdiRoleSeeder extends Seeder
{
    public function run()
    {
        // Periksa apakah role koordinator prodi sudah ada
        $roleExists = Role::where('role_akses', 'koordinator_prodi')->exists();
        
        if (!$roleExists) {
            Role::create([
                'role_akses' => 'koordinator_prodi',
            ]);
            
            $this->command->info('Role Koordinator Prodi berhasil ditambahkan');
        } else {
            $this->command->info('Role Koordinator Prodi sudah ada');
        }
    }
}