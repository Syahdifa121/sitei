<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use Illuminate\Support\Facades\Auth;

class ProfilController extends Controller
{
    public function show()
    {
        // Cek guard 
        if (Auth::guard('mahasiswa')->check()) {
            $profile = Auth::guard('mahasiswa')->user();
            $role = 'mahasiswa';
        } 
        elseif (Auth::guard('dosen')->check()) {
            $profile = Auth::guard('dosen')->user();
            $role = 'dosen';
        } 
        elseif (Auth::guard('admin')->check()) {
            $profile = Auth::guard('admin')->user();
            $role = 'admin';
        }
        // Jika tidak terautentikasi di kedua guard
        else {
            return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu');
        }

        // Double check untuk memastikan $profile tidak null
        if (!$profile) {
            return redirect()->route('login')->with('error', 'Profil tidak ditemukan');
        }

        return view('bimbingan.mahasiswa.profilmahasiswa', [
            'profile' => $profile,
            'role' => $role
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        try {
            // Cek guard mahasiswa
            if (Auth::guard('mahasiswa')->check()) {
                $profile = Auth::guard('mahasiswa')->user();
                $identifier = $profile->nim;
            } 
            // Cek guard dosen
            elseif (Auth::guard('dosen')->check()) {
                $profile = Auth::guard('dosen')->user();
                $identifier = $profile->nip;
            } 
            // Cek guard admin
            elseif (Auth::guard('admin')->check()) {
                $profile = Auth::guard('admin')->user();
                $identifier = $profile->id;
            }
            // Jika tidak terautentikasi di kedua guard
            else {
                return redirect()->route('login')
                    ->with('error', 'Anda harus login terlebih dahulu');
            }

            // Double check untuk memastikan $profile tidak null
            if (!$profile) {
                throw new \Exception('Profil tidak ditemukan');
            }

            if ($request->hasFile('foto')) {
                // Hapus foto lama jika ada
                if ($profile->foto && Storage::disk('public')->exists('foto_profil/' . $profile->foto)) {
                    Storage::disk('public')->delete('foto_profil/' . $profile->foto);
                }

                // Upload foto baru
                $file = $request->file('foto');
                $filename = $identifier . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $file->storeAs('foto_profil', $filename, 'public');

                $profile->update(['foto' => $filename]);
            }

            return redirect()->back()->with('success', 'Foto profil berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui foto profil: ' . $e->getMessage());
        }
    }

    public function remove()
    {
        try {
            // Cek guard mahasiswa
            if (Auth::guard('mahasiswa')->check()) {
                $profile = Auth::guard('mahasiswa')->user();
            } 
            // Cek guard dosen
            elseif (Auth::guard('dosen')->check()) {
                $profile = Auth::guard('dosen')->user();
            } 
            // Cek guard admin
            elseif (Auth::guard('admin')->check()) {
                $profile = Auth::guard('admin')->user();
            }
            // Jika tidak terautentikasi di kedua guard
            else {
                return redirect()->route('login')
                    ->with('error', 'Anda harus login terlebih dahulu');
            }

            // Double check untuk memastikan $profile tidak null
            if (!$profile) {
                throw new \Exception('Profil tidak ditemukan');
            }

            if ($profile->foto && Storage::disk('public')->exists('foto_profil/' . $profile->foto)) {
                Storage::disk('public')->delete('foto_profil/' . $profile->foto);
            }

            $profile->update(['foto' => null]);

            return redirect()->back()->with('success', 'Foto profil berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus foto profil: ' . $e->getMessage());
        }
    }
}