<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Anggota; // Pastikan Model Anggota di-import

class AuthController extends Controller
{
    // Menampilkan Halaman Login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Proses Login (Mode Dummy / Bypass)
    public function login(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        // 2. DAFTAR AKUN DUMMY (Untuk Testing UI)
        // Format: 'username' => 'Nama Jabatan'
        $dummy_accounts = [
            'ketua'      => 'Ketua',
            'sekretaris' => 'Sekretaris',
            'bendahara'  => 'Bendahara',
            'organisasi' => 'Organisasi',
            'advokasi'   => 'Advokasi',
            'anggota'    => 'Anggota Biasa'
        ];

        $inputUser = $request->username;
        $inputPass = $request->password;

        // 3. LOGIKA LOGIN SEMENTARA (BYPASS)
        // Jika username ada di daftar dummy DAN passwordnya '123'
        if (array_key_exists($inputUser, $dummy_accounts) && $inputPass == '123') {
            
            // Cari user di database, kalau tidak ada -> Buat baru otomatis!
            // Ini biar Session Laravel tidak error
            $user = Anggota::firstOrCreate(
                ['username' => $inputUser],
                [
                    'nama_lengkap'  => $dummy_accounts[$inputUser] . ' DPC', // Contoh: Ketua DPC
                    'password_hash' => bcrypt('123'), // Isi formalitas aja
                    'status_aktif'  => 1,
                    // Kita simpan jabatannya di kolom 'email' sementara (hack) 
                    // atau kolom lain yg kosong biar sidebar tau dia role apa
                    'email'         => $dummy_accounts[$inputUser] 
                ]
            );

            // Login Paksa
            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->intended('dashboard');
        }

        // 4. Jika login gagal
        return back()->withErrors([
            'username' => 'Login Gagal. Coba user: ketua / pass: 123',
        ]);
    }

    // Proses Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}