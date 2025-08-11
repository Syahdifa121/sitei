<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tambahkan nilai DIBATALKAN ke enum status
        DB::statement("ALTER TABLE usulan_bimbingans MODIFY COLUMN status ENUM('USULAN', 'DITERIMA', 'DISETUJUI', 'DITOLAK', 'SELESAI', 'DIBATALKAN') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan ke nilai enum asli tanpa DIBATALKAN
        DB::statement("ALTER TABLE usulan_bimbingans MODIFY COLUMN status ENUM('USULAN', 'DITERIMA', 'DISETUJUI', 'DITOLAK', 'SELESAI') NOT NULL");
    }
};