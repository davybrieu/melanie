# Mélanie Brieu – Photographie

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
php artisan inertia:start-ssr # serveur SSR Node, à garder actif (Supervisor, etc.)
```

Dans le `.env` de production : `APP_URL=https://melanie-photographie.fr` (URL canoniques et sitemap), `APP_ENV=production`, `APP_DEBUG=false`, et la configuration SMTP (`MAIL_*`) pour recevoir les demandes de contact.

Sans serveur SSR actif, le site reste fonctionnel (rendu côté client). Les dossiers `public/photos` (variantes d'images) et `storage` doivent être accessibles en écriture.

## Photos

Déposez les photos dans `resources/photos/` : le site les redimensionne et les convertit en WebP tout seul. Mode d'emploi : [`resources/photos/README.md`](resources/photos/README.md).

## Modifier les contenus

| Quoi | Où |
|---|---|
| Tarifs, durées, accroches des séances | `resources/js/data/seances.js` |
| Acompte, paiement, livraison, report, bon cadeau | `resources/js/data/conditions.js` (repris dans la FAQ et les CGV) |
| Questions / réponses de la FAQ | `resources/js/data/faq.js` |
| E-mail de contact, téléphone, délai de réponse | `.env` (`SITE_EMAIL`, `SITE_TELEPHONE`, `SITE_DELAI_REPONSE`) et `config/site.php` |
| Textes des pages | `resources/js/pages/*.vue` |

## Avant la mise en ligne

- [ ] Compléter les champs surlignés **[À compléter]** des pages Mentions légales, Confidentialité et CGV (SIRET, adresse, hébergeur, médiateur…), et faire relire les CGV.
- [ ] Valider les conditions de `resources/js/data/conditions.js`.
- [ ] Déposer les photos (portfolio, portrait, accueil).
- [ ] Relire et personnaliser la page « Mon univers » (`resources/js/pages/APropos.vue`).
- [ ] Créer l'adresse `contact@melanie-photographie.fr` (ou changer `SITE_EMAIL`) et configurer l'envoi d'e-mails.

## Structure

- `routes/web.php` : pages du site (silo `/photographe-{séance}-dijon`, `/portfolio/{catégorie}`…)
- `app/Http/Controllers` : pages, portfolio, contact, sitemap, photos
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
