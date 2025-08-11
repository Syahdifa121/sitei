<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JadwalBimbingan extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'nip',
        'waktu_mulai',
        'waktu_selesai',
        'catatan',
        'status',
        'kapasitas',
        'sisa_kapasitas',
        'lokasi',
        'jenis_bimbingan',    // Tambahkan ini
        'has_kuota_limit',    // Tambahkan ini
        'jumlah_pendaftar'    // Tambahkan ini
    ];

    protected $casts = [
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
        'kapasitas' => 'integer',
        'sisa_kapasitas' => 'integer',
        'jumlah_pendaftar' => 'integer',  // Tambahkan ini
        'has_kuota_limit' => 'boolean',   // Tambahkan ini
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Status constants (tambahkan konstanta baru)
    const STATUS_TERSEDIA = 'tersedia';
    const STATUS_TIDAK_TERSEDIA = 'tidak_tersedia';
    const STATUS_PENUH = 'penuh';
    const STATUS_SELESAI = 'selesai';          // Tambahkan ini
    const STATUS_DIBATALKAN = 'dibatalkan';    // Tambahkan ini

    // Status default
    protected $attributes = [
        'status' => self::STATUS_TERSEDIA,
        'kapasitas' => 1,
        'sisa_kapasitas' => 1,
        'jumlah_pendaftar' => 0,     // Tambahkan ini
        'has_kuota_limit' => false   // Tambahkan ini
    ];

    protected static function booted()
    {
        // Menambahkan observer untuk update status saat model disimpan
        static::saving(function ($jadwal) {
            // Update status berdasarkan pendaftar dan kapasitas
            if ($jadwal->has_kuota_limit && $jadwal->jumlah_pendaftar >= $jadwal->kapasitas) {
                $jadwal->status = self::STATUS_PENUH;
            } elseif (Carbon::parse($jadwal->waktu_selesai)->isPast()) {
                $jadwal->status = self::STATUS_SELESAI;
            } elseif ($jadwal->status !== self::STATUS_DIBATALKAN) {
                $jadwal->status = self::STATUS_TERSEDIA;
            }
        });
    }

    // Relasi dengan dosen
    public function dosen(): BelongsTo
    {
        return $this->belongsTo(Dosen::class, 'nip', 'nip');
    }

    // Relasi dengan bimbingan
    public function bimbingans(): HasMany
    {
        return $this->hasMany(Bimbingan::class, 'event_id', 'event_id');
    }

    // Tambahkan relasi dengan booking_bimbingan
    public function bookings(): HasMany
    {
        return $this->hasMany(BookingBimbingan::class, 'jadwal_id');
    }

    // Tambahkan method baru untuk mendapatkan jumlah booking aktif
    public function getActiveBookingsCount(): int
    {
        return $this->bookings()->where('status_booking', 'aktif')->count();
    }

    // Update method updateStatus untuk logika status yang lebih komprehensif
    public function updateStatus(): void
    {
        // Hitung jumlah pendaftar aktif dari usulan_bimbingans
        $pendaftarCount = DB::table('usulan_bimbingans')
            ->where('event_id', $this->event_id)
            ->whereIn('status', ['USULAN', 'DITERIMA', 'DISETUJUI'])
            ->count();

        // Update atribut jumlah_pendaftar
        $this->jumlah_pendaftar = $pendaftarCount;

        // Update sisa_kapasitas jika ada kuota limit
        if ($this->has_kuota_limit) {
            $this->sisa_kapasitas = max(0, $this->kapasitas - $pendaftarCount);

            // Jika pendaftar sudah mencapai atau melebihi kapasitas, status menjadi PENUH
            if ($pendaftarCount >= $this->kapasitas) {
                $this->status = self::STATUS_PENUH;
            }
            // Jika waktu jadwal sudah lewat, status menjadi SELESAI
            elseif (Carbon::parse($this->waktu_selesai)->isPast()) {
                $this->status = self::STATUS_SELESAI;
            }
            // Jika jadwal belum dibatalkan, status menjadi TERSEDIA
            elseif ($this->status !== self::STATUS_DIBATALKAN) {
                $this->status = self::STATUS_TERSEDIA;
            }
        } else {
            // Untuk jadwal tanpa kuota limit
            if (Carbon::parse($this->waktu_selesai)->isPast()) {
                $this->status = self::STATUS_SELESAI;
            } elseif ($this->status !== self::STATUS_DIBATALKAN) {
                $this->status = self::STATUS_TERSEDIA;
            }
        }

        // Debug log
        Log::info('Updating jadwal status:', [
            'id' => $this->id,
            'event_id' => $this->event_id,
            'old_status' => $this->getOriginal('status'),
            'new_status' => $this->status,
            'pendaftar_count' => $pendaftarCount,
            'kapasitas' => $this->kapasitas,
            'has_kuota_limit' => $this->has_kuota_limit
        ]);

        $this->save();
    }

    // Modifikasi method isAvailable
    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_TERSEDIA &&
            $this->sisa_kapasitas > 0 &&
            Carbon::parse($this->waktu_mulai)->isFuture();
    }

    // Modifikasi method decrementKapasitas
    public function decrementKapasitas(): bool
    {
        if ($this->sisa_kapasitas > 0) {
            $this->decrement('sisa_kapasitas');
            $this->increment('jumlah_pendaftar');

            if ($this->sisa_kapasitas === 0 && $this->has_kuota_limit) {
                $this->update(['status' => self::STATUS_PENUH]);
            }

            return true;
        }
        return false;
    }

    // Modifikasi method incrementKapasitas
    public function incrementKapasitas(): bool
    {
        if ($this->sisa_kapasitas < $this->kapasitas) {
            $this->increment('sisa_kapasitas');
            $this->decrement('jumlah_pendaftar');

            if ($this->status === self::STATUS_PENUH) {
                $this->update(['status' => self::STATUS_TERSEDIA]);
            }

            return true;
        }
        return false;
    }

    // Tambahkan method untuk membatalkan jadwal
    public function batalkan(): bool
    {
        $this->status = self::STATUS_DIBATALKAN;
        return $this->save();
    }

    // Scopes
    public function scopeTersedia($query)
    {
        return $query->where('status', self::STATUS_TERSEDIA)
            ->where('sisa_kapasitas', '>', 0)
            ->where('waktu_mulai', '>', now());
    }

    public function scopeByDosen($query, $nip)
    {
        return $query->where('nip', $nip);
    }

    // Accessor
    public function getWaktuLengkapAttribute(): string
    {
        return Carbon::parse($this->waktu_mulai)->isoFormat('dddd, D MMMM Y') .
            ' | ' .
            Carbon::parse($this->waktu_mulai)->format('H:i') .
            ' - ' .
            Carbon::parse($this->waktu_selesai)->format('H:i');
    }

    public function getDurasiAttribute(): int
    {
        return Carbon::parse($this->waktu_mulai)->diffInMinutes($this->waktu_selesai);
    }

    public function getIsSelesaiAttribute(): bool
    {
        return Carbon::parse($this->waktu_selesai)->isPast();
    }

    // Tambahkan accessor untuk status label yang lebih readable
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_TERSEDIA => 'Tersedia',
            self::STATUS_TIDAK_TERSEDIA => 'Tidak Tersedia',
            self::STATUS_PENUH => 'Penuh',
            self::STATUS_SELESAI => 'Selesai',
            self::STATUS_DIBATALKAN => 'Dibatalkan',
            default => 'Unknown'
        };
    }
}
