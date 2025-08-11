<?php

namespace App\Http\Controllers;

use App\Models\JadwalBimbingan;
use App\Models\BookingBimbingan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    public function bookJadwal(Request $request, $jadwalId)
    {
        try {
            $jadwal = JadwalBimbingan::findOrFail($jadwalId);
            
            // Cek apakah jadwal masih tersedia
            if ($jadwal->status !== 'tersedia') {
                return response()->json([
                    'success' => false,
                    'message' => 'Jadwal tidak tersedia'
                ], 400);
            }
            
            // Cek apakah kuota masih ada
            if ($jadwal->has_kuota_limit && $jadwal->jumlah_pendaftar >= $jadwal->kapasitas) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kuota sudah penuh'
                ], 400);
            }
            
            // Cek apakah mahasiswa sudah terdaftar
            $existingBooking = BookingBimbingan::where('jadwal_id', $jadwalId)
                ->where('nim', Auth::user()->nim)
                ->where('status_booking', 'aktif')
                ->first();
                
            if ($existingBooking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah terdaftar pada jadwal ini'
                ], 400);
            }
            
            DB::beginTransaction();
            try {
                // Buat booking baru
                $booking = BookingBimbingan::create([
                    'jadwal_id' => $jadwalId,
                    'nim' => Auth::user()->nim,
                    'status_booking' => 'aktif'
                ]);
                
                // Update status jadwal
                $jadwal->updateStatus();
                
                DB::commit();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Berhasil mendaftar bimbingan'
                ]);
                
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
            
        } catch (\Exception $e) {
            Log::error('Error booking jadwal: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mendaftar bimbingan'
            ], 500);
        }
    }
    
    public function cancelBooking($bookingId)
    {
        try {
            $booking = BookingBimbingan::where('id', $bookingId)
                ->where('nim', Auth::user()->nim)
                ->firstOrFail();
                
            DB::beginTransaction();
            try {
                // Update status booking menjadi dibatalkan
                $booking->status_booking = 'dibatalkan';
                $booking->save();
                
                // Update status jadwal
                $booking->jadwal->updateStatus();
                
                DB::commit();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Berhasil membatalkan pendaftaran'
                ]);
                
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
            
        } catch (\Exception $e) {
            Log::error('Error canceling booking: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membatalkan pendaftaran'
            ], 500);
        }
    }
    
    // Method untuk melihat daftar booking mahasiswa
    public function myBookings()
    {
        $bookings = BookingBimbingan::with(['jadwal.dosen'])
            ->where('nim', Auth::user()->nim)
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('mahasiswa.booking.index', compact('bookings'));
    }
}