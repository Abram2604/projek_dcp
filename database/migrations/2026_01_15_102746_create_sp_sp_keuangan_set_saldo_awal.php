<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Hapus SP lama jika ada (untuk menghindari error 'already exists')
        DB::unprepared("DROP PROCEDURE IF EXISTS `sp_keuangan_set_saldo_awal`");

        // 2. Buat SP Baru
        DB::unprepared("
CREATE PROCEDURE `sp_keuangan_set_saldo_awal` (
    IN `p_id_divisi` INT,
    IN `p_bulan` TINYINT,
    IN `p_tahun` SMALLINT,
    IN `p_saldo_awal` DECIMAL(15,2),
    IN `p_id_penanggung_jawab` INT
)
BEGIN
    DECLARE v_dana_masuk DECIMAL(15,2);
    DECLARE v_total_pengeluaran DECIMAL(15,2);
    DECLARE v_exists INT DEFAULT 0;

    SELECT COUNT(*)
    INTO v_exists
    FROM Periode_Keuangan
    WHERE id_divisi = p_id_divisi
        AND bulan = p_bulan
        AND tahun = p_tahun;

    IF v_exists = 0 THEN
        INSERT INTO Periode_Keuangan
            (id_divisi, bulan, tahun, id_penanggung_jawab, saldo_awal, dana_masuk, total_dana_tersedia, total_pengeluaran, sisa_saldo, status_dokumen, dibuat_pada)
        VALUES
            (p_id_divisi, p_bulan, p_tahun, p_id_penanggung_jawab, p_saldo_awal, 0, p_saldo_awal, 0, p_saldo_awal, 'DRAFT', NOW());
    ELSE
        SELECT dana_masuk, total_pengeluaran
        INTO v_dana_masuk, v_total_pengeluaran
        FROM Periode_Keuangan
        WHERE id_divisi = p_id_divisi
            AND bulan = p_bulan
            AND tahun = p_tahun
        LIMIT 1;

        UPDATE Periode_Keuangan
        SET saldo_awal = p_saldo_awal,
            id_penanggung_jawab = p_id_penanggung_jawab,
            total_dana_tersedia = p_saldo_awal + COALESCE(v_dana_masuk, 0),
            sisa_saldo = (p_saldo_awal + COALESCE(v_dana_masuk, 0)) - COALESCE(v_total_pengeluaran, 0),
            diupdate_pada = NOW()
        WHERE id_divisi = p_id_divisi
            AND bulan = p_bulan
            AND tahun = p_tahun;
    END IF;
END
        ");
    }

    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS `sp_keuangan_set_saldo_awal`");
    }
};
