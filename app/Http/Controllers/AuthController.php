<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\Mahasiswa;
use App\Models\Dosen;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $identifier = $request->username;
        $password = $request->password;

        // Cek mahasiswa
        $mahasiswa = Mahasiswa::where('nim', $identifier)->first();
        if ($mahasiswa && Hash::check($password, $mahasiswa->password)) {
            Auth::guard('mahasiswa')->login($mahasiswa);
            session(['role' => 'mahasiswa']);
            Log::info('Login berhasil untuk mahasiswa: ' . $mahasiswa->nim);
            return redirect('/alumni?tab=performa-alumni');
        }

        // Cek dosen
        $dosen = Dosen::where('nip', $identifier)->first();
        if ($dosen && Hash::check($password, $dosen->password)) {
            Auth::guard('dosen')->login($dosen);
            session(['role' => 'dosen']);
            Log::info('Login berhasil untuk dosen: ' . $dosen->nip);
            return redirect('/dosen/alumni?tab=performa-alumni');
        }

        // Cek admin
        $admin = Admin::where('nip', $identifier)->first();
        if ($admin && Hash::check($password, $admin->password)) {
            Auth::guard('admin')->login($admin);
            session(['role' => 'admin']);
            Log::info('Login berhasil untuk admin: ' . $admin->nip);
            return redirect('/admin/alumni?tab=statistik-alumni');
        }

        // Jika login gagal
        Log::warning('Login gagal untuk: ' . $identifier);
        return back()
            ->withInput($request->only('username'))
            ->withErrors([
                'login' => 'NIP/NIM atau password salah.'
            ]);
    }

    public function logout(Request $request)
    {
        if (Auth::guard('mahasiswa')->check()) {
            Auth::guard('mahasiswa')->logout();
        } else if (Auth::guard('dosen')->check()) {
            Auth::guard('dosen')->logout();
        } else if (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
        }
        
        return redirect()->route('login');
    }
}