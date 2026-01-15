<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
DROP PROCEDURE IF EXISTS `sp_anggota_generate_qr`;
CREATE PROCEDURE `sp_anggota_generate_qr` (
    IN `p_id` INT
)
BEGIN
    DECLARE v_qr_code VARCHAR(100);
    DECLARE v_exists INT;

    -- Cek apakah user ada
    SELECT COUNT(*) INTO v_exists FROM Anggota WHERE id = p_id;

    IF v_exists > 0 THEN
        -- Generate Kode Baru
        SET v_qr_code = CONCAT(
            'SPSI-', 
            YEAR(NOW()), '-', 
            p_id, '-', 
            SUBSTRING(MD5(RAND()), 1, 5) -- 5 digit random hex
        );

        -- Update Database
        UPDATE Anggota 
        SET string_kode_qr = v_qr_code,
            diupdate_pada = NOW()
        WHERE id = p_id;

        -- Return kode baru
        SELECT p_id AS id, v_qr_code AS new_qr_code, 'SUCCESS' AS status;
    ELSE
        SELECT p_id AS id, NULL AS new_qr_code, 'USER_NOT_FOUND' AS status;
    END IF;
END
SQL
        );
    }

    public function down(): void
    {
        // Logic rollback (jika ada)
        DB::unprepared(<<<'SQL'
DROP PROCEDURE IF EXISTS `sp_anggota_generate_qr`;
SQL
        );
    }
};
