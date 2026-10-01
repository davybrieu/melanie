# Mélanie Photographie

Site de **melanie-photographie.fr** : photographe grossesse, nouveau-né & famille à Dijon.

Laravel 13 · Vue 3 · Inertia v3 (SSR) · Ziggy · Tailwind CSS 4 · interface en français.

## Prérequis

- PHP 8.3+ avec les extensions `gd` (WebP) et `exif` ; `bcmath` en développement (Laravel Lang)
- Composer
- Node.js 20.19+ ou 22.12+

## Installation

```bash
composer setup
```

Installe les dépendances, crée `.env`, génère la clé, lance les migrations (SQLite par défaut) et compile les assets.

## Développement

```bash
composer dev
```

Lance le serveur Laravel, la file d'attente et Vite. En développement, le SSR passe par le serveur Vite (plugin `@inertiajs/vite`) : aucun processus Node séparé.

## Production

```bash
npm run build                 # bundle client (public/build) + bundle SSR (bootstrap/ssr)
php artisan seo:generer       # sitemap.xml et robots.txt (à relancer à chaque déploiement)
php artisan inertia:start-ssr # serveur SSR Node sur 127.0.0.1:13728, à garder actif (Supervisor, etc.)
```

Dans le `.env` de production : `APP_URL=https://www.melanie-photographie.fr` (URL canoniques et sitemap), `APP_ENV=production`, `APP_DEBUG=false`, et la configuration SMTP (`MAIL_*`) pour recevoir les demandes de contact.

En production, `melanie-photographie.fr` redirige (301) vers `www.melanie-photographie.fr`. Les deux domaines doivent pointer vers le serveur (DNS) et être couverts par le certificat SSL. Côté Nginx, `www.melanie-photographie.fr` doit servir le site : aucune redirection de www vers le domaine sans www (réglage par défaut de certains panneaux d'hébergement), sinon les deux redirections tournent en boucle. Les fichiers statiques (images, `sitemap.xml`…) sont servis directement par le serveur web, sans passer par Laravel : pour les rediriger aussi, ajoutez la même règle dans la configuration du serveur.

Le serveur SSR utilise le port 13728 et non 13714, le port par défaut d'Inertia, déjà pris par d'autres sites du serveur. Ce port est défini dans `vite.config.js` (il est compilé dans le bundle SSR) et dans `config/inertia.php` : modifiez les deux ensemble, puis relancez `npm run build`, `php artisan config:cache` (si la configuration est mise en cache) et le serveur SSR.

Sur **Laravel Forge**, activez « Inertia SSR » (daemon du serveur SSR) dans l'onglet Overview du site. Aucune tâche planifiée n'est nécessaire. Dans le script de déploiement, après `npm run build`, terminez par :

```bash
$FORGE_PHP artisan seo:generer
$FORGE_PHP artisan inertia:stop-ssr --graceful   # Forge relance aussitôt le daemon, avec le nouveau bundle
```

Sans `--graceful`, la commande échoue (« Unable to connect to Inertia SSR server. ») dès que le serveur SSR est arrêté, et fait échouer tout le déploiement. Après un changement de port, redémarrez une fois le daemon depuis Forge : l'ancien processus écoute encore sur l'ancien port.

Sans serveur SSR actif, le site reste fonctionnel (rendu côté client). Les dossiers `public/photos` (variantes d'images) et `storage` doivent être accessibles en écriture.

## Sitemap, robots.txt et llms.txt

`php artisan seo:generer` écrit `public/sitemap.xml` et `public/robots.txt`, sans charger aucune page :

- `sitemap.xml` liste l'adresse de chaque page indexable déclarée dans `config/seo.php` ;
- `robots.txt` autorise tous les robots partout et indique l'adresse du sitemap.

Relancez-la à chaque déploiement. Toute nouvelle page publique doit être ajoutée à `config/seo.php`.

`public/llms.txt`, le résumé du site pour les assistants IA, est rédigé à la main et versionné. Il doit être mis à jour à chaque changement de pages, de séances, de prix, de conditions ou de coordonnées.

## Photos

Déposez les photos dans `resources/photos/` : le site les redimensionne et les convertit en WebP tout seul. Mode d'emploi : [`resources/photos/README.md`](resources/photos/README.md).

Les autres images du site (logos, décors) sont aussi en WebP. Pour en ajouter une, convertissez-la d'abord en WebP (qualité 80), puis utilisez le `.webp`. Les exceptions (favicon, icônes, image de partage `og-image.jpg`) sont détaillées dans `CLAUDE.md`.

## Modifier les contenus

| Quoi | Où |
|---|---|
| Tarifs, durées, accroches des séances | `resources/js/data/seances.js` |
| Acompte, paiement, livraison, report, bon cadeau | `resources/js/data/conditions.js` (repris dans la FAQ et les CGV) |
| Questions / réponses de la FAQ | `resources/js/data/faq.js` |
| E-mail de contact, téléphone, délai de réponse | `.env` (`SITE_EMAIL`, `SITE_TELEPHONE`, `SITE_DELAI_REPONSE`) et `config/site.php` |
| Textes des pages | `resources/js/pages/*.vue` |
| Pages du sitemap | `config/seo.php` |
| Résumé du site pour les IA | `public/llms.txt` (à tenir à jour à la main) |

## Avant la mise en ligne

- [ ] Compléter les champs surlignés **[À compléter]** des pages Mentions légales et CGV (SIRET, adresse, hébergeur, médiateur…), et faire relire les CGV.
- [ ] Valider les conditions de `resources/js/data/conditions.js`.
- [ ] Déposer les photos (portfolio, portrait, accueil).
- [ ] Relire et personnaliser la page « Mon univers » (`resources/js/pages/APropos.vue`).
- [ ] Créer l'adresse `contact@melanie-photographie.fr` (ou changer `SITE_EMAIL`) et configurer l'envoi d'e-mails.

## Structure

- `routes/web.php` : pages du site (silo `/photographe-{séance}-dijon`, `/portfolio/{catégorie}`…)
- `app/Http/Controllers` : pages, portfolio, contact, photos · `app/Console/Commands` : commande `seo:generer`
- `resources/js/pages/` : pages Inertia · `resources/js/components/` : composants (DA) · `resources/js/layouts/` : en-tête et pied de page
- `resources/js/app.js` : point d'entrée client **et** SSR
- `resources/views/app.blade.php` : template racine · `resources/views/mail/` : e-mail de demande de contact
- `public/images/marque/` : logo, monogramme, fleurs, image de partage

## Routes côté Vue (Ziggy)

Dans les templates, `route()` est disponible directement :

```vue
<Link :href="route('tarifs')">Tarifs</Link>
```

Dans `<script setup>`, passer par `inject` pour que le code fonctionne aussi en SSR :

```vue
<script setup>
import { inject } from 'vue';

const route = inject('route');
</script>
```

## Traductions

Les fichiers français sont dans `lang/fr` et `lang/fr.json` (générés par [Laravel Lang](https://laravel-lang.com)). Pour les mettre à jour :

```bash
php artisan lang:update
```
