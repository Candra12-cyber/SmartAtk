<?php

namespace App\Providers;

use App\Models\Permintaan;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        // Paksa locale Indonesia untuk format tanggal (translatedFormat),
        // supaya tidak tergantung cache config atau nilai APP_LOCALE di .env.
        App::setLocale('id');

        View::composer(['admin.*', 'admin/*'], function ($view) {
            $jumlahMenunggu = Permintaan::where('status', 'Menunggu')->count();
            $view->with('jumlahMenunggu', $jumlahMenunggu);
        });
    }
}