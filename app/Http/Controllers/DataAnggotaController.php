<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Exports\DataAnggotaExport;
use Maatwebsite\Excel\Facades\Excel;

class DataAnggotaController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        // 1. Ambil Data PUK
        $dataPuk = DB::select('CALL sp_puk_list(?)', [$search]);
        
        // 2. Hitung Total (Untuk Footer Tabel)
        // Kita hitung berdasarkan kolom 'jumlah_anggota' (data real dari PUK Anak)
        $totalAnggota = 0;
        foreach($dataPuk as $p) {
            $totalAnggota += $p->jumlah_anggota;
        }

        // 3. Ambil Data Tanda Tangan (Row pertama)
        $ttd = DB::table('Pengaturan_Tanda_Tangan')->first();

        // 4. Cek Akses (Hanya Ketua, Sekretaris, atau level BPH yang bisa edit)
        $jabatan = session('user_jabatan');
        $canEdit = ($jabatan === 'Ketua DPC' || $jabatan === 'Sekretaris' || session('user_level') === 'BPH');

        return view('pages.data_anggota.index', compact('dataPuk', 'totalAnggota', 'canEdit', 'search', 'ttd'));
    }

    public function store(Request $request)
    {
        // Validasi Input
        $request->validate([
            'nama_perusahaan' => 'required|string',
            'jumlah_anggota' => 'required|integer',
            'manual_total_anggota' => 'nullable|integer' // Validasi untuk kolom baru
        ]);

        // Panggil SP Create dengan 10 Parameter
        DB::statement('CALL sp_puk_create(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
            $request->nama_perusahaan,
            $request->no_pencatatan,
            $request->jumlah_anggota,
            $request->hasil_verifikasi ?? 0,
            $request->nama_federasi,
            $request->no_pencatatan_federasi,
            $request->afiliasi ?? 'KSPSI',
            $request->nama_ketua,
            $request->nama_sekretaris,
            $request->manual_total_anggota // Parameter ke-10 (Baru)
        ]);

        return redirect()->back()->with('success', 'Data PUK berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        // Validasi Input
        $request->validate([
            'nama_perusahaan' => 'required', 
            'jumlah_anggota' => 'required',
            'manual_total_anggota' => 'nullable|integer' // Validasi untuk kolom baru
        ]);

        // Panggil SP Update dengan 11 Parameter (ID + 10 Data)
        DB::statement('CALL sp_puk_update(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
            $id,
            $request->nama_perusahaan,
            $request->no_pencatatan,
            $request->jumlah_anggota,
            $request->hasil_verifikasi ?? 0,
            $request->nama_federasi,
            $request->no_pencatatan_federasi,
            $request->afiliasi ?? 'KSPSI',
            $request->nama_ketua,
            $request->nama_sekretaris,
            $request->manual_total_anggota // Parameter Terakhir (Baru)
        ]);

        return redirect()->back()->with('success', 'Data PUK berhasil diperbarui.');
    }
    
    public function destroy($id)
    {
        DB::statement('CALL sp_puk_delete(?)', [$id]);
        return redirect()->back()->with('success', 'Data PUK dihapus.');
    }

    // --- FITUR BARU: EXPORT EXCEL VIA BACKEND ---
    public function exportExcel(Request $request)
    {
        $search = $request->input('search');
        
        // 1. Ambil Data
        $dataPuk = DB::select('CALL sp_puk_list(?)', [$search]);
        
        // 2. Hitung Total
        $totalAnggota = 0;
        foreach($dataPuk as $p) $totalAnggota += $p->jumlah_anggota;
        
        // 3. Ambil Tanda Tangan
        $ttd = DB::table('Pengaturan_Tanda_Tangan')->first();

        // 4. Download Excel menggunakan Class Export
        return Excel::download(new DataAnggotaExport($dataPuk, $totalAnggota, $ttd), 'Data_Anggota_DPC_SPSI_'.date('Y').'.xlsx');
    }

    // --- FITUR UPDATE: ATUR TTD + UPLOAD GAMBAR ---
    public function updateTtd(Request $request)
    {
        // Data Teks
        $data = [
            'kadis_nama' => $request->kadis_nama,
            'kadis_nip' => $request->kadis_nip,
            'ketua_nama' => $request->ketua_nama,
            'sekretaris_nama' => $request->sekretaris_nama,
            'kota_surat' => $request->kota_surat,
            'updated_at' => now()
        ];

        // Handle File Upload (Jika ada gambar baru diupload)
        if ($request->hasFile('ttd_kadis')) {
            $data['path_ttd_kadis'] = $request->file('ttd_kadis')->store('ttd', 'public');
        }
        if ($request->hasFile('ttd_ketua')) {
            $data['path_ttd_ketua'] = $request->file('ttd_ketua')->store('ttd', 'public');
        }
        if ($request->hasFile('ttd_sekretaris')) {
            $data['path_ttd_sekretaris'] = $request->file('ttd_sekretaris')->store('ttd', 'public');
        }

        // Update Database
        DB::table('Pengaturan_Tanda_Tangan')->where('id', 1)->update($data);

        return redirect()->back()->with('success', 'Format tanda tangan & gambar berhasil diperbarui.');
    }
}