<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class AbsensiService
{
    /**
     * Helper: Generate Inisial (Contoh: Dadan Muldan -> DM)
     */
    private function generateInitials($name)
    {
        $words = explode(' ', $name);
        $initials = '';
        foreach ($words as $w) {
            $initials .= strtoupper(substr($w, 0, 1));
        }
        return substr($initials, 0, 3);
    }

    /**
     * Pusat Logika Pengambilan Data Rekap
     * Digunakan oleh: View Rekap Web, Export Excel, Export PDF Matrix, Export PDF Slip
     */
    public function getDataRekap($reqBulan, $reqTahun)
    {
        $currentDate = Carbon::createFromDate($reqTahun, $reqBulan, 1);
        $startDate = $currentDate->copy()->startOfMonth();
        $endDate   = $currentDate->copy()->endOfMonth();

        // 1. Generate Array Tanggal
        $period = CarbonPeriod::create($startDate, $endDate);
        $dates = [];
        foreach ($period as $date) {
            $dates[] = $date;
        }

        // 2. Ambil Data Anggota
        $anggotaRaw = DB::table('Anggota')
            ->select('id', 'nama_lengkap')
            ->where('status_aktif', 1)
            ->orderBy('id', 'asc')
            ->get();

        $anggota = [];
        foreach ($anggotaRaw as $a) {
            $anggota[] = [
                'id' => $a->id,
                'nama' => $a->nama_lengkap,
                'inisial' => $this->generateInitials($a->nama_lengkap)
            ];
        }

        // 3. Ambil Data Absensi
        $absensiRaw = DB::table('Riwayat_Absensi')
            ->whereBetween('tanggal', [$startDate->toDateString(), $endDate->toDateString()])
            ->get();

        // 4. Mapping Data Matrix & Hitung Insentif
        $matrixData = [];
        $rekapInsentif = [];

        // Init Struktur Data
        foreach ($anggota as $usr) {
            $rekapInsentif[$usr['id']] = [
                'nama' => $usr['nama'],
                'hadir' => 0,
                'dinas' => 0,
                'sakit' => 0,
                'izin' => 0,
                'nominal_hadir' => 0,
                'nominal_dinas' => 0,
                'total_terima' => 0
            ];
        }

        // Proses Data Absensi
        foreach ($absensiRaw as $absen) {
            $dateKey = $absen->tanggal;
            $userId  = $absen->id_anggota;
            $jamPulang = $absen->jam_pulang;

            // LOGIC PENTING: Hanya data APPROVED yang dihitung valid
            $isValid = ($absen->status_validasi == 'APPROVED');

            $status = '-';

            if ($absen->status_kehadiran == 'DINAS') {
                $status = 'D';
            } elseif ($absen->status_kehadiran == 'SAKIT') {
                $status = 'S';
            } elseif ($absen->status_kehadiran == 'IZIN') {
                $status = 'I';
            } elseif ($absen->status_kehadiran == 'HADIR') {
                // Cek Lembur (> 21:00)
                if ($jamPulang && $jamPulang >= '21:00:00') {
                    $status = 'O'; // Overtime
                } else {
                    $status = 'H'; // Hadir Biasa
                }
            }

            // Jika REJECTED, tandai 'X' atau abaikan
            if ($absen->status_validasi == 'REJECTED') {
                $status = 'X';
            }

            // Simpan ke Matrix [Tanggal][User] (Tapilkan status apa adanya untuk monitoring)
            $matrixData[$dateKey][$userId] = $status;

            // Hitung Insentif (HANYA JIKA APPROVED)
            if ($isValid && isset($rekapInsentif[$userId])) {
                if ($status == 'H' || $status == 'O') {
                    $rekapInsentif[$userId]['hadir']++;
                    $rekapInsentif[$userId]['nominal_hadir'] += 100000; // Rate Hadir
                } elseif ($status == 'D') {
                    $rekapInsentif[$userId]['dinas']++;
                    $rekapInsentif[$userId]['nominal_dinas'] += 150000; // Rate Dinas
                } elseif ($status == 'S') {
                    $rekapInsentif[$userId]['sakit']++;
                } elseif ($status == 'I') {
                    $rekapInsentif[$userId]['izin']++;
                }
            }
        }

        // Hitung Total Terima
        foreach ($rekapInsentif as $uid => $val) {
            $rekapInsentif[$uid]['total_terima'] = $val['nominal_hadir'] + $val['nominal_dinas'];
        }

        // 5. Daftar Hari Libur
        $hariLibur = [
            $reqTahun . '-12-25' => 'LIBUR NATAL',
            $reqTahun . '-01-01' => 'TAHUN BARU',
            $reqTahun . '-08-17' => 'HUT RI',
            $reqTahun . '-05-01' => 'BURUH',
        ];

        return compact('dates', 'anggota', 'matrixData', 'rekapInsentif', 'startDate', 'hariLibur');
    }
}
