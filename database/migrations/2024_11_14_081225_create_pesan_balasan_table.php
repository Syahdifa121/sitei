<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pesan_balasan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesan_id')
                  ->constrained('pesan')
                  ->onDelete('cascade');
            $table->foreignId('role_id')      // Menggunakan role_id
                  ->constrained('role')
                  ->onDelete('cascade');
            $table->string('pengirim_id');    // nim atau nip
            $table->text('pesan');
            $table->string('attachment')->nullable(); // link google drive
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            // Index untuk pencarian
            $table->index('pengirim_id');
            $table->index('role_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pesan_balasan');
    }
};