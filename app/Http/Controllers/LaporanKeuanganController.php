<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanKeuanganController extends Controller {
    
    public function index(Request $request) {
        if (session('user_level') !== 'BPH') { abort(403, 'Akses Ditolak'); }

        $bulan = $request->input('bulan', Carbon::now()->month);
        $tahun = $request->input('tahun', Carbon::now()->year);
        $type = $request->input('type', 'flow');

        $data = $this->getData($bulan, $tahun);
        $ttd  = DB::table('Pengaturan_Tanda_Tangan')->first();

        return view('pages.laporan_keuangan.partials.index', compact('data', 'bulan', 'tahun', 'type', 'ttd'));
    }

    public function store(Request $request) {
        // Ambil data lama agar tidak tertimpa null jika partial update
        $existing = DB::table('Laporan_Keuangan_Rekap')
            ->where('bulan', $request->bulan)
            ->where('tahun', $request->tahun)
            ->first();

        // 1. INPUT MANUAL UNTUK LAPORAN ORGANISASI (SUMMARY)
        $saldoAwal = $request->input('saldoAwal', $existing->saldo_awal ?? 0);
        $pemasukanCos = $request->input('pemasukanCos', 0);
        $pemasukanNonCos = $request->input('pemasukanNonCos', 0);
        $pengeluaranOps = $request->input('pengeluaranOps', 0);
        $pengeluaranEvent = $request->input('pengeluaranEvent', 0);
        $pengeluaranSekretariat = $request->input('pengeluaranSekretariat', 0);
        $pengeluaranInsentif = $request->input('pengeluaranInsentif', 0);

        // 2. MAPPING JSON DATA (Merge dengan data lama jika ada)
        $incomeCos = json_encode($request->input('incomeCos', json_decode($existing->income_cos ?? '[]', true)));
        $incomeNonCos = json_encode($request->input('incomeNonCos', json_decode($existing->income_non_cos ?? '[]', true)));
        $expenses = json_encode($request->input('expenses', json_decode($existing->expenses ?? '[]', true)));
        
        // ASSETS: Merge data aset dari form
        $assetData = $request->input('assets', []);
        $existingAssets = json_decode($existing->assets ?? '[]', true) ?? [];
        $mergedAssets = array_merge($existingAssets, $assetData);
        $assets = json_encode($mergedAssets);
        
        // LIABILITIES: Merge data liabilities (pengeluaran + modal) dari form
        $liabilityData = $request->input('liabilities', []);
        $existingLiabilities = json_decode($existing->liabilities ?? '[]', true) ?? [];
        $mergedLiabilities = array_merge($existingLiabilities, $liabilityData);
        $liabilities = json_encode($mergedLiabilities);
        
        $volumes = json_encode($request->input('volumes', json_decode($existing->volumes ?? '[]', true)));

        // 3. SIMPAN KE DATABASE
        DB::table('Laporan_Keuangan_Rekap')->updateOrInsert(
            ['bulan' => $request->bulan, 'tahun' => $request->tahun],
            [
                'saldo_awal'              => $saldoAwal,
                'pemasukan_cos'           => $pemasukanCos,
                'pemasukan_non_cos'       => $pemasukanNonCos,
                'pengeluaran_ops'         => $pengeluaranOps,
                'pengeluaran_event'       => $pengeluaranEvent,
                'pengeluaran_sekretariat' => $pengeluaranSekretariat,
                'pengeluaran_insentif'    => $pengeluaranInsentif,
                'income_cos'              => $incomeCos,
                'income_non_cos'          => $incomeNonCos,
                'expenses'                => $expenses,
                'assets'                  => $assets,
                'liabilities'             => $liabilities,
                'volumes'                 => $volumes,
                'updated_at'              => now()
            ]
        );

        return back()->with('success', 'Data keuangan berhasil diperbarui secara manual.');
    }

    public function updateTtd(Request $request) {
        // ... (Kode sama seperti sebelumnya) ...
        $data = [
            'ketua_nama' => $request->ketua_nama,
            'sekretaris_nama' => $request->sekretaris_nama,
            'bendahara_nama' => $request->bendahara_nama,
            'kota_surat' => $request->kota_surat,
            'updated_at' => now()
        ];
        if($request->hasFile('ttd_ketua')) $data['path_ttd_ketua'] = $request->file('ttd_ketua')->store('ttd', 'public');
        if($request->hasFile('ttd_sekretaris')) $data['path_ttd_sekretaris'] = $request->file('ttd_sekretaris')->store('ttd', 'public');
        if($request->hasFile('ttd_bendahara')) $data['path_ttd_bendahara'] = $request->file('ttd_bendahara')->store('ttd', 'public');

        DB::table('Pengaturan_Tanda_Tangan')->updateOrInsert(['id' => 1], $data);
        return back()->with('success', 'Pengaturan Tanda Tangan disimpan.');
    }

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

        // Default structure untuk Manual Input 3 Laporan
        $defaults = [
            'incomeCos' => ['kiic'=>0, 'kim'=>0, 'kisc'=>0, 'luar'=>0],
            'incomeNonCos' => ['adminBank'=>0, 'donasi'=>0],
            'expenses' => [
                // Operasional
                'ops_ketua'=>0, 'ops_bidang1'=>0, 'ops_bidang2'=>0, 'ops_bidang3'=>0, 'ops_bidang4'=>0, 'ops_bidang5'=>0,
                // Event (BARU)
                'evt_ketua'=>0, 'evt_bidang1'=>0, 'evt_bidang2'=>0, 'evt_bidang3'=>0, 'evt_bidang4'=>0, 'evt_bidang5'=>0,
                // Lainnya
                'sekretariat'=>0, 'insentif'=>0
            ],
            // Assets: Data aset/dana
            'assets' => [
                'bni'=>0, 'kas'=>0, 'advSekretariat'=>0, 'advBph'=>0, 'advProposal'=>0
            ],
            // Liabilities: Pengeluaran & Modal dari Posisi Keuangan
            'liabilities' => [
                // Pengeluaran
                'pos_ops'=>0, 'pos_evt'=>0, 'pos_sekretariat'=>0, 'pos_insentif'=>0,
                // Modal
                'pos_saldo_awal'=>0, 'pos_inc_cos'=>0, 'pos_inc_non_cos'=>0
            ],
            'volumes' => [],
            'saldoAwal' => 0,
            'pemasukanCos' => 0,
            'pemasukanNonCos' => 0,
            'pengeluaranOps' => 0,
            'pengeluaranEvent' => 0,
            'pengeluaranSekretariat' => 0,
            'pengeluaranInsentif' => 0,
            'totalPemasukan' => 0,
            'totalPengeluaran' => 0,
            'saldoAkhir' => 0,
            'total_aset' => 0,
            'total_pengeluaran' => 0,
            'total_modal' => 0,
            'saldo_modal' => 0
        ];

        if ($rekap) {
            // Hitung total pemasukan dan pengeluaran untuk Summary
            $pemasukanCos = $rekap->pemasukan_cos ?? 0;
            $pemasukanNonCos = $rekap->pemasukan_non_cos ?? 0;
            $totalPemasukan = $pemasukanCos + $pemasukanNonCos;

            $pengeluaranOps = $rekap->pengeluaran_ops ?? 0;
            $pengeluaranEvent = $rekap->pengeluaran_event ?? 0;
            $pengeluaranSekretariat = $rekap->pengeluaran_sekretariat ?? 0;
            $pengeluaranInsentif = $rekap->pengeluaran_insentif ?? 0;
            $totalPengeluaran = $pengeluaranOps + $pengeluaranEvent + $pengeluaranSekretariat + $pengeluaranInsentif;

            $saldoAwal = $rekap->saldo_awal ?? 0;
            $saldoAkhir = $saldoAwal + $totalPemasukan - $totalPengeluaran;

            // Hitung total dari Laporan Posisi Keuangan
            $assetsArray = json_decode($rekap->assets, true) ?? [];
            $liabilitiesArray = json_decode($rekap->liabilities, true) ?? [];

            $totalAset = 0;
            foreach ($assetsArray as $value) {
                $totalAset += $value;
            }

            // Pisahkan Pengeluaran dan Modal dari Liabilities
            $pengeluaranPos = [
                'pos_ops' => $liabilitiesArray['pos_ops'] ?? 0,
                'pos_evt' => $liabilitiesArray['pos_evt'] ?? 0,
                'pos_sekretariat' => $liabilitiesArray['pos_sekretariat'] ?? 0,
                'pos_insentif' => $liabilitiesArray['pos_insentif'] ?? 0
            ];
            $totalPengeluaranPos = array_sum($pengeluaranPos);

            $modalPos = [
                'pos_saldo_awal' => $liabilitiesArray['pos_saldo_awal'] ?? 0,
                'pos_inc_cos' => $liabilitiesArray['pos_inc_cos'] ?? 0,
                'pos_inc_non_cos' => $liabilitiesArray['pos_inc_non_cos'] ?? 0
            ];
            $totalModalPos = array_sum($modalPos);

            $saldoModalPos = $totalModalPos - $totalPengeluaranPos;

            return [
                'incomeCos' => array_merge($defaults['incomeCos'], json_decode($rekap->income_cos, true) ?? []),
                'incomeNonCos' => array_merge($defaults['incomeNonCos'], json_decode($rekap->income_non_cos, true) ?? []),
                'expenses' => array_merge($defaults['expenses'], json_decode($rekap->expenses, true) ?? []),
                'assets' => array_merge($defaults['assets'], $assetsArray),
                'liabilities' => array_merge($defaults['liabilities'], $liabilitiesArray),
                'volumes' => json_decode($rekap->volumes, true) ?? [],
                // Summary data
                'saldoAwal' => $saldoAwal,
                'pemasukanCos' => $pemasukanCos,
                'pemasukanNonCos' => $pemasukanNonCos,
                'pengeluaranOps' => $pengeluaranOps,
                'pengeluaranEvent' => $pengeluaranEvent,
                'pengeluaranSekretariat' => $pengeluaranSekretariat,
                'pengeluaranInsentif' => $pengeluaranInsentif,
                'totalPemasukan' => $totalPemasukan,
                'totalPengeluaran' => $totalPengeluaran,
                'saldoAkhir' => $saldoAkhir,
                // Position data
                'total_aset' => $totalAset,
                'total_pengeluaran' => $totalPengeluaranPos,
                'total_modal' => $totalModalPos,
                'saldo_modal' => $saldoModalPos
            ];
        }

        return $defaults;
    }
}