<?php

namespace App\Http\Middleware;

use App\Support\Admin;
use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

/**
 * Espace de gestion (/admin) : 404 tant que les identifiants ne sont pas configurés, page de
 * connexion pour qui n'est pas connecté. Ses pages ne sont jamais indexées.
 */
class EspaceAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(Admin::actif(), 404);

        $connecte = hash_equals(Admin::jeton(), (string) $request->session()->get('admin'));
        $pageConnexion = $request->routeIs('admin.connexion', 'admin.connexion.envoyer');

        if (! $connecte && ! $pageConnexion) {
            // Action envoyée après l'expiration de la session : la page de connexion le signale.
            if (! $request->isMethod('GET')) {
                Inertia::flash('sessionExpiree', true);
            }

            return redirect()->guest(route('admin.connexion'));
        }

        if ($connecte && $pageConnexion) {
            return to_route('admin.bons-cadeaux');
        }

        $reponse = $next($request);
        $reponse->headers->set('X-Robots-Tag', 'noindex, nofollow');

        return $reponse;
    }
}
