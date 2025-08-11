<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('jadwal_bimbingans', function (Blueprint $table) {
            $table->id();
            $table->string('event_id')->unique();
            $table->string('nip');
            $table->dateTime('waktu_mulai');
            $table->dateTime('waktu_selesai');
            $table->text('catatan')->nullable();
            $table->enum('status', ['tersedia', 'tidak_tersedia', 'penuh'])->default('tersedia');
            $table->integer('kapasitas')->default(1);
            $table->integer('sisa_kapasitas')->default(1);
            $table->string('lokasi')->nullable();
            $table->string('jenis_bimbingan')->nullable()->default(null);
            $table->boolean('has_kuota_limit')->default(false);
            $table->timestamps();

            // Indexes
            $table->index('nip');
            $table->index('status');
            $table->index(['waktu_mulai', 'waktu_selesai']);
            $table->index('event_id');

            // Foreign keys
            $table->foreign('nip')->references('nip')->on('dosens')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('jadwal_bimbingans');
    }
};
