<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CekAksesManajemen
{
    public function handle(Request $request, Closure $next): Response
    {
        // Ambil data jabatan & divisi dari session (disimpan saat login)
        $jabatan = session('user_jabatan'); // Contoh: 'Ketua DPC', 'Sekretaris'
        $divisi  = session('user_divisi');  // Contoh: 'Bidang Organisasi'

        // LOGIC IZIN AKSES:
        // 1. Ketua DPC
        // 2. Sekretaris
        // 3. Orang yang berada di Bidang Organisasi
        $isKetua      = $jabatan === 'Ketua DPC';
        $isSekretaris = $jabatan === 'Sekretaris';
        $isOrganisasi = str_contains($divisi, 'Organisasi'); // Cek jika ada kata 'Organisasi'

        if ($isKetua || $isSekretaris || $isOrganisasi) {
            return $next($request); // Silakan masuk
        }

        // Jika tidak punya akses, tendang ke dashboard dengan pesan error
        return redirect('/dashboard')->with('error', 'Anda tidak memiliki hak akses ke Manajemen Anggota!');
    }
}