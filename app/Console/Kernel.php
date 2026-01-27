<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Jalankan Auto Checkout setiap hari pukul 23:55 malam
        $schedule->command('absensi:auto-checkout')
                 ->dailyAt('23:55')
                 ->timezone('Asia/Jakarta');
                 
        // Opsional: Bisa tambahkan backup database atau tugas lain disini
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}