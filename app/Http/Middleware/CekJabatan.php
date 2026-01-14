<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CekJabatan
{
    public function handle(Request $request, Closure $next, ...$allowedLevels): Response
    {
        // 1. Ambil level user dari session (yang kita simpan di AuthController)
        $userLevel = session('user_level'); // Isinya: 'BPH', 'KORBID', atau 'ANGGOTA'

        // 2. Cek apakah level user ada di daftar yang dibolehkan
        if (in_array($userLevel, $allowedLevels)) {
            return $next($request); // Silakan masuk
        }

        // 3. Jika tidak boleh, tendang balik atau error 403
        abort(403, 'Maaf, Anda tidak punya akses ke halaman ini.');
    }
}