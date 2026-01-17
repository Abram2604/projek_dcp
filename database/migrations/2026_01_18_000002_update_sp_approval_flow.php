<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. UPDATE SP DINAS: Default Status = PENDING
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
                DELETE FROM Riwayat_Absensi WHERE id_anggota = p_id_anggota AND tanggal = p_tanggal;
                INSERT INTO Riwayat_Absensi 
                (id_anggota, tanggal, jam_masuk, jam_pulang, status_kehadiran, status_validasi, sumber_absensi, keterangan_tambahan, url_bukti, dibuat_pada)
                VALUES 
                (p_id_anggota, p_tanggal, p_jam_input, p_jam_pulang_estimasi, 'DINAS', 'PENDING', 'HP_MOBILE', p_keterangan, p_url_bukti, NOW());
            END
        ");

        // 2. UPDATE SP IZIN: Default Status = PENDING
        DB::unprepared("DROP PROCEDURE IF EXISTS `sp_absen_izin`");
        DB::unprepared("
            CREATE PROCEDURE `sp_absen_izin`(
                IN `p_id_anggota` INT,
                IN `p_tanggal` DATE,
                IN `p_status` VARCHAR(20),
                IN `p_keterangan` VARCHAR(255),
                IN `p_url_bukti` VARCHAR(255)
            )
            BEGIN
                DELETE FROM Riwayat_Absensi WHERE id_anggota = p_id_anggota AND tanggal = p_tanggal;
                INSERT INTO Riwayat_Absensi 
                (id_anggota, tanggal, status_kehadiran, status_validasi, sumber_absensi, keterangan_tambahan, url_bukti, dibuat_pada)
                VALUES 
                (p_id_anggota, p_tanggal, p_status, 'PENDING', 'HP_MOBILE', p_keterangan, p_url_bukti, NOW());
            END
        ");

        // 3. UPDATE SP MASUK: Default Status = APPROVED (QR dianggap valid)
        DB::unprepared("DROP PROCEDURE IF EXISTS `sp_absen_masuk`");
        DB::unprepared("
            CREATE PROCEDURE `sp_absen_masuk`(
                IN `p_id_anggota` INT,
                IN `p_tanggal` DATE,
                IN `p_jam_masuk` TIME,
                IN `p_sumber` VARCHAR(20)
            )
            BEGIN
                DECLARE v_exists INT;
                SELECT COUNT(*) INTO v_exists FROM Riwayat_Absensi WHERE id_anggota = p_id_anggota AND tanggal = p_tanggal;

                IF v_exists > 0 THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'User sudah melakukan absen masuk hari ini.';
                ELSE
                    INSERT INTO Riwayat_Absensi 
                    (id_anggota, tanggal, jam_masuk, status_kehadiran, status_validasi, sumber_absensi, dibuat_pada)
                    VALUES 
                    (p_id_anggota, p_tanggal, p_jam_masuk, 'HADIR', 'APPROVED', p_sumber, NOW());
                END IF;
            END
        ");

        // 4. NEW SP: APPROVE/REJECT ABSENSI
        DB::unprepared("DROP PROCEDURE IF EXISTS `sp_update_status_absensi`");
        DB::unprepared("
            CREATE PROCEDURE `sp_update_status_absensi`(
                IN `p_id_record` INT,
                IN `p_status_baru` VARCHAR(20), -- 'APPROVED' or 'REJECTED'
                IN `p_validator_id` INT
            )
            BEGIN
                UPDATE Riwayat_Absensi 
                SET status_validasi = p_status_baru,
                    divalidasi_oleh = p_validator_id,
                    diupdate_pada = NOW()
                WHERE id = p_id_record;
            END
        ");
    }

    public function down(): void
    {
        // Rollback logic (Optional)
    }
};