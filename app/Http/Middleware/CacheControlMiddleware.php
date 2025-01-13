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

        // Appliquer l'en-tête Cache-Control pour les fichiers statiques
        if ($this->isStaticAsset($request)) {
            $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
        }

        return $response;
    }

    /**
     * Vérifie si la requête concerne un fichier statique.
     *
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    protected function isStaticAsset(Request $request)
    {
        return preg_match('/\.(jpg|jpeg|png|gif|css|js|woff|woff2|ttf|otf)$/i', $request->getRequestUri());
    }
}
