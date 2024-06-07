<?php
// app/Http/Middleware/AdminAuthMiddleware.php

namespace App\Http\Middleware;

use App\Models\Admin;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier si l'utilisateur est authentifié en tant qu'administrateur
        if (Auth::guard('admin')->check()) {
            // L'utilisateur est authentifié en tant qu'administrateur, mettre à jour last_seen
            Admin::where('id', Auth::guard('admin')->user()->id)->update(['last_seen' => now()]);

            // Vérifier si l'administrateur est banni
            if (Auth::guard('admin')->user()->isban) {
                Auth::guard('admin')->logout(); // Déconnexion de l'administrateur
                return redirect()->route('admin.login')->with('error', 'Votre compte à été bloqué !'); // Redirection avec un message d'erreur
            }
        } else {
            // Utilisateur non authentifié en tant qu'administrateur, rediriger vers la page de connexion
            return redirect()->route('admin.login');
        }


        // L'utilisateur est authentifié en tant qu'administrateur, autoriser l'accès à la route
        return $next($request);
    }
}
