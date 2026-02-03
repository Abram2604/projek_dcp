<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }
    public function pagination()
{
    Paginator::useBootstrapFive(); // <--- Tambahkan baris ini
}
    /**
     * Bootstrap any application services.
     */
   public function boot(): void
    {
        Paginator::useBootstrapFive();
        View::composer('*', function ($view) {
            
            if (Auth::check()) {
                $userId = Auth::id();

                try {
                    $notif = DB::table('Notifikasi')
                                ->where('id_anggota', $userId)
                                ->where('is_read', 0) 
                                ->orderBy('dibuat_pada', 'desc')
                                ->limit(5)
                                ->get();
                    $unread = DB::table('Notifikasi')
                                ->where('id_anggota', $userId)
                                ->where('is_read', 0)
                                ->count();

                    $view->with('navbar_notif', $notif);
                    $view->with('navbar_unread', $unread);

                } catch (\Exception $e) {
                    $view->with('navbar_notif', collect([])); 
                    $view->with('navbar_unread', 0);
                }
            }
        });
    }
}