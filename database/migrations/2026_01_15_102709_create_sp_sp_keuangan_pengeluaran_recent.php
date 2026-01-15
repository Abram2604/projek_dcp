<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Hapus SP lama jika ada (untuk menghindari error 'already exists')
        DB::unprepared("DROP PROCEDURE IF EXISTS `sp_keuangan_pengeluaran_recent`");

        // 2. Buat SP Baru
        DB::unprepared("
CREATE PROCEDURE `sp_keuangan_pengeluaran_recent` (IN `p_bulan` TINYINT, IN `p_tahun` SMALLINT, IN `p_limit` INT)
BEGIN
    SELECT
        tp.id,
        tp.tanggal_transaksi,
        tp.nama_kegiatan,
        tp.uraian_pengeluaran,
        tp.volume,
        tp.satuan,
        tp.jumlah_rupiah,
        tp.total_nominal,
        d.nama_divisi,
        pk.nama_program
    FROM Transaksi_Pengeluaran tp
    JOIN Periode_Keuangan pkh ON tp.id_periode_keuangan = pkh.id
    JOIN Divisi d ON pkh.id_divisi = d.id
    LEFT JOIN Program_Kerja pk ON tp.id_program_kerja = pk.id
    WHERE pkh.bulan = p_bulan
        AND pkh.tahun = p_tahun
    ORDER BY tp.tanggal_transaksi DESC, tp.id DESC
    LIMIT p_limit;
END
        ");
    }

    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS `sp_keuangan_pengeluaran_recent`");
    }
};
