<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Menggunakan Raw SQL karena mengubah ENUM di Laravel kadang bermasalah dengan Doctrine
        DB::statement("
            ALTER TABLE Program_Kerja 
            MODIFY COLUMN status_proker 
            ENUM('RENCANA', 'BERJALAN', 'SELESAI', 'DITUNDA', 'DIBATALKAN', 'TERKENDALA') 
            NOT NULL DEFAULT 'RENCANA'
        ");
    }

    public function down(): void
    {
        // Kembalikan ke setingan awal (Hati-hati, data 'TERKENDALA' akan hilang/error jika di-rollback)
        DB::statement("UPDATE Program_Kerja SET status_proker = 'DITUNDA' WHERE status_proker = 'TERKENDALA'");
        
        DB::statement("
            ALTER TABLE Program_Kerja 
            MODIFY COLUMN status_proker 
            ENUM('RENCANA', 'BERJALAN', 'SELESAI', 'DITUNDA', 'DIBATALKAN') 
            NOT NULL DEFAULT 'RENCANA'
        ");
    }
};