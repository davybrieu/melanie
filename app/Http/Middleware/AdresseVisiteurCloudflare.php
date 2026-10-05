<?php

namespace App\Http\Middleware;

use App\Support\Cloudflare;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\IpUtils;
use Symfony\Component\HttpFoundation\Response;

/**
 * Derrière Cloudflare, le serveur ne voit que l'adresse IP d'un serveur Cloudflare. Celle du
 * visiteur arrive dans l'en-tête CF-Connecting-IP, que Cloudflare écrit lui-même : elle remplace
 * l'adresse vue par Laravel (limite d'envois du formulaire, sessions).
 *
 * X-Forwarded-For ne convient pas : une requête partie d'un Worker Cloudflare peut y placer
 * l'adresse de son choix, alors que Cloudflare impose la sienne dans CF-Connecting-IP.
 * L'en-tête n'est lu que pour les requêtes venues des plages de Cloudflare : en accès direct
 * au serveur, il est ignoré.
 */
class AdresseVisiteurCloudflare
{
    public function handle(Request $request, Closure $next): Response
    {
        $visiteur = $request->headers->get('CF-Connecting-IP');

        if (filter_var($visiteur, FILTER_VALIDATE_IP) && IpUtils::checkIp((string) $request->server->get('REMOTE_ADDR'), Cloudflare::PLAGES_IP)) {
            $request->server->set('REMOTE_ADDR', $visiteur);
        }

        return $next($request);
    }
}
