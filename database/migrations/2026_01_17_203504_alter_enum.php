<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
ALTER TABLE Riwayat_Absensi 
MODIFY COLUMN sumber_absensi 
ENUM('QR_DINDING', 'HP_MOBILE', 'INPUT_MANUAL', 'SYSTEM_GENERATED', 'QR_MESIN') 
NOT NULL DEFAULT 'QR_DINDING';
SQL
        );
    }

    public function down(): void
    {
        // Logic rollback (jika ada)
        DB::unprepared(<<<'SQL'
-- Optional: Delete data logic
SQL
        );
    }
};
