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
        if (!Schema::hasTable('booking_bimbingan')) {
            Schema::create('booking_bimbingan', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('jadwal_id');
                $table->string('nim');
                $table->enum('status_booking', ['aktif', 'dibatalkan'])->default('aktif');
                $table->timestamps();

                // Foreign keys
                $table->foreign('jadwal_id')->references('id')->on('jadwal_bimbingans')->onDelete('cascade');
                $table->foreign('nim')->references('nim')->on('mahasiswas')->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('booking_bimbingan');
    }
};
