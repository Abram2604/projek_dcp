<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS `sp_pemasukan_create`");

        DB::unprepared("
CREATE PROCEDURE `sp_pemasukan_create` (
    IN `p_id_periode_keuangan` INT,
    IN `p_tanggal` DATE,
    IN `p_sumber_dana` VARCHAR(200),
    IN `p_kategori` VARCHAR(100),
    IN `p_jumlah_rupiah` DECIMAL(15,2),
    IN `p_keterangan` TEXT,
    IN `p_url_bukti` VARCHAR(255)
)
BEGIN
    DECLARE v_nominal DECIMAL(15,2);

    SET v_nominal = COALESCE(p_jumlah_rupiah, 0);

    INSERT INTO Transaksi_Pemasukan
        (id_periode_keuangan, tanggal_transaksi, sumber_dana, kategori_pemasukan, jumlah_rupiah, keterangan, url_bukti)
    VALUES
        (p_id_periode_keuangan, p_tanggal, p_sumber_dana, p_kategori, v_nominal, p_keterangan, p_url_bukti);

    UPDATE Periode_Keuangan
    SET dana_masuk = dana_masuk + v_nominal,
        total_dana_tersedia = total_dana_tersedia + v_nominal,
        sisa_saldo = sisa_saldo + v_nominal,
        diupdate_pada = NOW()
    WHERE id = p_id_periode_keuangan;
END
        ");
    }

    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS `sp_pemasukan_create`");
    }
};
