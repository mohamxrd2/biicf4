<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CacheControlMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Appliquer un en-tête Cache-Control approprié
        // `max-age=3600` pour une heure de cache, `public` pour un cache partagé et `must-revalidate` pour revalidation après expiration
        $response->headers->set('Cache-Control', 'max-age=3600, public, must-revalidate');

        return $response;
    }
}
