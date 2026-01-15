<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Hapus SP lama jika ada (untuk menghindari error 'already exists')
        DB::unprepared("DROP PROCEDURE IF EXISTS `sp_keuangan_pengeluaran_detail`");

        // 2. Buat SP Baru
        DB::unprepared("
CREATE PROCEDURE `sp_keuangan_pengeluaran_detail` (IN `p_id_divisi` INT, IN `p_bulan` TINYINT, IN `p_tahun` SMALLINT)
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
        tp.keterangan,
        tp.url_bukti_struk,
        pk.nama_program
    FROM Transaksi_Pengeluaran tp
    JOIN Periode_Keuangan pkh ON tp.id_periode_keuangan = pkh.id
    LEFT JOIN Program_Kerja pk ON tp.id_program_kerja = pk.id
    WHERE pkh.id_divisi = p_id_divisi
        AND pkh.bulan = p_bulan
        AND pkh.tahun = p_tahun
    ORDER BY tp.tanggal_transaksi DESC, tp.id DESC;
END
        ");
    }

    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS `sp_keuangan_pengeluaran_detail`");
    }
};
