<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Konsentrasi extends Model
{
    use HasFactory;
    protected $table = 'konsentrasi';
    protected $primaryKey = 'id';
    protected $fillable = [
        'konsentrasi_id',
        'nama_konsentrasi'
    ];
}
