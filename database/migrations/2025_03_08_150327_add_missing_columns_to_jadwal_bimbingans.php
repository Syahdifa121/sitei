<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('jadwal_bimbingans', function (Blueprint $table) {
            if (!Schema::hasColumn('jadwal_bimbingans', 'jenis_bimbingan')) {
                $table->string('jenis_bimbingan')->nullable();
            }
            
            if (!Schema::hasColumn('jadwal_bimbingans', 'has_kuota_limit')) {
                $table->boolean('has_kuota_limit')->default(false);
            }
        });
    }

    public function down()
    {
        Schema::table('jadwal_bimbingans', function (Blueprint $table) {
            $table->dropColumn(['jenis_bimbingan', 'has_kuota_limit']);
        });
    }
};