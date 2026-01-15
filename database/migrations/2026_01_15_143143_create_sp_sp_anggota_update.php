<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
DROP PROCEDURE IF EXISTS `sp_anggota_update`;
CREATE PROCEDURE `sp_anggota_soft_delete` (
    IN `p_id` INT
)
BEGIN
    UPDATE Anggota
    SET 
        status_aktif = 0,
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
DROP PROCEDURE IF EXISTS `sp_anggota_update`;
SQL
        );
    }
};
