<?php

namespace App\Providers;

use App\Models\Notifikasi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        View::composer([
            'admin.template.header',
            'pendaftar.template.header',
            'verifikator.template.header'
        ], function ($view) {
            $notifikasi = [];
            if (Auth::check()) {
                $query_notif = Notifikasi::where('user_id', Auth::id())->where('dibaca', 0);
                $notifikasi = $query_notif->get();
                $notifikasi_counter = $query_notif->count();
            }
            $view->with([
                'notifikasi_counter' => $notifikasi_counter,
                'notifikasi' => $notifikasi
            ]);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}