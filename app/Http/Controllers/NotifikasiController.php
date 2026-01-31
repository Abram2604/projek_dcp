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

        // 2. Tandai semua sebagai "Sudah Dibaca" saat halaman dibuka
        DB::table('Notifikasi')
            ->where('id_anggota', $userId)
            ->where('is_read', 0)
            ->update(['is_read' => 1, 'diupdate_pada' => now()]);

        return view('pages.notifikasi.index', compact('notifikasi'));
    }
}