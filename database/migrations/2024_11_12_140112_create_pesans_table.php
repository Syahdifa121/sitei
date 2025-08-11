<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pesan', function (Blueprint $table) {
            $table->id();
            $table->string('mahasiswa_nim');
            $table->string('dosen_nip');
            $table->string('subjek');
            $table->text('pesan');
            $table->enum('prioritas', ['mendesak', 'umum']);
            $table->enum('status', ['aktif', 'selesai'])->default('aktif');
            $table->string('attachment')->nullable(); // link google drive
            $table->string('last_reply_by')->nullable();
            $table->timestamp('last_reply_at')->nullable();
            $table->timestamps();

            $table->foreign('mahasiswa_nim')
                  ->references('nim')
                  ->on('mahasiswas')
                  ->onDelete('cascade');

            $table->foreign('dosen_nip')
                  ->references('nip')
                  ->on('dosens')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pesan');
    }
};