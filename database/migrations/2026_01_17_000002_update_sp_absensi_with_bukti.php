<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Update SP Absen Dinas (Tambah p_url_bukti)
        DB::unprepared("DROP PROCEDURE IF EXISTS `sp_absen_dinas`");
        DB::unprepared("
            CREATE PROCEDURE `sp_absen_dinas`(
                IN `p_id_anggota` INT,
                IN `p_tanggal` DATE,
                IN `p_jam_input` TIME,
                IN `p_jam_pulang_estimasi` TIME,
                IN `p_keterangan` VARCHAR(255),
                IN `p_url_bukti` VARCHAR(255)
            )
            BEGIN
                -- Hapus data eksisting jika ada (overwrite)
                DELETE FROM Riwayat_Absensi 
                WHERE id_anggota = p_id_anggota AND tanggal = p_tanggal;

                -- Insert Data Dinas dengan Bukti
                INSERT INTO Riwayat_Absensi 
                (id_anggota, tanggal, jam_masuk, jam_pulang, status_kehadiran, sumber_absensi, keterangan_tambahan, url_bukti, dibuat_pada)
                VALUES 
                (p_id_anggota, p_tanggal, p_jam_input, p_jam_pulang_estimasi, 'DINAS', 'HP_MOBILE', p_keterangan, p_url_bukti, NOW());
            END
        ");

        // 2. Update SP Absen Izin (Tambah p_url_bukti)
        DB::unprepared("DROP PROCEDURE IF EXISTS `sp_absen_izin`");
        DB::unprepared("
            CREATE PROCEDURE `sp_absen_izin`(
                IN `p_id_anggota` INT,
                IN `p_tanggal` DATE,
                IN `p_status` VARCHAR(20), -- 'SAKIT' atau 'IZIN'
                IN `p_keterangan` VARCHAR(255),
                IN `p_url_bukti` VARCHAR(255)
            )
            BEGIN
                -- Hapus data eksisting
                DELETE FROM Riwayat_Absensi 
                WHERE id_anggota = p_id_anggota AND tanggal = p_tanggal;

                -- Insert Data Izin dengan Bukti
                INSERT INTO Riwayat_Absensi 
                (id_anggota, tanggal, status_kehadiran, sumber_absensi, keterangan_tambahan, url_bukti, dibuat_pada)
                VALUES 
                (p_id_anggota, p_tanggal, p_status, 'HP_MOBILE', p_keterangan, p_url_bukti, NOW());
            END
        ");
    }

    public function down(): void
    {
        // Kembalikan ke versi lama (tanpa url_bukti) jika rollback
        DB::unprepared("DROP PROCEDURE IF EXISTS `sp_absen_dinas`");
        DB::unprepared("
            CREATE PROCEDURE `sp_absen_dinas`(
                IN `p_id_anggota` INT,
                IN `p_tanggal` DATE,
                IN `p_jam_input` TIME,
                IN `p_jam_pulang_estimasi` TIME,
                IN `p_keterangan` VARCHAR(255)
            )
            BEGIN
                DELETE FROM Riwayat_Absensi WHERE id_anggota = p_id_anggota AND tanggal = p_tanggal;
                INSERT INTO Riwayat_Absensi 
                (id_anggota, tanggal, jam_masuk, jam_pulang, status_kehadiran, sumber_absensi, keterangan_tambahan, dibuat_pada)
                VALUES 
                (p_id_anggota, p_tanggal, p_jam_input, p_jam_pulang_estimasi, 'DINAS', 'HP_MOBILE', p_keterangan, NOW());
            END
        ");

        DB::unprepared("DROP PROCEDURE IF EXISTS `sp_absen_izin`");
        DB::unprepared("
            CREATE PROCEDURE `sp_absen_izin`(
                IN `p_id_anggota` INT,
                IN `p_tanggal` DATE,
                IN `p_status` VARCHAR(20),
                IN `p_keterangan` VARCHAR(255)
            )
            BEGIN
                DELETE FROM Riwayat_Absensi WHERE id_anggota = p_id_anggota AND tanggal = p_tanggal;
                INSERT INTO Riwayat_Absensi 
                (id_anggota, tanggal, status_kehadiran, sumber_absensi, keterangan_tambahan, dibuat_pada)
                VALUES 
                (p_id_anggota, p_tanggal, p_status, 'HP_MOBILE', p_keterangan, NOW());
            END
        ");
    }
};