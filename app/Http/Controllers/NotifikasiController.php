<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // 1. Ambil Notifikasi (Pagination 15 per halaman)
        $notifikasi = DB::table('Notifikasi')
                        ->where('id_anggota', $userId)
                        ->orderBy('dibuat_pada', 'desc')
                        ->paginate(15);
        return view('pages.notifikasi.index', compact('notifikasi'));
    }
    
    // 2. Fungsi Tandai Satu Saja (Tombol Ceklis)
    public function markAsRead($id)
    {
        $userId = Auth::id();

        // Update status jadi 1 (sudah dibaca)
        DB::table('Notifikasi')
            ->where('id', $id)
            ->where('id_anggota', $userId)
            ->update([
                'is_read' => 1, 
                'diupdate_pada' => now()
            ]);

        // Tetap di halaman notifikasi (Refresh halaman)
        return back()->with('success', 'Notifikasi ditandai sudah dibaca.');
    }

    // 3. Fungsi Tandai Semua (Tombol "Baca Semua")
    public function markAllRead()
    {
        $userId = Auth::id();

        DB::table('Notifikasi')
            ->where('id_anggota', $userId)
            ->where('is_read', 0) // Hanya yang belum dibaca
            ->update([
                'is_read' => 1, 
                'diupdate_pada' => now()
            ]);

        return back()->with('success', 'Semua notifikasi telah ditandai sudah dibaca.');
    }

}