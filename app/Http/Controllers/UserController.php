<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil data via SP
        $search = $request->input('search');
        
        // Panggil SP: sp_anggota_list(p_search, p_status_aktif)
        // Kita set p_status_aktif = NULL agar semua (aktif & non-aktif) muncul, atau 1 jika mau aktif saja
        $users = DB::select('CALL sp_anggota_list(?, ?)', [$search, null]);

        // 2. Ambil data master untuk Dropdown (Divisi & Jabatan)
        $divisi = DB::select('CALL sp_divisi_list()');
        // Anggap tabel Jabatan sederhana, kita query biasa saja
        $jabatan = DB::table('Jabatan')->orderBy('nama_jabatan', 'asc')->get();

        return view('pages.users.index', compact('users', 'divisi', 'jabatan'));
    }
    public function activate($id)
{
    // Kita update manual saja query-nya agar simpel
    DB::update('UPDATE Anggota SET status_aktif = 1, diupdate_pada = NOW() WHERE id = ?', [$id]);
    
    return redirect()->back()->with('success', 'User berhasil diaktifkan kembali.');
}

    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required',
            'username'     => 'required|unique:Anggota,username',
            'password'     => 'required|min:6',
            'id_jabatan'   => 'required',
        ]);

        try {
            // Hash Password Laravel
            $passwordHash = Hash::make($request->password);

            // Panggil SP Create (Otomatis generate QR di database)
            DB::statement('CALL sp_anggota_create(?, ?, ?, ?, ?, ?, ?)', [
                $request->nama_lengkap,
                $request->username,
                $passwordHash,
                $request->email,
                $request->nomor_hp,
                $request->id_divisi, // Bisa null
                $request->id_jabatan
            ]);

            return redirect()->back()->with('success', 'Anggota berhasil ditambahkan & QR Code dibuat.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambah anggota: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            DB::statement('CALL sp_anggota_update(?, ?, ?, ?, ?, ?, ?)', [
                $id,
                $request->nama_lengkap,
                $request->username,
                $request->email,
                $request->nomor_hp,
                $request->id_divisi,
                $request->id_jabatan
            ]);
            
            return redirect()->back()->with('success', 'Data anggota berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        // Soft Delete via SP
        DB::statement('CALL sp_anggota_soft_delete(?)', [$id]);
        return redirect()->back()->with('success', 'Anggota dinonaktifkan (Soft Delete).');
    }

    public function resetPassword(Request $request, $id)
    {
        $request->validate(['new_password' => 'required|min:6']);
        
        $hash = Hash::make($request->new_password);
        DB::statement('CALL sp_anggota_reset_password(?, ?)', [$id, $hash]);

        return redirect()->back()->with('success', 'Password berhasil direset.');
    }

    public function generateQr($id)
    {
        // Regenerate QR untuk user lama
        DB::statement('CALL sp_anggota_generate_qr(?)', [$id]);
        return redirect()->back()->with('success', 'QR Code berhasil digenerate ulang.');
    }
}