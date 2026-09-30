# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Projet

Site de **Mélanie Photographie**, photographe grossesse, nouveau-né et famille à Dijon (melanie-photographie.fr) : Laravel 13, Inertia v3 avec SSR, Vue 3, Ziggy, Tailwind CSS 4.

Tout est en français : noms de classes, méthodes, variables et composants, contenus, messages de commit. Les textes du site sont écrits par Mélanie à la première personne et vouvoient le visiteur ; les textes affichés utilisent l'apostrophe typographique `’`.

Pas de base de données métier : SQLite ne sert qu'aux sessions, au cache et à la file d'attente. Les contenus sont dans le code.

**Git : tous les commits se font directement sur `main`.** Ne créez ni branche ni pull request.

## Commandes

```bash
composer setup                  # installation : .env, clé, migrations (SQLite), npm, build, seo:generer
composer dev                    # serveur Laravel + file d'attente + Vite (en dev, le SSR passe par Vite)
npm run build                   # bundle client (public/build) + bundle SSR (bootstrap/ssr)
php artisan inertia:start-ssr   # serveur SSR Node de production (127.0.0.1:13714)
php artisan seo:generer         # génère public/sitemap.xml, robots.txt et llms.txt
php artisan lang:update         # met à jour les traductions lang/fr (Laravel Lang)
```

Pas de tests automatisés ni de linter, par choix du projet : n'en ajoutez pas et gardez les dépendances au strict minimum. Pour valider un changement :

1. `npm run build`, qui compile aussi le bundle SSR et fait donc remonter les erreurs de template ;
2. `php -l` sur les fichiers PHP modifiés ;
3. contrôle d'une page rendue : avec `php artisan serve` et `php artisan inertia:start-ssr` lancés, le HTML doit contenir `data-server-rendered`.

## Architecture

**Chaîne de rendu.** Route nommée (`routes/web.php`) → contrôleur → `Inertia::render('Page', props)` → `resources/views/app.blade.php` → page Vue de `resources/js/pages/`. `resources/js/app.js` est l'unique point d'entrée, client et SSR (plugin `@inertiajs/vite`) : il ajoute « | Mélanie Photographie » aux titres et applique `layouts/SiteLayout.vue` (en-tête et pied de page) à toutes les pages.

**Props partagées** (`app/Http/Middleware/HandleInertiaRequests.php`) :
- `site` : `config/site.php` (identité, contact, localisation), envoyée une fois par visite (`Inertia::once`). Côté Vue, passer par le composable `useSite()`, qui ajoute `instagramUrl`.
- `ziggy` : configuration des routes, utile seulement au rendu SSR (le navigateur utilise `@routes`).
- `canonical` : toujours construite à partir de `APP_URL`, jamais de l'hôte de la requête.

Les messages ponctuels passent par `Inertia::flash()` / `page.flash` (`demandeEnvoyee`, `sessionExpiree`).

**Ziggy.** `route()` est global dans les templates. Dans `<script setup>`, écrire `const route = inject('route')` pour que le code fonctionne en SSR. Les réponses de la FAQ sont du HTML avec des liens écrits en dur (`/tarifs`, `/contact`) : à reprendre si une URL change. Les routes techniques sont exclues dans `config/ziggy.php`.

**Données de contenu** (`resources/js/data/`). Les textes vivent dans les pages Vue ; ce qui est repris à plusieurs endroits est centralisé ici :
- `seances.js` : prix, durées et contenu des séances. C'est la source unique : pages, FAQ, CGV, meta descriptions et JSON-LD en dépendent. Ne jamais écrire un prix en dur dans une page.
- `conditions.js` : acompte, paiement, livraison, report, validité du bon cadeau. Ce sont des valeurs provisoires, que Mélanie doit valider.
- `faq.js` : questions et réponses (HTML), reprises par la page FAQ, les pages séances, la page tarifs et le JSON-LD `FAQPage`. Les questions ont été fournies par Mélanie : ne pas les reformuler ni les supprimer sans son accord.
- `seo.js` : générateurs JSON-LD (`LocalBusiness`, `Service`, `FAQPage`).

Les clés `grossesse`, `nouveau-ne` et `famille` relient toutes les couches :
- les URL silo `/photographe-{clé}-dijon` et `/portfolio/{clé}` ;
- `DemandeContactRequest::SEANCES` ;
- `Photos::DOSSIERS` et les dossiers de `resources/photos/` ;
- `data/seances.js` et `data/portfolio.js` ;
- `config/seo.php`.

Ajouter un type de séance, c'est modifier tous ces endroits.

**Photos** (`app/Support/Photos.php`). Les photos sources sont dans `resources/photos/{grossesse,nouveau-ne,famille,site}/`, triées par nom de fichier. Leur rôle dépend de leur rang (la 1re est la photo principale de la page séance, etc.) ou, dans `site/`, de leur nom (`accueil`, `portrait`, `a-propos`, `bon-cadeau`) : voir `resources/photos/README.md`.

Les contrôleurs passent aux pages des objets `{src, srcset, largeur, hauteur, alt}` ; le texte `alt` est tiré du nom de fichier. Les variantes WebP (`/photos/{largeur}/{dossier}/{nom}-{empreinte}.webp`) sont créées à la première demande par `PhotoController`, puis servies comme fichiers statiques depuis `public/photos` (non versionné). S'il n'y a pas de photo, `components/Photo.vue` affiche un emplacement aux couleurs du site.

**SEO.** Chaque page inclut `<Seo titre="…" description="…" :json-ld="…" />`, avec un titre sans suffixe.

La commande `php artisan seo:generer` (`app/Console/Commands/GenererFichiersSeo.php`) rend en SSR chaque page listée dans `config/seo.php`. Elle garde une empreinte du contenu de chaque page (titre, description, texte et photos de `<main>`) dans `storage/app/private/seo/empreintes.json`. Le `<lastmod>` du sitemap ne change que si ce contenu change.

- La commande a besoin du bundle SSR compilé (ou de Vite lancé). En cas d'échec, elle garde les anciens fichiers.
- Elle tourne chaque nuit à 4 h, heure de Paris (`routes/console.php`).
- **Toute nouvelle page publique doit être ajoutée à `config/seo.php`.**
- `sitemap.xml`, `robots.txt` et `llms.txt` sont générés et ne sont pas versionnés.

**Erreurs** (`bootstrap/app.php`). Les codes 403, 404, 429, 500 et 503 affichent la page Inertia `Erreur` (en mode debug, les erreurs 500 gardent la page détaillée de Laravel). Une 419 (session expirée) ramène l'utilisateur sur la page précédente avec le flash `sessionExpiree`.

**Contact.** `ContactController` et `DemandeContactRequest` (champ piège `site_web`, 5 envois par minute au maximum) envoient `App\Mail\DemandeContact` à `config('site.email')`. Les paramètres `/contact?seance=…` et `?objet=bon-cadeau` pré-remplissent le formulaire. Les champs obligatoires ont un astérisque rouge ; les champs facultatifs n'ont aucune mention.

## Style (Tailwind CSS 4)

- Couleurs et polices sont définies dans le bloc `@theme` de `resources/css/app.css` : `creme`, `poudre`, `taupe`, `cacao`, `brique`, `or`, `rouge`.
- Utilitaires maison (en `@utility`) : `surtitre`, `manuscrit`, `conteneur`, `texte-courant`. La classe `.prose-mp` met en forme le HTML de la FAQ et des pages légales ; `.a-completer` signale les champs à remplir.
- Pièges de Tailwind 4 : une classe maison doit être déclarée en `@utility` pour marcher avec `@apply` et les variantes ; le modificateur important s'écrit en suffixe (`px-6!`).
- Polices : Gilda Display pour les titres, Jost pour le texte, Allison (manuscrite) pour les phrases courtes seulement. Elles sont auto-hébergées via `bunny()` dans `vite.config.js`, sans appel externe (RGPD).
- Boutons : `components/Bouton.vue`, variantes `plein`, `contour` et `lien`. Une ancre `#…` ou un lien `externe` donne une balise `<a>` classique, sans navigation Inertia.

## Règles de contenu

- Ne jamais attribuer à Mélanie une formation, une certification ou un label qu'elle n'a pas (notamment sur la sécurité des nouveau-nés).
- Ne pas inventer d'informations légales : les mentions `[À compléter]` des pages Mentions légales, Confidentialité et CGV restent à remplir par Mélanie.

## Production

- `APP_URL` doit être l'URL https avec www (`https://www.melanie-photographie.fr`) : les URL canoniques, le sitemap, `robots.txt` et `llms.txt` en dépendent. En production, le middleware global `RedirigerVersWww` redirige en 301 le domaine sans www vers cet hôte (les fichiers statiques ne passent pas par Laravel).
- À chaque déploiement : `npm run build` puis `php artisan seo:generer`. Garder `php artisan inertia:start-ssr` actif et installer la tâche cron `schedule:run` de Laravel.
- Conserver `storage/app/private/seo/empreintes.json` d'un déploiement à l'autre, sinon toutes les dates `<lastmod>` repartent du jour de la génération.
- Sans serveur SSR actif, le site reste fonctionnel en rendu côté client.
- PHP doit avoir les extensions `gd` (WebP) et `exif`.
