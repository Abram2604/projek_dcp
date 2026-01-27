<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProgjaController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $levelAkses = session('user_level'); // BPH, KORBID, ANGGOTA
        
        // 1. Logika Filter Divisi
        // Jika BPH, filter berdasarkan inputan 'divisi_id' (dari dropdown filter).
        // Jika Anggota/Korbid, filter terkunci ke id_divisi mereka sendiri.
        $filterDivisi = ($levelAkses === 'BPH') ? $request->input('divisi_id') : $user->id_divisi;
        
        $search = $request->input('search');

        // 2. Ambil List Progja (Stored Procedure menghandle NULL sebagai "Semua")
        $progja = DB::select('CALL sp_progja_list(?, ?)', [$filterDivisi, $search]);

        // 3. Ambil Statistik
        $statsRaw = DB::select('CALL sp_progja_stats(?)', [$filterDivisi]);
        $stats = $statsRaw[0];

        // 4. [BARU] Ambil Evaluasi BPH Terbaru
        // Mengambil data evaluasi terakhir berdasarkan filter divisi yang dipilih
        $evaluasiRaw = DB::select('CALL sp_evaluasi_get_latest(?)', [$filterDivisi]);
        $evaluasi = $evaluasiRaw[0] ?? null; // Bisa null jika belum ada evaluasi

        // 5. Ambil List Divisi (Untuk BPH: Dropdown Filter & Modal Input)
        $divisiList = [];
        if($levelAkses === 'BPH'){
            $divisiList = DB::select('CALL sp_divisi_list()');
        }

        // Kirim variabel baru (evaluasi & filterDivisi) ke View
        return view('pages.progja.index', compact(
            'progja', 
            'stats', 
            'levelAkses', 
            'divisiList', 
            'evaluasi', 
            'filterDivisi'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_program' => 'required',
            'tanggal_selesai' => 'required|date',
            'anggaran' => 'required|numeric'
        ]);

        $user = Auth::user();
        // Jika BPH, ambil dari input form, jika Divisi, ambil otomatis dari akun
        $idDivisi = ($request->input('id_divisi')) ? $request->input('id_divisi') : $user->id_divisi;

        DB::statement('CALL sp_progja_create(?, ?, ?, ?, ?)', [
            $idDivisi,
            $request->nama_program,
            $request->tanggal_selesai,
            $request->anggaran,
            $user->id
        ]);

        return redirect()->back()->with('success', 'Program kerja berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'persen_progress' => 'required|integer|min:0|max:100',
            'status_proker' => 'required'
        ]);

        DB::statement('CALL sp_progja_update_progress(?, ?, ?)', [
            $id,
            $request->persen_progress,
            $request->status_proker
        ]);

        return redirect()->back()->with('success', 'Progress berhasil diperbarui.');
    }

    public function destroy($id)
    {
        DB::statement('CALL sp_progja_delete(?)', [$id]);
        return redirect()->back()->with('success', 'Program kerja dihapus.');
    }

    // [BARU] Fungsi Simpan Evaluasi BPH
    public function storeEvaluasi(Request $request)
    {
        // Validasi input
        $request->validate([
            'id_divisi' => 'required|integer',
            'isi_evaluasi' => 'required|string',
        ]);

        // Panggil SP Create Evaluasi
        DB::statement('CALL sp_evaluasi_create(?, ?, ?, ?)', [
            $request->id_divisi,
            Auth::id(), // ID BPH yang menulis
            $request->isi_evaluasi,
            now()->toDateString() // Tanggal hari ini
        ]);

        return redirect()->back()->with('success', 'Evaluasi BPH berhasil ditambahkan.');
    }
}