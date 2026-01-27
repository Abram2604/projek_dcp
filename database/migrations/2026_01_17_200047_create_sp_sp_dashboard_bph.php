<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
DROP PROCEDURE IF EXISTS `sp_dashboard_bph`;
DROP PROCEDURE IF EXISTS `sp_dashboard_bph`;


CREATE PROCEDURE `sp_dashboard_bph`()
BEGIN
    -- 1. Total Anggota Aktif
    SELECT COUNT(*) INTO @total_anggota FROM Anggota WHERE status_aktif = 1;

    -- 2. Hadir Hari Ini (Hanya yang APPROVED / Scan QR)
    SELECT COUNT(*) INTO @hadir_hari_ini FROM Riwayat_Absensi 
    WHERE tanggal = CURDATE() 
    AND status_kehadiran = 'HADIR'
    AND status_validasi = 'APPROVED';

    -- 3. Dinas Hari Ini (Hanya yang APPROVED)
    SELECT COUNT(*) INTO @dinas_hari_ini FROM Riwayat_Absensi 
    WHERE tanggal = CURDATE() 
    AND status_kehadiran LIKE 'DINAS%'
    AND status_validasi = 'APPROVED';

    -- 4. Laporan Masuk (Tetap sama)
    SELECT COUNT(*) INTO @laporan_masuk FROM Laporan_Harian 
    WHERE tanggal_laporan = CURDATE();

    -- 5. Saldo (Tetap sama)
    SELECT COALESCE(SUM(sisa_saldo), 0) INTO @saldo_kas FROM Periode_Keuangan 
    WHERE bulan = MONTH(CURDATE()) AND tahun = YEAR(CURDATE());

    SELECT 
        @total_anggota AS total_anggota,
        @hadir_hari_ini AS hadir_hari_ini,
        @dinas_hari_ini AS dinas_hari_ini,
        @laporan_masuk AS laporan_masuk,
        @saldo_kas AS saldo_kas;
END
SQL
        );
    }

    public function down(): void
    {
        // Logic rollback (jika ada)
        DB::unprepared(<<<'SQL'
DROP PROCEDURE IF EXISTS `sp_dashboard_bph`;
SQL
        );
    }
};
