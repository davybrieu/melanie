<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Str;
use Inertia\Ssr\BundleDetector;
use Inertia\Ssr\HttpGateway;
use RuntimeException;
use Symfony\Component\Process\Process;

/**
 * Génère sitemap.xml, robots.txt et llms.txt dans public/.
 *
 * Chaque page est rendue (SSR) puis réduite à une empreinte de son contenu :
 * titre, description, texte et photos de <main>. Le <lastmod> du sitemap
 * ne change que lorsque cette empreinte change, jamais simplement parce que
 * la commande a tourné.
 */
class GenererFichiersSeo extends Command
{
    protected $signature = 'seo:generer';

    protected $description = 'Génère public/sitemap.xml, public/robots.txt et public/llms.txt';

    public function handle(Kernel $kernel): int
    {
        $racine = rtrim(config('app.url'), '/');

        if (app()->isProduction() && ! str_starts_with($racine, 'https://')) {
            $this->warn("APP_URL ({$racine}) n’est pas en https : vérifiez le .env de production.");
        }

        try {
            $pages = $this->avecSsr(fn () => $this->analyserPages($kernel));
        } catch (RuntimeException $exception) {
            $this->error($exception->getMessage());
            $this->line('Les fichiers existants sont conservés.');

            return self::FAILURE;
        }

        $pages = $this->datesDeModification($pages);

        $this->ecrire('sitemap.xml', $this->sitemap($pages, $racine));
        $this->ecrire('robots.txt', $this->robots($racine));
        $this->ecrire('llms.txt', $this->llms($pages, $racine));

        $this->table(
            ['Page', 'Dernière modification'],
            array_map(fn (array $page) => [$page['chemin'], $page['lastmod'].($page['modifiee'] ? '  ← modifiée' : '')], $pages),
        );

        return self::SUCCESS;
    }

    /**
     * Rend chaque page comme pour un visiteur et en extrait le contenu.
     *
     * @return list<array<string, mixed>>
     */
    private function analyserPages(Kernel $kernel): array
    {
        // Rendus internes : aucune session à enregistrer.
        config(['session.driver' => 'array']);

        return array_map(function (array $page) use ($kernel) {
            $chemin = route($page['route'], $page['parametres'] ?? [], false);

            // Repart d'un état neuf (rendu SSR, contexte…) comme pour une vraie requête.
            app()->forgetScopedInstances();
            $reponse = $kernel->handle(Request::create($chemin));

            if ($reponse->getStatusCode() !== 200) {
                throw new RuntimeException("La page {$chemin} répond {$reponse->getStatusCode()}.");
            }

            return ['chemin' => $chemin, 'section' => $page['section'] ?? null, ...$this->extraire($reponse->getContent(), $chemin)];
        }, config('seo.pages'));
    }

    /**
     * @return array{titre: string, description: string, empreinte: string}
     */
    private function extraire(string $html, string $chemin): array
    {
        if (! str_contains($html, 'data-server-rendered') || ! preg_match('/<main\b[^>]*>(.*)<\/main>/s', $html, $main)) {
            throw new RuntimeException("Le rendu SSR de {$chemin} a échoué : contenu impossible à analyser.");
        }

        preg_match('/<title\b[^>]*>(.*?)<\/title>/s', $html, $titre);
        preg_match('/<meta name="description" content="([^"]*)"/', $html, $description);
        preg_match_all('/<img\b[^>]*>/i', $main[1], $images);

        $titre = $this->texte($titre[1] ?? '');
        $description = $this->texte($description[1] ?? '');

        // Photos : dossier/nom et dimensions (l'empreinte des URL varie d'un serveur à l'autre).
        $photos = array_map(fn (string $img) => implode('|', [
            preg_replace('#^/photos/\d+/(.+)-[a-f0-9]{8}\.webp$#', '$1', $this->attribut($img, 'src')),
            $this->attribut($img, 'alt'),
            $this->attribut($img, 'width'),
            $this->attribut($img, 'height'),
        ]), $images[0]);

        return [
            'titre' => $titre,
            'description' => $description,
            'empreinte' => sha1(json_encode([$titre, $description, $this->texte($main[1]), $photos])),
        ];
    }

    private function attribut(string $balise, string $nom): string
    {
        return preg_match('/\s'.$nom.'="([^"]*)"/', $balise, $valeur)
            ? html_entity_decode($valeur[1], ENT_QUOTES | ENT_HTML5, 'UTF-8')
            : '';
    }

    private function texte(string $html): string
    {
        $html = preg_replace('/<(script|style)\b.*?<\/\1>/is', ' ', $html);
        $texte = html_entity_decode(preg_replace('/<[^>]+>/', ' ', $html), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim(preg_replace('/\s+/u', ' ', $texte));
    }

    /**
     * Rendu SSR via Vite (développement), le serveur SSR déjà lancé, ou à défaut
     * un serveur SSR temporaire arrêté à la fin.
     *
     * @template T
     *
     * @param  callable(): T  $rendu
     * @return T
     */
    private function avecSsr(callable $rendu): mixed
    {
        $passerelle = app(HttpGateway::class);

        if (Vite::isRunningHot() || $passerelle->isHealthy()) {
            return $rendu();
        }

        $bundle = app(BundleDetector::class)->detect()
            ?? throw new RuntimeException('Bundle SSR introuvable : lancez « npm run build ».');

        $serveur = new Process([config('inertia.ssr.runtime', 'node'), $bundle], base_path());
        $serveur->setTimeout(null);
        $serveur->start();

        try {
            $limite = microtime(true) + 15;

            while (! $passerelle->isHealthy()) {
                if (! $serveur->isRunning() || microtime(true) > $limite) {
                    throw new RuntimeException('Le serveur SSR n’a pas démarré : '.trim($serveur->getErrorOutput()));
                }

                usleep(200_000);
            }

            return $rendu();
        } finally {
            $serveur->stop();
        }
    }

    /**
     * Garde la date de dernière modification des pages dont le contenu n'a pas changé.
     *
     * @param  list<array<string, mixed>>  $pages
     * @return list<array<string, mixed>>
     */
    private function datesDeModification(array $pages): array
    {
        $disque = Storage::disk('local');
        $fichier = config('seo.empreintes');
        $precedentes = $disque->exists($fichier) ? (json_decode($disque->get($fichier), true) ?? []) : [];
        $aujourdhui = now(config('seo.fuseau_horaire'))->toDateString();

        $pages = array_map(function (array $page) use ($precedentes, $aujourdhui) {
            $precedente = $precedentes[$page['chemin']] ?? null;
            $inchangee = $precedente && $precedente['empreinte'] === $page['empreinte'];

            return [...$page, 'lastmod' => $inchangee ? $precedente['lastmod'] : $aujourdhui, 'modifiee' => ! $inchangee];
        }, $pages);

        $etat = [];

        foreach ($pages as $page) {
            $etat[$page['chemin']] = ['empreinte' => $page['empreinte'], 'lastmod' => $page['lastmod']];
        }

        $disque->put($fichier, json_encode($etat, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

        return $pages;
    }

    /**
     * @param  list<array<string, mixed>>  $pages
     */
    private function sitemap(array $pages, string $racine): string
    {
        $urls = implode("\n", array_map(fn (array $page) => implode("\n", [
            '    <url>',
            '        <loc>'.e($racine.$page['chemin']).'</loc>',
            "        <lastmod>{$page['lastmod']}</lastmod>",
            '    </url>',
        ]), $pages));

        return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n{$urls}\n</urlset>\n";
    }

    private function robots(string $racine): string
    {
        return "User-agent: *\nAllow: /\n\nSitemap: {$racine}/sitemap.xml\n";
    }

    /**
     * Fichier llms.txt (https://llmstxt.org) tiré des titres et descriptions réels des pages.
     *
     * @param  list<array<string, mixed>>  $pages
     */
    private function llms(array $pages, string $racine): string
    {
        $site = config('site');
        $suffixe = ' | '.$site['nom'];
        $accueil = collect($pages)->firstWhere('chemin', '/') ?? ['titre' => $site['metier'], 'description' => ''];

        $sections = collect($pages)
            ->filter(fn (array $page) => $page['section'] !== null)
            ->groupBy('section')
            ->map(fn ($liste, string $section) => "## {$section}\n\n".$liste
                ->map(fn (array $page) => '- ['.Str::before($page['titre'], $suffixe)."]({$racine}{$page['chemin']}): {$page['description']}")
                ->implode("\n"))
            ->implode("\n\n");

        return implode("\n\n", [
            "# {$site['nom']} – ".Str::before($accueil['titre'], $suffixe),
            "> {$accueil['description']}",
            "{$site['nom']} est installée à {$site['commune']} et se déplace à {$site['ville']} et dans ses alentours. "
                ."Réservations par le formulaire de contact du site ou sur Instagram (@{$site['instagram']}). Contact : {$site['email']}.",
            $sections,
        ])."\n";
    }

    private function ecrire(string $nom, string $contenu): void
    {
        $chemin = public_path($nom);

        if (is_file($chemin) && file_get_contents($chemin) === $contenu) {
            $this->line("  {$nom} : inchangé");

            return;
        }

        File::put($chemin.'.tmp', $contenu);
        rename($chemin.'.tmp', $chemin);

        $this->info("  {$nom} : mis à jour");
    }
}
