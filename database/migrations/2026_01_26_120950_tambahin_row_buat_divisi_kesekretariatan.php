<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
INSERT INTO `Divisi` (`id`, `nama_divisi`, `kode_divisi`, `deskripsi`, `dibuat_pada`) VALUES ('6', 'Bidang Kesekretariatan', 'KSK', 'Divisi Kesekretariatan', CURRENT_TIMESTAMP);
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
