<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\Services\TimeSync\TimeSyncService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TimeSyncService::class, function ($app) {
            return new TimeSyncService(
                $app->make('App\Services\TimeProvider'), // Votre classe de récupération du temps
                3, // nombre de tentatives
                50000 // délai en microsecondes
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Paginator::useTailwind();
        Schema::defaultStringLength(191);
    }
}
