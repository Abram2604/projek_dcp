<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CekAksesDataAnggota
{
    public function handle(Request $request, Closure $next): Response
    {
        // Ambil jabatan dari session
        $jabatan = session('user_jabatan');

        // Hanya izinkan Ketua DPC dan Sekretaris
        if ($jabatan === 'Ketua DPC' || $jabatan === 'Sekretaris') {
            return $next($request);
        }

        // Jika bukan, tendang ke dashboard
        return redirect()->route('dashboard')->with('error', 'Akses ditolak! Menu ini hanya untuk Ketua dan Sekretaris.');
    }
}