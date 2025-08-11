<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; 
use Illuminate\Database\Eloquent\Model;

class Admin extends Authenticatable
{
    use HasFactory;
    protected $table = 'admin';
    protected $primaryKey = 'nip';
    protected $keyType = 'string';

    protected $fillable = [
        'nip',
        'nama',
        'nama_singkat',
        'email',
        'password',
        'foto',
        'role_id',
    ];

    protected $hidden = [
        'password',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class,'role_id', 'id');
    }

    public function hasRole($roleName)
    {
        return $this->role && $this->role->role_akses === $roleName;
    }

    public function getFotoUrlAttribute()
    {
        if ($this->foto) {
            return asset('storage/foto_profil/' . $this->foto);
        }
        return asset('images/default-avatar.png');
    }
}
