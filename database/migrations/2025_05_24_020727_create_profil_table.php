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
        Schema::create('profil', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('user_nim');
            $table->foreign('user_nim')->references('nim')->on('mahasiswas')->onDelete('cascade');
    
            $table->string('foto')->nullable();
            $table->string('nama')->nullable();
            $table->string('email')->nullable();
            $table->string('no_telepon')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('portfolio')->nullable();
            $table->text('deskripsi_diri')->nullable();

            $table->json('pengalaman')->nullable();
            $table->json('pendidikan')->nullable();
            $table->json('organisasi')->nullable();
            
            $table->text('hard_skill')->nullable();
            $table->text('soft_skill')->nullable();

            $table->string('cv_path')->nullable();
    
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profil');
    }
};
