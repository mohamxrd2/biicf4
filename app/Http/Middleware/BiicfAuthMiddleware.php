<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BiicfAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Vérifier si l'utilisateur est authentifié et si son email est vérifié
        if (!auth()->check() || auth()->user()->email_verified_at === null) {
            return redirect()->route('biicf.login')->with('error', 'Veillez confirmer votre email !'); // Redirection avec un message d'erreur
        }

        return $next($request);
    }
}
