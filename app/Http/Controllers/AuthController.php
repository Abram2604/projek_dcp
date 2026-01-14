<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Anggota;

class AuthController extends Controller
{
    /**
     * Menampilkan Halaman Login
     */
    public function showLoginForm()
    {
        // Jika user sudah login, langsung lempar ke dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    /**
     * Proses Login Utama
     */
    public function login(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        try {
            // 2. Cari User Menggunakan Eloquent
            // Kita pakai Eloquent (Model Anggota) agar Auth::login bekerja sempurna mencatat sesi
            $user = Anggota::where('username', $request->username)->first();

            // 3. Cek Apakah User Ditemukan & Password Hash Cocok
            // Kita pakai Hash::check karena password di database sudah di-bcrypt
            if ($user && Hash::check($request->password, $user->password_hash)) {
                
                // 4. Proses Login ke Sistem Laravel
                // Ini akan membuat File/Database Session untuk user ini
                Auth::login($user); 
                
                // 5. Regenerasi ID Sesi (Wajib untuk keamanan / mencegah Fixation Attack)
                $request->session()->regenerate();

                // 6. Ambil Data Tambahan (Jabatan & Divisi) secara Manual
                // Kita ambil pakai Query Builder untuk memastikan data ada, 
                // jaga-jaga jika relasi di Model belum dibuat.
                $jabatan = DB::table('Jabatan')->where('id', $user->id_jabatan)->first();
                $divisi  = DB::table('Divisi')->where('id', $user->id_divisi)->first();

                // 7. Simpan Hak Akses ke dalam Session
                // Data ini akan dipakai oleh Sidebar untuk menampilkan/menyembunyikan menu
                session([
                    'user_jabatan' => $jabatan ? $jabatan->nama_jabatan : 'Anggota',
                    'user_level'   => $jabatan ? $jabatan->level_akses : 'ANGGOTA', // BPH / KORBID / ANGGOTA
                    'user_divisi'  => $divisi  ? $divisi->nama_divisi : '-',
                ]);

                // 8. Redirect ke Dashboard
                return redirect()->intended('dashboard');
            }

            // Jika Username tidak ada ATAU Password salah
            return back()->withErrors([
                'username' => 'Username atau password salah.',
            ])->onlyInput('username');

        } catch (\Exception $e) {
            // Tangkap Error jika ada masalah koneksi database dll
            return back()->withErrors(['username' => 'System Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Proses Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        // Hapus sesi
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login');
    }
}