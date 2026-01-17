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

        // Ambil semua notifikasi (Pagination 10 per halaman)
        $notifikasi = DB::table('Notifikasi')
                        ->where('id_anggota', $userId)
                        ->orderBy('dibuat_pada', 'desc')
                        ->paginate(10);

        // Tandai semua sebagai sudah dibaca saat membuka halaman ini
        DB::table('Notifikasi')
            ->where('id_anggota', $userId)
            ->where('is_read', 0)
            ->update(['is_read' => 1]);

        return view('pages.notifikasi.index', compact('notifikasi'));
    }
}