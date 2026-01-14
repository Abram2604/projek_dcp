<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $levelAkses = session('user_level', 'ANGGOTA');
        $today = Carbon::today();

        // --- LOGIKA BPH (PAKAI STORED PROCEDURE) ---
        if ($levelAkses === 'BPH') {
            
            // 1. Panggil SP yang baru dibuat (Lebih Cepat!)
            $stats = DB::select('CALL sp_dashboard_bph()');
            $dataStats = $stats[0]; // Ambil baris pertama

            // 2. Chart Pie (Tetap Query Builder agar fleksibel format array-nya)
            $statistikAbsen = DB::table('Riwayat_Absensi')
                            ->select('status_kehadiran', DB::raw('count(*) as total'))
                            ->where('tanggal', $today)
                            ->groupBy('status_kehadiran')
                            ->pluck('total', 'status_kehadiran')->toArray();
            
            $chartAbsenLabel = array_keys($statistikAbsen);
            $chartAbsenData  = array_values($statistikAbsen);

            // 3. Chart Bar Laporan
            $laporanPerDivisi = DB::table('Laporan_Harian')
                                ->join('Divisi', 'Laporan_Harian.id_divisi', '=', 'Divisi.id')
                                ->select('Divisi.kode_divisi', DB::raw('count(*) as total'))
                                ->where('tanggal_laporan', $today)
                                ->groupBy('Divisi.kode_divisi')
                                ->pluck('total', 'kode_divisi')->toArray();

            $chartLaporanLabel = array_keys($laporanPerDivisi);
            $chartLaporanData  = array_values($laporanPerDivisi);

            // 4. Tabel Keuangan
            $keuanganDivisi = DB::table('Periode_Keuangan')
                              ->join('Divisi', 'Periode_Keuangan.id_divisi', '=', 'Divisi.id')
                              ->where('bulan', $today->month)
                              ->where('tahun', $today->year)
                              ->select('Divisi.nama_divisi', 'saldo_awal', 'total_pengeluaran', 'sisa_saldo')
                              ->get();

            return view('dashboard.index', compact(
                'levelAkses', 'dataStats', // <-- Kita kirim variabel baru ini
                'chartAbsenLabel', 'chartAbsenData', 'chartLaporanLabel', 'chartLaporanData',
                'keuanganDivisi'
            ));
        } 
        
        // --- LOGIKA ANGGOTA (Tetap sama) ---
        else {
            // 1. Panggil SP untuk Cek Status Hari Ini (Header Dashboard)
            $statusHariIni = DB::select('CALL sp_dashboard_anggota(?)', [$user->id]);
            $dataHeader = $statusHariIni[0]; // Isinya: jam_masuk & status_lapor

            // 2. Riwayat Kehadiran (5 Terakhir) - Tetap Query Builder (Karena butuh array data banyak)
            $riwayatAbsen = DB::table('Riwayat_Absensi')
                            ->where('id_anggota', $user->id)
                            ->orderBy('tanggal', 'desc')
                            ->limit(5)
                            ->get();

            // 3. Laporan Divisi Terakhir (5 Terakhir) - Tetap Query Builder
            $laporanDivisi = DB::table('Laporan_Harian')
                                ->where('id_divisi', $user->id_divisi)
                                ->orderBy('dibuat_pada', 'desc')
                                ->limit(5)
                                ->get();

            return view('dashboard.index', compact(
                'levelAkses', 'dataHeader', 'riwayatAbsen', 'laporanDivisi'
            ));
        }
    }
}   