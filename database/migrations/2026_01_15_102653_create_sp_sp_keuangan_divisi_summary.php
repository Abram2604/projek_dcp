<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Hapus SP lama jika ada (untuk menghindari error 'already exists')
        DB::unprepared("DROP PROCEDURE IF EXISTS `sp_keuangan_divisi_summary`");

        // 2. Buat SP Baru
        DB::unprepared("
CREATE PROCEDURE `sp_keuangan_divisi_summary` (IN `p_bulan` TINYINT, IN `p_tahun` SMALLINT)
BEGIN
    SELECT
        d.id,
        d.nama_divisi,
        d.kode_divisi,
        COALESCE(pk.saldo_awal, 0) AS saldo_awal,
        COALESCE(pk.total_pengeluaran, 0) AS total_pengeluaran,
        COALESCE(pk.sisa_saldo, 0) AS sisa_saldo
    FROM Divisi d
    LEFT JOIN Periode_Keuangan pk
        ON pk.id_divisi = d.id
        AND pk.bulan = p_bulan
        AND pk.tahun = p_tahun
    ORDER BY d.nama_divisi;
END
        ");
    }

    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS `sp_keuangan_divisi_summary`");
    }
};
