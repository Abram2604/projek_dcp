<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
        $recentPemasukan = DB::select('CALL sp_keuangan_pemasukan_recent(?, ?, ?)', [$bulan, $tahun, 5]);
        $pemasukanList = DB::select('CALL sp_keuangan_pemasukan_list(?, ?)', [$bulan, $tahun]);
        $pengeluaranList = DB::select('CALL sp_keuangan_pengeluaran_list(?, ?)', [$bulan, $tahun]);
        $divisiList = DB::select('CALL sp_divisi_list()');

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
            'recentPemasukan',
            'pemasukanList',
            'pengeluaranList',
            'detailPerDivisi',
            'divisiList',
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
                if ($saldoValue > 0) {
                    DB::statement('CALL sp_notif_saldo_divisi(?, ?, ?, ?)', [
                        (int) $divisiId,
                        $bulan,
                        $tahun,
                        $saldoValue
                    ]);
                }
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
            ->with('success', 'Saldo awal berhasil disimpan & Notifikasi Dikirim.');
    }

    public function storePemasukan(Request $request)
    {
        $request->validate([
            'id_divisi' => 'required|integer',
            'tanggal_transaksi' => 'required|date',
            'sumber_dana' => 'required|string|max:200',
            'kategori_pemasukan' => 'nullable|string|max:100',
            'jumlah_rupiah' => 'required|numeric|min:1',
            'keterangan' => 'nullable|string',
            'bukti' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'form_context' => 'nullable|string',
        ]);

        $idDivisi = (int) $request->input('id_divisi');
        $tanggal = $request->input('tanggal_transaksi');
        $sumber = $request->input('sumber_dana');
        $kategori = $request->input('kategori_pemasukan');
        $jumlah = (float) $request->input('jumlah_rupiah');
        $keterangan = $request->input('keterangan');

        $buktiUrl = null;
        $buktiPath = null;
        if ($request->hasFile('bukti')) {
            $buktiPath = $request->file('bukti')->store('keuangan/pemasukan', 'public');
            $buktiUrl = Storage::url($buktiPath);
        }

        // 1. Cek apakah periode keuangan sudah ada
        $periodeRows = DB::select('CALL sp_periode_keuangan_get(?, ?)', [$idDivisi, $tanggal]);

        // 2. [PERBAIKAN] Jika periode belum ada, BUAT SECARA OTOMATIS (Auto-Create)
        if (count($periodeRows) === 0) {
            $dateObj = Carbon::parse($tanggal);
            
            try {
                // Panggil SP untuk inisialisasi periode baru dengan saldo awal 0
                // Parameter: id_divisi, bulan, tahun, saldo_awal, id_penanggung_jawab
                DB::statement('CALL sp_keuangan_set_saldo_awal(?, ?, ?, ?, ?)', [
                    $idDivisi,
                    $dateObj->month,
                    $dateObj->year,
                    0, // Saldo awal 0, karena ini inisialisasi otomatis via pemasukan
                    Auth::id(),
                ]);

                // Panggil ulang get untuk mendapatkan ID periode yang baru saja dibuat
                $periodeRows = DB::select('CALL sp_periode_keuangan_get(?, ?)', [$idDivisi, $tanggal]);
            } catch (\Exception $e) {
                // Jika gagal buat periode (opsional: hapus file bukti jika perlu)
                if ($buktiPath) {
                    Storage::disk('public')->delete($buktiPath);
                }
                return back()
                    ->withErrors(['pemasukan' => 'Gagal membuat periode keuangan otomatis: ' . $e->getMessage()])
                    ->withInput();
            }
        }

        // Validasi double check (seharusnya tidak terjadi jika auto-create sukses)
        if (count($periodeRows) === 0) {
            if ($buktiPath) {
                Storage::disk('public')->delete($buktiPath);
            }
            return back()
                ->withErrors(['pemasukan' => 'Periode keuangan tidak dapat ditemukan atau dibuat.'])
                ->withInput();
        }

        $periodeId = $periodeRows[0]->id;

        DB::beginTransaction();
        try {
            DB::statement('CALL sp_pemasukan_create(?, ?, ?, ?, ?, ?, ?)', [
                $periodeId,
                $tanggal,
                $sumber,
                $kategori,
                $jumlah,
                $keterangan,
                $buktiUrl,
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            if ($buktiPath) {
                Storage::disk('public')->delete($buktiPath);
            }

            return back()
                ->withErrors(['pemasukan' => 'Gagal menyimpan pemasukan: ' . $e->getMessage()])
                ->withInput();
        }

        $tanggalObj = Carbon::parse($tanggal);

        return redirect()
            ->route('keuangan.index', ['bulan' => $tanggalObj->month, 'tahun' => $tanggalObj->year])
            ->with('success', 'Pemasukan berhasil disimpan.');
    }
}