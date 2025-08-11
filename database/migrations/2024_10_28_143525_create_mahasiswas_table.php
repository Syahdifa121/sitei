<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mahasiswas', function (Blueprint $table) {
            $table->string('nim')->primary();
            $table->string('nama');
            $table->integer('angkatan');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('foto')->nullable();
            $table->string('fcm_token')->nullable();
            $table->datetime('created_at');
            $table->datetime('updated_at');
            $table->unsignedBigInteger('prodi_id');
            $table->unsignedBigInteger('konsentrasi_id');
            $table->unsignedBigInteger('role_id');
            $table->text('google_access_token')->nullable();
            $table->text('google_refresh_token')->nullable();
            $table->integer('google_token_expires_in')->nullable();
            $table->timestamp('google_token_created_at')->nullable();

            $table->foreign('prodi_id')->references('id')->on('prodi');
            $table->foreign('konsentrasi_id')->references('id')->on('konsentrasi');
            $table->foreign('role_id')->references('id')->on('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mahasiswas');
    }
};
