<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Traits\HasGoogleCalendar;
use App\Traits\HasFcmNotification;

class Mahasiswa extends Authenticatable
{
    use HasGoogleCalendar;
    use HasFcmNotification;
    use HasFactory, Notifiable;
    protected $primaryKey = 'nim';
    protected $keyType = 'string';

    protected $fillable = [
        'nim',
        'nama',
        'angkatan',
        'email',
        'password',
        'foto',
        'prodi_id',
        'konsentrasi_id',
        'role_id',
        'status_alumni',
        'google_access_token',
        'google_refresh_token',
        'google_token_expires_in',
        'google_token_created_at'
    ];

    protected $hidden = [
        'password',
        'google_access_token',
        'google_refresh_token'
    ];

    protected $casts = [
        'google_token_created_at' => 'datetime',
        'google_token_expires_in' => 'integer',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class,'role_id','id');
    }

    public function prodi()
    {
        return $this->belongsTo(Prodi::class);
    }

    public function konsentrasi()
    {
        return $this->belongsTo(Konsentrasi::class);
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

    public function alumni()
    {
        return $this->hasOne(Alumni::class, 'user_nim', 'nim');
    }
}
