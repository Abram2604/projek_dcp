<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
DROP PROCEDURE IF EXISTS `sp_anggota_reset_password`;
CREATE PROCEDURE `sp_anggota_reset_password` (
    IN `p_id` INT,
    IN `p_new_password_hash` VARCHAR(255)
)
BEGIN
    UPDATE Anggota
    SET 
        password_hash = p_new_password_hash,
        diupdate_pada = NOW()
    WHERE id = p_id;
END
SQL
        );
    }

    public function down(): void
    {
        // Logic rollback (jika ada)
        DB::unprepared(<<<'SQL'
DROP PROCEDURE IF EXISTS `sp_anggota_reset_password`;
SQL
        );
    }
};
