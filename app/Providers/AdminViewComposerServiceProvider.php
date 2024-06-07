<?php

// app/Providers/AdminViewComposerServiceProvider.php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;

class AdminViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Utiliser un view composer pour toutes les vues dans le namespace 'admin'
        View::composer('admin.*', function ($view) {
            $admin = Auth::guard('admin')->user();
            $view->with('admin', $admin);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
