<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            // Config Ziggy pour le rendu SSR ; « once » évite de la renvoyer
            // à chaque navigation (le navigateur utilise @routes).
            'ziggy' => Inertia::once(fn () => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ]),
            // Sans les adresses e-mail : la publique ne va qu'aux pages légales (PageController),
            // la privée, qui reçoit le formulaire, n'est jamais envoyée aux pages.
            'site' => Inertia::once(fn () => [
                ...Arr::except(config('site'), ['naissance', 'email', 'email_demandes']),
                'age' => Carbon::parse(config('site.naissance'))->age,
                'url' => rtrim(config('app.url'), '/'),
            ]),
            // URL canonique : toujours le domaine configuré, sans paramètres.
            'canonical' => rtrim(config('app.url'), '/').'/'.ltrim($request->path(), '/'),
        ];
    }
}
