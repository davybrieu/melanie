<?php

use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\RedirigerVersWww;
use App\Support\Cloudflare;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // En production : melanie-photographie.fr → www.melanie-photographie.fr (hôte de APP_URL).
        $middleware->prepend(RedirigerVersWww::class);

        // Adresse IP du visiteur derrière Cloudflare (limite d'envois du formulaire, sessions) :
        // Cloudflare l'ajoute dans X-Forwarded-For. Les autres en-têtes X-Forwarded-* ne sont pas lus :
        // l'hôte, le port et le préfixe peuvent venir du visiteur, et Cloudflare joint déjà le serveur en HTTPS.
        $middleware->trustProxies(at: Cloudflare::PLAGES_IP, headers: Request::HEADER_X_FORWARDED_FOR);

        $middleware->web(append: [
            HandleInertiaRequests::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        // Pages d'erreur aux couleurs du site (les erreurs 500 restent détaillées en mode debug).
        $exceptions->respond(function (Response $response, Throwable $exception, Request $request) {
            $statut = $response->getStatusCode();

            if ($statut === 419) {
                Inertia::flash('sessionExpiree', true);

                return back();
            }

            if (! in_array($statut, [403, 404, 429, 500, 503]) || ($statut >= 500 && config('app.debug')) || $request->expectsJson()) {
                return $response;
            }

            // Une 404 survient avant les middlewares de route : on repartage les données communes.
            Inertia::share(app(HandleInertiaRequests::class)->share($request));

            return Inertia::render('Erreur', ['statut' => $statut])
                ->toResponse($request)
                ->setStatusCode($statut);
        });
    })->create();
