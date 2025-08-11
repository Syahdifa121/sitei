<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('jadwal_bimbingans', function (Blueprint $table) {
            if (!Schema::hasColumn('jadwal_bimbingans', 'jumlah_pendaftar')) {
                $table->integer('jumlah_pendaftar')->default(0)->after('sisa_kapasitas');
            }
        });
    }

    public function down()
    {
        Schema::table('jadwal_bimbingans', function (Blueprint $table) {
            if (Schema::hasColumn('jadwal_bimbingans', 'jumlah_pendaftar')) {
                $table->dropColumn('jumlah_pendaftar');
            }
        });
    }
};
