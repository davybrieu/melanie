<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Écrit public/sitemap.xml (les pages indexables de config/seo.php) et public/robots.txt.
 *
 * Aucune page n'est chargée : la liste vient de la configuration. public/llms.txt
 * n'est pas généré ici, il est rédigé à la main (voir CLAUDE.md).
 */
class GenererFichiersSeo extends Command
{
    protected $signature = 'seo:generer';

    protected $description = 'Génère public/sitemap.xml et public/robots.txt';

    public function handle(): int
    {
        $racine = rtrim(config('app.url'), '/');

        if (app()->isProduction() && ! str_starts_with($racine, 'https://')) {
            $this->warn("APP_URL ({$racine}) n’est pas en https : vérifiez le .env de production.");
        }

        $urls = array_map(
            fn (array $page) => $racine.route($page['route'], $page['parametres'] ?? [], false),
            config('seo.pages'),
        );

        $this->ecrire('sitemap.xml', $this->sitemap($urls));
        $this->ecrire('robots.txt', $this->robots($racine));
        $this->line('  '.count($urls).' pages dans le sitemap');

        return self::SUCCESS;
    }

    /**
     * @param  list<string>  $urls
     */
    private function sitemap(array $urls): string
    {
        $entrees = implode("\n", array_map(fn (string $url) => implode("\n", [
            '    <url>',
            '        <loc>'.e($url).'</loc>',
            '    </url>',
        ]), $urls));

        return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n{$entrees}\n</urlset>\n";
    }

    /**
     * Tous les robots sont autorisés partout ; seule l'adresse du sitemap est ajoutée.
     */
    private function robots(string $racine): string
    {
        return "User-agent: *\nAllow: /\nDisallow:\n\nSitemap: {$racine}/sitemap.xml\n";
    }

    private function ecrire(string $nom, string $contenu): void
    {
        $chemin = public_path($nom);

        if (is_file($chemin) && file_get_contents($chemin) === $contenu) {
            $this->line("  {$nom} : inchangé");

            return;
        }

        // Écriture atomique : un fichier à moitié écrit n'est jamais servi.
        File::put($chemin.'.tmp', $contenu);
        rename($chemin.'.tmp', $chemin);

        $this->info("  {$nom} : mis à jour");
    }
}
