<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PesanBalasan extends Model
{
    use HasFactory;

    protected $table = 'pesan_balasan';

    protected $fillable = [
        'pesan_id',
        'role_id',      
        'pengirim_id', 
        'pesan',
        'attachment',  
        'is_read'
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function pesan()
    {
        return $this->belongsTo(Pesan::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // Method untuk mendapatkan pengirim berdasarkan role
    public function pengirim()
    {
        if ($this->role_id == 3) {
            return $this->belongsTo(Mahasiswa::class, 'pengirim_id', 'nim');
        } 
        return $this->belongsTo(Dosen::class, 'pengirim_id', 'nip');
    }
}