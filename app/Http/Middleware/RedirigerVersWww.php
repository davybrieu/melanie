<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * En production, redirige le domaine sans www (melanie-photographie.fr) vers le domaine
 * avec www défini par APP_URL (https://www.melanie-photographie.fr), en conservant
 * le chemin et les paramètres.
 */
class RedirigerVersWww
{
    public function handle(Request $request, Closure $next): Response
    {
        $url = rtrim(config('app.url'), '/');
        $hote = strtolower((string) parse_url($url, PHP_URL_HOST));

        if (app()->isProduction() && str_starts_with($hote, 'www.') && $request->getHost() === substr($hote, 4)) {
            // 308 plutôt que 301 hors GET/HEAD : le navigateur renvoie alors le formulaire tel quel.
            return redirect()->to($url.$request->getRequestUri(), $request->isMethodSafe() ? 301 : 308);
        }

        return $next($request);
    }
}
