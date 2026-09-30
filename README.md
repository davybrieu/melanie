# Melanie

Laravel 13 · Vue 3 · Inertia v3 (SSR) · Tailwind CSS 4 · interface en français.

## Prérequis

- PHP 8.3+ (avec l'extension `bcmath`, requise par Laravel Lang en développement)
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

Lance le serveur Laravel, la file d'attente et Vite. En développement, le SSR est assuré directement par le serveur Vite (plugin `@inertiajs/vite`) : aucun processus Node séparé n'est nécessaire.

## Production

```bash
npm run build                 # bundle client (public/build) + bundle SSR (bootstrap/ssr)
php artisan inertia:start-ssr # serveur SSR Node, à garder actif (Supervisor, etc.)
```

Sans serveur SSR actif, Inertia revient automatiquement au rendu côté client. Le SSR peut être désactivé avec `INERTIA_SSR_ENABLED=false`.

## Structure

- `resources/js/app.js` : point d'entrée client **et** SSR
- `resources/js/pages/` : pages Inertia (ex. `Route::inertia('/', 'Welcome')`)
- `resources/views/app.blade.php` : template racine

## Traductions

Les fichiers français sont dans `lang/fr` et `lang/fr.json` (générés par [Laravel Lang](https://laravel-lang.com)). Pour les mettre à jour :

```bash
php artisan lang:update
```
