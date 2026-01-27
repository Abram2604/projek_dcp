<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS `sp_keuangan_pemasukan_recent`");

        DB::unprepared("
CREATE PROCEDURE `sp_keuangan_pemasukan_recent` (IN `p_bulan` TINYINT, IN `p_tahun` SMALLINT, IN `p_limit` INT)
BEGIN
    SELECT
        tp.id,
        tp.tanggal_transaksi,
        tp.sumber_dana,
        tp.kategori_pemasukan,
        tp.jumlah_rupiah,
        tp.keterangan,
        tp.url_bukti,
        d.nama_divisi
    FROM Transaksi_Pemasukan tp
    JOIN Periode_Keuangan pkh ON tp.id_periode_keuangan = pkh.id
    JOIN Divisi d ON pkh.id_divisi = d.id
    WHERE pkh.bulan = p_bulan
        AND pkh.tahun = p_tahun
    ORDER BY tp.tanggal_transaksi DESC, tp.id DESC
    LIMIT p_limit;
END
        ");
    }

    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS `sp_keuangan_pemasukan_recent`");
    }
};
