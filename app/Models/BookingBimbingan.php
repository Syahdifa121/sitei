<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingBimbingan extends Model
{
    protected $table = 'booking_bimbingan';
    
    protected $fillable = [
        'jadwal_id',
        'nim',
        'status_booking'
    ];

    public function jadwal()
    {
        return $this->belongsTo(JadwalBimbingan::class, 'jadwal_id');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'nim', 'nim');
    }
}