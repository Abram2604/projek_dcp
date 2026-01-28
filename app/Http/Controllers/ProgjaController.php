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
        $levelAkses = session('user_level');
        
        $filterDivisi = ($levelAkses === 'BPH') ? $request->input('divisi_id') : $user->id_divisi;
        $search = $request->input('search');

        $progja = DB::select('CALL sp_progja_list(?, ?)', [$filterDivisi, $search]);
        $statsRaw = DB::select('CALL sp_progja_stats(?)', [$filterDivisi]);
        $stats = $statsRaw[0];
        $evaluasiRaw = DB::select('CALL sp_evaluasi_get_latest(?)', [$filterDivisi]);
        $evaluasi = $evaluasiRaw[0] ?? null;

        $queryRapbo = DB::table('RAPBO')
                        ->join('Divisi', 'RAPBO.id_divisi', '=', 'Divisi.id')
                        ->select('RAPBO.*', 'Divisi.nama_divisi')
                        ->orderBy('RAPBO.id', 'asc');

        if ($filterDivisi) {
            $queryRapbo->where('RAPBO.id_divisi', $filterDivisi);
        }
        $rapboList = $queryRapbo->get();

        $divisiList = ($levelAkses === 'BPH') ? DB::select('CALL sp_divisi_list()') : [];

        $activeTab = $request->input('tab', 'progja');

        return view('pages.progja.index', compact(
            'progja', 'stats', 'levelAkses', 'divisiList', 'evaluasi', 'filterDivisi',
            'rapboList', 'activeTab' 
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_program' => 'required',
            'target' => 'required', // Baru
            'action' => 'required', // Baru
            'tanggal_selesai' => 'required|date',
            'anggaran' => 'required|numeric'
        ]);

        $user = Auth::user();
        $idDivisi = ($request->input('id_divisi')) ? $request->input('id_divisi') : $user->id_divisi;

        // Panggil SP dengan urutan parameter baru
        DB::statement('CALL sp_progja_create(?, ?, ?, ?, ?, ?, ?)', [
            $idDivisi,
            $request->nama_program,
            $request->target,       // Baru
            $request->action,       // Baru
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