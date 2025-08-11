<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usulan_bimbingans', function (Blueprint $table) {
            $table->id();
            // Foreign keys
            $table->string('nim');
            $table->string('nip');
            $table->string('dosen_nama');
            $table->string('mahasiswa_nama');

            // Informasi bimbingan
            $table->enum('jenis_bimbingan', [
                'skripsi',
                'kp',
                'akademik',
                'konsultasi',
                'mbkm',
                'lainnya'
            ]);
            
            // Waktu bimbingan
            $table->date('tanggal');
            $table->time('waktu_mulai');
            $table->time('waktu_selesai');
            
            // Lokasi dan deskripsi
            $table->string('lokasi')->nullable();
            $table->text('deskripsi')->nullable();
            $table->integer('nomor_antrian')->nullable();
            
            // Status dan keterangan
            $table->enum('status', [
                'USULAN',
                'DISETUJUI',
                'DITOLAK',
                'SELESAI'
            ])->default('USULAN');
            $table->text('keterangan')->nullable();
            
            // Google Calendar integration 
            $table->string('event_id');
            
            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index('nim');
            $table->index('nip');
            $table->index(['tanggal', 'waktu_mulai']);
            $table->index('status');
            $table->index('event_id'); 
            
            // Foreign key constraints
            $table->foreign('nim')->references('nim')->on('mahasiswas');
            $table->foreign('nip')->references('nip')->on('dosens');
            $table->foreign('event_id')->references('event_id')->on('jadwal_bimbingans');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bimbingans');
    }
};