<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pesan extends Model
{
    use HasFactory;

    protected $table = 'pesan';

    protected $fillable = [
        'mahasiswa_nim',
        'dosen_nip',
        'subjek',
        'pesan',
        'prioritas', 
        'status', 
        'attachment', 
        'last_reply_by', 
        'last_reply_at'
    ];

    protected $casts = [
        'last_reply_at' => 'datetime',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_nim', 'nim');
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'dosen_nip', 'nip');
    }

    public function balasan()
    {
        return $this->hasMany(PesanBalasan::class, 'pesan_id');
    }

    // Scope untuk filter
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeSelesai($query)
    {
        return $query->where('status', 'selesai');
    }
}