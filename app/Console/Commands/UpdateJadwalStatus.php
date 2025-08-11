<?php

namespace App\Console\Commands;

use App\Models\JadwalBimbingan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateJadwalStatus extends Command
{
    protected $signature = 'jadwal:update-status';
    protected $description = 'Update status jadwal bimbingan secara otomatis';

    public function handle()
    {
        $this->info('Memulai update status jadwal...');
        
        $jadwals = JadwalBimbingan::all();
        $count = 0;
        
        foreach ($jadwals as $jadwal) {
            $oldStatus = $jadwal->status;
            
            // Hitung jumlah pendaftar aktif
            $pendaftarCount = DB::table('usulan_bimbingans')
                ->where('event_id', $jadwal->event_id)
                ->whereIn('status', ['USULAN', 'DITERIMA', 'DISETUJUI'])
                ->count();
            
            // Update jumlah pendaftar
            $jadwal->jumlah_pendaftar = $pendaftarCount;
            $jadwal->updateStatus();
            
            if ($oldStatus !== $jadwal->status) {
                $count++;
                $this->line("Jadwal ID: {$jadwal->id} status berubah dari '{$oldStatus}' menjadi '{$jadwal->status}'");
            }
        }
        
        $this->info("Berhasil mengupdate status {$count} jadwal dari total {$jadwals->count()} jadwal");
    }
}