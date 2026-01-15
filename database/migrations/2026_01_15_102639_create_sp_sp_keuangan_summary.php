<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Hapus SP lama jika ada (untuk menghindari error 'already exists')
        DB::unprepared("DROP PROCEDURE IF EXISTS `sp_keuangan_summary`");

        // 2. Buat SP Baru
        DB::unprepared("
CREATE PROCEDURE `sp_keuangan_summary` (IN `p_bulan` TINYINT, IN `p_tahun` SMALLINT)
BEGIN
    SELECT
        COALESCE(SUM(CASE WHEN bulan = p_bulan THEN sisa_saldo ELSE 0 END), 0) AS total_saldo_aktif,
        COALESCE(SUM(CASE WHEN bulan = p_bulan THEN dana_masuk ELSE 0 END), 0) AS pemasukan_bulan_ini,
        COALESCE(SUM(dana_masuk), 0) AS total_pemasukan_tahun,
        COALESCE(SUM(total_pengeluaran), 0) AS total_pengeluaran_tahun
    FROM Periode_Keuangan
    WHERE tahun = p_tahun;
END
        ");
    }

    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS `sp_keuangan_summary`");
    }
};
