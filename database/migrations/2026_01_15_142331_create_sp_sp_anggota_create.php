<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
DROP PROCEDURE IF EXISTS `sp_anggota_create`;
CREATE PROCEDURE sp_anggota_create (
    IN p_nama_lengkap VARCHAR(150),
    IN p_username VARCHAR(50),
    IN p_password_hash VARCHAR(255),
    IN p_email VARCHAR(100),
    IN p_nomor_hp VARCHAR(20),
    IN p_id_divisi INT,
    IN p_id_jabatan INT
)
BEGIN
    DECLARE v_new_id INT;
    DECLARE v_qr_code VARCHAR(100);
    INSERT INTO Anggota (
        nama_lengkap, username, password_hash, email, nomor_hp, 
        id_divisi, id_jabatan, status_aktif, dibuat_pada
    ) VALUES (
        p_nama_lengkap, p_username, p_password_hash, p_email, p_nomor_hp, 
        p_id_divisi, p_id_jabatan, 1, NOW()
    );
    SET v_new_id = LAST_INSERT_ID();
    SET v_qr_code = CONCAT(
        'SPSI-', 
        YEAR(NOW()), '-', 
        v_new_id, '-', 
        SUBSTRING(MD5(RAND()), 1, 4)
    );
    UPDATE Anggota 
    SET string_kode_qr = v_qr_code 
    WHERE id = v_new_id;
    SELECT v_new_id AS id, v_qr_code AS qr_code;
END
SQL
        );
    }

    public function down(): void
    {
        // Logic rollback (jika ada)
        DB::unprepared(<<<'SQL'
DROP PROCEDURE IF EXISTS `sp_anggota_create`;
SQL
        );
    }
};
