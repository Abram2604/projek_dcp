<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CekAksesManajemen
{
    public function handle(Request $request, Closure $next): Response
    {
        $jabatan = session('user_jabatan'); 
        $divisi  = session('user_divisi'); 
        $isKetua      = $jabatan === 'Ketua DPC';
        $isSekretaris = $jabatan === 'Sekretaris';
        $isOrganisasi = str_contains($divisi, 'Organisasi');

        if ($isKetua || $isSekretaris || $isOrganisasi) {
            return $next($request);
        }
        return redirect('/dashboard')->with('error', 'Anda tidak memiliki hak akses ke Manajemen Anggota!');
    }
}