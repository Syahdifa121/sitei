<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Mahasiswa;
use App\Models\Dosen;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::user();
                if ($user instanceof Mahasiswa) {
                    return redirect()->route('mahasiswa.usulanbimbingan');
                } elseif ($user instanceof Dosen) {
                    return redirect()->route('dosen.persetujuan');
                } elseif ($user instanceof Admin) {
                    return redirect()->route('admin.statistik-alumni');
                }
            }
        }

        return $next($request);
    }
}