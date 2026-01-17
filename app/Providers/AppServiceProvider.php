<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // === LOGIKA NOTIFIKASI NAVBAR ===
        // Kode ini akan berjalan setiap kali tampilan (View) dimuat
        View::composer('*', function ($view) {
            
            // Cek apakah user sedang login?
            if (Auth::check()) {
                $userId = Auth::id();

                try {
                    // 1. Ambil 5 Notifikasi Terbaru dari SP
                    $notif = DB::select("CALL sp_notifikasi_list(?, ?)", [$userId, 5]);
                    
                    // 2. Hitung Jumlah yang Belum Dibaca (Unread)
                    $unread = DB::table('Notifikasi')
                                ->where('id_anggota', $userId)
                                ->where('is_read', 0)
                                ->count();

                    // 3. Kirim variabel ke semua View (Navbar)
                    $view->with('navbar_notif', $notif);
                    $view->with('navbar_unread', $unread);

                } catch (\Exception $e) {
                    // Jika database belum siap/migrasi belum jalan, hindari error fatal
                    $view->with('navbar_notif', []);
                    $view->with('navbar_unread', 0);
                }
            }
        });
    }
}