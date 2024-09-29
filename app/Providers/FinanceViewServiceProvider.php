<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class FinanceViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        // Définir un View Composer pour les vues sous le préfixe 'finance'
        View::composer('finance.*', function ($view) {
            // Récupérer l'utilisateur authentifié
            $user = Auth::guard('web')->user();

            // Récupérer le nombre de notifications non lues
            $unreadCount = $user ? $user->unreadNotifications->count() : 0;

            $parrain = User::where('id', $user->parrain)->first();

            // Passer l'utilisateur et le nombre de notifications non lues aux vues
            $view->with('user', $user)
                ->with('unreadCount', $unreadCount)->with('parrain');
        });
    }
}
