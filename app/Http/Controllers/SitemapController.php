<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /** Pages indexables, dans l'ordre du silo. */
    private const ROUTES = [
        ['accueil'],
        ['grossesse'],
        ['nouveau-ne'],
        ['famille'],
        ['portfolio'],
        ['portfolio.categorie', ['categorie' => 'grossesse']],
        ['portfolio.categorie', ['categorie' => 'nouveau-ne']],
        ['portfolio.categorie', ['categorie' => 'famille']],
        ['a-propos'],
        ['tarifs'],
        ['bon-cadeau'],
        ['faq'],
        ['contact'],
        ['mentions-legales'],
        ['confidentialite'],
        ['cgv'],
    ];

    public function __invoke(): Response
    {
        // Mêmes URL que les balises canoniques : domaine de APP_URL, quel que soit l'hôte de la requête.
        $racine = rtrim(config('app.url'), '/');

        $urls = collect(self::ROUTES)
            ->map(fn (array $route) => '<url><loc>'.e($racine.route($route[0], $route[1] ?? [], false)).'</loc></url>')
            ->implode("\n    ");

        $xml = <<<XML
        <?xml version="1.0" encoding="UTF-8"?>
        <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
            {$urls}
        </urlset>
        XML;

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}
