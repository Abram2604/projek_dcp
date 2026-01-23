<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoCheckoutAbsensi extends Command
{
    /**
     * Nama command yang akan dipanggil di terminal/scheduler
     */
    protected $signature = 'absensi:auto-checkout';

    /**
     * Deskripsi command
     */
    protected $description = 'Melakukan checkout otomatis bagi user yang lupa absen pulang hari ini';

    /**
     * Eksekusi logic
     */
    public function handle()
    {
        try {
            $this->info('Memulai proses auto-checkout...');
            
            // Panggil Stored Procedure
            DB::statement('CALL sp_auto_checkout_harian()');
            
            $this->info('Berhasil! Data absensi telah diperbarui.');
            
            // Catat ke Log Laravel (storage/logs/laravel.log)
            Log::info('Scheduler Auto-Checkout berhasil dijalankan pada ' . now());
            
        } catch (\Exception $e) {
            $this->error('Gagal: ' . $e->getMessage());
            Log::error('Scheduler Auto-Checkout Gagal: ' . $e->getMessage());
        }
    }
}