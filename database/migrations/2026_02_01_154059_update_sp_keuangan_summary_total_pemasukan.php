<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared("
            DROP PROCEDURE IF EXISTS `sp_keuangan_summary`;
            CREATE PROCEDURE `sp_keuangan_summary` (IN `p_bulan` TINYINT, IN `p_tahun` SMALLINT)
            BEGIN
                SELECT
                    COALESCE(SUM(CASE WHEN bulan = p_bulan THEN sisa_saldo ELSE 0 END), 0) AS total_saldo_aktif,
                    (
                        SELECT COALESCE(SUM(tp.jumlah_rupiah), 0)
                        FROM Transaksi_Pemasukan tp
                        JOIN Periode_Keuangan pk ON tp.id_periode_keuangan = pk.id 
                        WHERE pk.bulan = p_bulan AND pk.tahun = p_tahun
                    ) AS pemasukan_bulan_ini,
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