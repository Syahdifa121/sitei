<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsulanBimbingan extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'nim',
        'nip',
        'event_id',
        'mahasiswa_nama',
        'dosen_nama',
        'jenis_bimbingan',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'lokasi',
        'status',       
        'keterangan',
        'nomor_antrian'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'waktu_mulai' => 'string', 
        'waktu_selesai' => 'string', 
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'nomor_antrian' => 'integer'
    ];

    protected $attributes = [
        'status' => self::STATUS_USULAN,
        'lokasi' => null,
        'keterangan' => null,
        'nomor_antrian' => 0
    ];

    // Status bimbingan
    const STATUS_USULAN = 'USULAN';
    const STATUS_DISETUJUI = 'DISETUJUI';
    const STATUS_DITOLAK = 'DITOLAK';
    const STATUS_SELESAI = 'SELESAI';

    // Relasi dengan model lain
    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'nim', 'nim');
    }

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(Dosen::class, 'nip', 'nip');
    }

    // Accessor 
    public function getWaktuLengkapAttribute(): string
    {
        return Carbon::parse($this->tanggal)->format('l, d F Y') . 
               ' | ' . 
               $this->waktu_mulai . ' - ' . $this->waktu_selesai;
    }

    public function setujui(?string $lokasi = null): bool
    {
        try {
            DB::beginTransaction();

            // Dapatkan nomor antrian terakhir untuk jadwal yang sama
            $lastQueue = self::where('event_id', $this->event_id)
                            ->where('status', self::STATUS_DISETUJUI)
                            ->max('nomor_antrian') ?? 0;

            // Set nomor antrian berikutnya
            $nextQueue = $lastQueue + 1;

            $updated = $this->update([
                'status' => self::STATUS_DISETUJUI,
                'lokasi' => $lokasi,
                'nomor_antrian' => $nextQueue
            ]);

            DB::commit();
            return $updated;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function tolak(string $keterangan): bool
    {
        return $this->update([
            'status' => self::STATUS_DITOLAK,
            'keterangan' => $keterangan,
            'nomor_antrian' => null  // Reset nomor antrian jika ditolak
        ]);
    }

    // Method untuk mendapatkan posisi antrian saat ini
    public function getQueuePosition(): int
    {
        if (!$this->nomor_antrian) {
            return 0;
        }

        return $this->nomor_antrian;
    }

    // Method untuk cek total antrian pada jadwal yang sama
    public function getTotalQueue(): int
    {
        return self::where('event_id', $this->event_id)
                  ->where('status', self::STATUS_DISETUJUI)
                  ->count();
    }

    // Method untuk mengatur ulang antrian jika ada pembatalan
    public static function reorderQueue(string $event_id): void
    {
        $bimbingans = self::where('event_id', $event_id)
                         ->where('status', self::STATUS_DISETUJUI)
                         ->orderBy('nomor_antrian')
                         ->get();

        $newQueue = 1;
        foreach ($bimbingans as $bimbingan) {
            $bimbingan->update(['nomor_antrian' => $newQueue]);
            $newQueue++;
        }
    }
}