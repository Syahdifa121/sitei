<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profil extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'profil';

    protected $fillable = [
        'foto',
        'nama',
        'email',
        'no_telepon',
        'linkedin',
        'portfolio',
        'deskripsi_diri',
        'hard_skill',
        'soft_skill',
        'cv_path',
    ];

    protected $casts = [
        'pengalaman' => 'array',
        'pendidikan' => 'array',
    ];
}
