<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KeuanganController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();
        $bulan = (int) $request->input('bulan', $today->month);
        $tahun = (int) $request->input('tahun', $today->year);

        $summaryRows = DB::select('CALL sp_keuangan_summary(?, ?)', [$bulan, $tahun]);
        $summary = $summaryRows[0] ?? (object) [
            'total_saldo_aktif' => 0,
            'pemasukan_bulan_ini' => 0,
            'total_pemasukan_tahun' => 0,
            'total_pengeluaran_tahun' => 0,
        ];

        $divisiSummary = DB::select('CALL sp_keuangan_divisi_summary(?, ?)', [$bulan, $tahun]);
        $recentPengeluaran = DB::select('CALL sp_keuangan_pengeluaran_recent(?, ?, ?)', [$bulan, $tahun, 5]);

        $detailPerDivisi = [];
        foreach ($divisiSummary as $divisi) {
            $detailPerDivisi[$divisi->id] = DB::select('CALL sp_keuangan_pengeluaran_detail(?, ?, ?)', [
                $divisi->id,
                $bulan,
                $tahun,
            ]);
        }

        $jabatan = strtolower((string) session('user_jabatan', ''));
        $isFinanceAdmin = str_contains($jabatan, 'bendahara') || str_contains($jabatan, 'ketua');

        return view('pages.keuangan.index', compact(
            'summary',
            'divisiSummary',
            'recentPengeluaran',
            'detailPerDivisi',
            'isFinanceAdmin',
            'bulan',
            'tahun'
        ));
    }

    public function storeSaldoAwal(Request $request)
    {
        $request->validate([
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2000',
            'saldo_awal' => 'required|array',
            'saldo_awal.*' => 'nullable|numeric|min:0',
        ]);

        $bulan = (int) $request->input('bulan');
        $tahun = (int) $request->input('tahun');
        $saldoAwal = $request->input('saldo_awal', []);
        $userId = Auth::id();

        DB::beginTransaction();
        try {
            foreach ($saldoAwal as $divisiId => $saldo) {
                if ($saldo === null || $saldo === '') {
                    continue;
                }
                $saldoValue = (float) $saldo;
                DB::select('CALL sp_keuangan_set_saldo_awal(?, ?, ?, ?, ?)', [
                    (int) $divisiId,
                    $bulan,
                    $tahun,
                    $saldoValue,
                    $userId,
                ]);
                 DB::statement('CALL sp_notifikasi_saldo_divisi(?, ?, ?, ?)', [
                    (int) $divisiId,
                    $bulan,
                    $tahun,
                    $saldoValue
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withErrors(['saldo_awal' => 'Gagal menyimpan saldo awal: ' . $e->getMessage()])
                ->withInput();
        }

        return redirect()
            ->route('keuangan.index', ['bulan' => $bulan, 'tahun' => $tahun])
            ->with('success', 'Saldo awal berhasil disimpan.');
    }
}
