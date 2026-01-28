<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanKeuanganController extends Controller {
    
    public function index(Request $request) {
        $jabatan = session('user_jabatan');
        // Izinkan BPH mengakses
        if (session('user_level') !== 'BPH') {
            abort(403, 'Akses Ditolak');
        }

        $bulan = $request->input('bulan', Carbon::now()->month);
        $tahun = $request->input('tahun', Carbon::now()->year);
        $type = $request->input('type', 'flow');

        $data = $this->getData($bulan, $tahun);
        $ttd  = DB::table('Pengaturan_Tanda_Tangan')->first();

        return view('pages.laporan_keuangan.partials.index', compact('data', 'bulan', 'tahun', 'type', 'ttd'));
    }

    public function store(Request $request) {
        // Simpan Data Keuangan (JSON)
        DB::table('Laporan_Keuangan_Rekap')->updateOrInsert(
            ['bulan' => $request->bulan, 'tahun' => $request->tahun],
            [
                'saldo_awal' => $request->saldoAwal,
                'income_cos' => json_encode($request->incomeCos),
                'income_non_cos' => json_encode($request->incomeNonCos),
                'expenses' => json_encode($request->expenses),
                'assets' => json_encode($request->assets),
                'updated_at' => now()
            ]
        );
        return back()->with('success', 'Data keuangan berhasil diperbarui.');
    }

    // --- UPDATE TTD (GAMBAR & TEKS) ---
    public function updateTtd(Request $request) {
        $data = [
            'ketua_nama' => $request->ketua_nama,
            'sekretaris_nama' => $request->sekretaris_nama,
            'bendahara_nama' => $request->bendahara_nama,
            'kota_surat' => $request->kota_surat,
            'updated_at' => now()
        ];

        // Handle Upload Gambar
        if($request->hasFile('ttd_ketua')) {
            $data['path_ttd_ketua'] = $request->file('ttd_ketua')->store('ttd', 'public');
        }
        if($request->hasFile('ttd_sekretaris')) {
            $data['path_ttd_sekretaris'] = $request->file('ttd_sekretaris')->store('ttd', 'public');
        }
        if($request->hasFile('ttd_bendahara')) {
            $data['path_ttd_bendahara'] = $request->file('ttd_bendahara')->store('ttd', 'public');
        }

        DB::table('Pengaturan_Tanda_Tangan')->updateOrInsert(['id' => 1], $data);

        return back()->with('success', 'Pengaturan Tanda Tangan disimpan.');
    }

    // --- EXPORT PDF ---
    public function exportPdf(Request $request) {
        $bulan = $request->input('bulan', Carbon::now()->month);
        $tahun = $request->input('tahun', Carbon::now()->year);
        $type  = $request->input('type', 'flow');

        $data = $this->getData($bulan, $tahun);
        $ttd  = DB::table('Pengaturan_Tanda_Tangan')->first();
        $namaBulan = Carbon::create()->month($bulan)->isoFormat('MMMM');

        $pdf = Pdf::loadView('exports.pdf_laporan_strategis', compact('data', 'bulan', 'tahun', 'type', 'ttd', 'namaBulan'))
                  ->setPaper('a4', 'portrait');

        return $pdf->download('Laporan_Keuangan_'.$type.'_'.$bulan.'_'.$tahun.'.pdf');
    }

    private function getData($bulan, $tahun) {
        $rekap = DB::table('Laporan_Keuangan_Rekap')
            ->where('bulan', $bulan)->where('tahun', $tahun)->first();

        // Data Default jika belum ada
        return $rekap ? [
            'incomeCos' => json_decode($rekap->income_cos, true),
            'incomeNonCos' => json_decode($rekap->income_non_cos, true),
            'expenses' => json_decode($rekap->expenses, true),
            'assets' => json_decode($rekap->assets, true),
            'saldoAwal' => $rekap->saldo_awal
        ] : [
            'incomeCos' => ['kiic'=>0, 'kim'=>0, 'kisc'=>0, 'luar'=>0],
            'incomeNonCos' => ['adminBank'=>0, 'donasi'=>0],
            'expenses' => ['operasional'=>0, 'bidang1'=>0, 'bidang2'=>0, 'bidang3'=>0, 'bidang4'=>0, 'bidang5'=>0, 'sekretariat'=>0, 'insentif'=>0],
            'assets' => ['bni'=>0, 'kas'=>0, 'advSekretariat'=>0, 'advBph'=>0, 'advLain'=>0],
            'saldoAwal' => 0
        ];
    }
}