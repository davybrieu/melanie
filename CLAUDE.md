# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Projet

Site de **Mélanie Photographie**, photographe grossesse, nouveau-né et famille à Dijon (melanie-photographie.fr) : Laravel 13, Inertia v3 avec SSR, Vue 3, Ziggy, Tailwind CSS 4.

Tout est en français : noms de classes, méthodes, variables et composants, contenus, messages de commit. Les textes du site sont écrits par Mélanie à la première personne et vouvoient le visiteur ; les textes affichés utilisent l'apostrophe typographique `’`.

Les contenus sont dans le code. La base de données (SQLite en local ; en production, celle du serveur Forge, pas SQLite) ne sert qu'aux sessions, au cache, à la file d'attente, aux avis Google et aux bons cadeaux (voir ces deux sections).

**Git : tous les commits se font directement sur `main`.** Ne créez ni branche ni pull request.

## Commandes

```bash
composer setup                  # installation : .env, clé, migrations (SQLite), npm, build, seo:generer
composer dev                    # serveur Laravel + file d'attente + Vite (en dev, le SSR passe par Vite)
npm run build                   # bundle client (public/build) + bundle SSR (bootstrap/ssr)
php artisan inertia:start-ssr   # serveur SSR Node de production (127.0.0.1:13728)
php artisan seo:generer         # génère public/sitemap.xml et robots.txt (llms.txt est écrit à la main)
php artisan avis:actualiser     # note et avis de la fiche Google (planifié chaque matin)
php artisan lang:update         # met à jour les traductions lang/fr (Laravel Lang)
```

Pas de tests automatisés ni de linter, par choix du projet : n'en ajoutez pas et gardez les dépendances au strict minimum. Pour valider un changement :

1. `npm run build`, qui compile aussi le bundle SSR et fait donc remonter les erreurs de template ;
2. `php -l` sur les fichiers PHP modifiés ;
3. contrôle d'une page rendue : avec `php artisan serve` et `php artisan inertia:start-ssr` lancés, le HTML doit contenir `data-server-rendered`.

## Architecture

**Chaîne de rendu.** Route nommée (`routes/web.php`) → contrôleur → `Inertia::render('Page', props)` → `resources/views/app.blade.php` → page Vue de `resources/js/pages/`. `resources/js/app.js` est l'unique point d'entrée, client et SSR (plugin `@inertiajs/vite`) : il applique `layouts/SiteLayout.vue` (en-tête et pied de page) à toutes les pages, sauf à celles de l'espace de gestion (`Admin/*`), qui déclarent `AdminLayout.vue` (`defineOptions({ layout })` : la mise en page d'une page l'emporte sur celle par défaut).

**Props partagées** (`app/Http/Middleware/HandleInertiaRequests.php`) :
- `site` : `config/site.php` (identité, contact, localisation), envoyée une fois par visite (`Inertia::once`). L'adresse e-mail du site n'y figure pas : elle n'est passée qu'aux pages Mentions légales et Confidentialité (prop `email`, `PageController`), pour ne pas apparaître dans le code des autres pages. Côté Vue, passer par le composable `useSite()`, qui ajoute `instagramUrl`, `telephoneUrl`, `googleUrl` (lien officiel de Google Maps vers la fiche Google, construit sur son Place ID `google_place_id`, réglable par `GOOGLE_PLACE_ID`) et `reseaux` (liste utilisée par la carte contact et le pied de page).
- `ziggy` : configuration des routes, utile seulement au rendu SSR (le navigateur utilise `@routes`).
- `canonical` : toujours construite à partir de `APP_URL`, jamais de l'hôte de la requête.

Les messages ponctuels passent par `Inertia::flash()` / `page.flash` (`demandeEnvoyee`, `sessionExpiree`, `bonCadeau`).

**Ziggy.** `route()` est global dans les templates. Dans `<script setup>`, écrire `const route = inject('route')` pour que le code fonctionne en SSR. Les réponses de la FAQ sont du HTML avec des liens écrits en dur (`/tarifs`, `/contact`) : à reprendre si une URL change. Les routes techniques et celles de l'espace de gestion (`admin.*`) sont exclues dans `config/ziggy.php`, où `skip-route-function` limite `@routes` à la liste des routes : la fonction `route()` vient du bundle (`ZiggyVue`, dans `app.js`).

**Données de contenu** (`resources/js/data/`). Les textes vivent dans les pages Vue ; ce qui est repris à plusieurs endroits est centralisé ici :
- `seances.js` : prix, durées et contenu des séances. C'est la source unique : pages, FAQ, CGV, meta descriptions et JSON-LD en dépendent. Ne jamais écrire un prix en dur dans une page.
- `conditions.js` : acompte, paiement (le bon cadeau se règle en totalité), livraison, report et annulation, validité du bon cadeau, zone de déplacement. Mélanie les a validées en octobre 2026. Les photos se choisissent avec Mélanie, sur place, à la fin de la séance, et sont livrées retouchées sous 3 jours : pas de galerie en ligne, et le site ne dit pas par quel moyen elles sont envoyées (à sa demande). Zone : 40 km autour de Dijon (`zone`, repris par la FAQ et les CGV ; `deplacements`, la mention courte « Déplacements sur Dijon et alentours (40 km) » de l'accueil, des tarifs, de l'appel à réserver et du pied de page) et 20 communes desservies (`communes`, avec leur code postal), listées sur l'accueil et reprises dans les données structurées et `llms.txt`.
- `faq.js` : questions et réponses (HTML), reprises par la page FAQ, les pages séances et la page tarifs. Seule la page FAQ les balise en JSON-LD `FAQPage` : Google demande de ne baliser chaque question qu'une fois sur tout le site. Les questions ont été fournies par Mélanie : ne pas les reformuler ni les supprimer sans son accord.
- `parcours.js` : le parcours de Mélanie, qu'elle a confirmé (octobre 2026) : maman de quatre enfants, formation en ligne Domestika « Introduction à la photographie newborn » (Guillermo Casas et Marina Gondra, Le Photograph ; certificat du 30 septembre 2026, avec un volet sur la sécurité du nouveau-né). Repris sur les pages nouveau-né (avec sa vaccination contre la coqueluche et son métier dans la santé) et « Mon univers », sur l'accueil, dans le `founder` du `LocalBusiness` (`hasCredential`) et `llms.txt`.
- `seo.js` : générateurs JSON-LD (voir SEO).

Les clés `grossesse`, `nouveau-ne` et `famille` relient toutes les couches :
- les URL silo `/photographe-{clé}-dijon` et `/portfolio/{clé}` ;
- `DemandeContactRequest::SEANCES`, qui y ajoute `autres`, un choix propre au formulaire de contact ;
- `BonCadeau::SEANCES`, les séances qui peuvent être offertes ;
- `Photos::DOSSIERS` et les dossiers de `resources/photos/` ;
- `data/seances.js` et `data/portfolio.js` ;
- `config/seo.php`.

Ajouter un type de séance, c'est modifier tous ces endroits.

**Photos** (`app/Support/Photos.php`). Les photos sources sont dans `resources/photos/{grossesse,nouveau-ne,famille,site}/`, triées par nom de fichier. Leur rôle dépend de leur rang (la 1re est la photo principale de la page séance, etc.) ou, dans `site/`, de leur nom (`accueil`, `portrait`, `bon-cadeau`) : voir `resources/photos/README.md`.

Les contrôleurs passent aux pages des objets `{src, srcset, largeur, hauteur, alt}` ; le texte `alt` est tiré du nom de fichier. Les variantes WebP (`/photos/{largeur}/{dossier}/{nom}-{empreinte}.webp`) sont créées à la première demande par `PhotoController`, puis servies comme fichiers statiques depuis `public/photos` (non versionné). S'il n'y a pas de photo, `components/Photo.vue` affiche un emplacement aux couleurs du site.

**SEO.** Chaque page inclut `<Seo titre="…" description="…" :json-ld="…" />`, avec un titre sans suffixe. Le composant ajoute « | Mélanie Photographie » seulement si le titre complet tient en 60 caractères (Google tronque au-delà, et affiche de toute façon le nom du site) : viser 37 caractères au plus pour garder la marque. La description fait 155 caractères au plus. Le composant ajoute aussi l'URL canonique et les balises Open Graph et Twitter ; l'image de partage est toujours `og-image.jpg` (1200 × 630, avec type, dimensions et texte alternatif). Le favicon (16, 32 et 48 px), l'icône Apple, le manifeste et la couleur de thème sont déclarés dans `app.blade.php`.

Données structurées (JSON-LD, `data/seo.js`), validées sans erreur ni avertissement sur validator.schema.org :
- accueil : `WebSite` + `LocalBusiness` (l'entreprise, avec le catalogue des séances ; son `sameAs`, Instagram et la fiche Google, relie le site à la fiche) ; à propos et contact : `AboutPage` / `ContactPage` + `LocalBusiness` ;
- pages séances et tarifs : un `Service` par séance, avec le même `@id` partout (`/#seance-{clé}`) ; bon cadeau : `Service` avec une `AggregateOffer` ;
- FAQ : `FAQPage`, seule page qui balise les questions ;
- portfolio : `CollectionPage`, puis une `ImageGallery` par catégorie dont chaque photo est un `ImageObject` (créateur, crédit, droits d'auteur) ;
- fil d'Ariane : `BreadcrumbList` (`FilAriane.vue`) ;
- zone desservie (`areaServed` de l'entreprise, des séances et du bon cadeau) : une `City` par commune de `conditions.js` ;
- toutes les URL sont construites sur `APP_URL` (`adresse()` de `seo.js`), comme l'URL canonique ;
- ne rien inventer : pas de rue, de coordonnées GPS, d'horaires ni d'avis tant que Mélanie ne les a pas fournis.

La commande `php artisan seo:generer` (`app/Console/Commands/GenererFichiersSeo.php`) écrit, sans charger aucune page :
- `public/sitemap.xml` : l'adresse de chaque page indexable listée dans `config/seo.php`, rien d'autre ;
- `public/robots.txt` : tous les robots autorisés partout (`Allow: /`, `Disallow:` vide) et l'adresse du sitemap.

Elle se lance à chaque déploiement ; ces deux fichiers ne sont pas versionnés. **Toute nouvelle page publique doit être ajoutée à `config/seo.php`.**

**`public/llms.txt` est rédigé à la main par Claude et versionné.** Il résume le site pour les assistants IA (séances, prix, conditions, zone, contact) et liste ses pages, avec les URL de production. Le mettre à jour dans le même commit que tout changement qui le concerne : page ajoutée, supprimée ou renommée, prix, durée ou contenu d'une séance (`seances.js`), conditions (`conditions.js`), zone de déplacement, coordonnées, réseaux sociaux. N'y mettre que des informations publiées sur le site.

**Avis Google** (`App\Support\FicheGoogle`, commande `avis:actualiser`, tables `note_google` et `avis_google`). Lus par la Business Profile API, avec l'accès OAuth du compte propriétaire de la fiche (`GOOGLE_BUSINESS_CLIENT_ID`, `GOOGLE_BUSINESS_CLIENT_SECRET`, `GOOGLE_BUSINESS_REFRESH_TOKEN` ; sans eux, la commande ne fait rien) ; la fiche est retrouvée par son Place ID. La commande, planifiée chaque matin à 6 h (`routes/console.php`), enregistre la note globale et le nombre d'avis. Elle garde tous les avis, même une note sans commentaire (texte vide, remplacé à l'affichage par « Sans commentaire ») : modifiés ou supprimés sur Google, ils le sont aussi sur le site. Mélanie choisit ceux qui sont affichés dans l'espace de gestion (colonne `affiche`), et la commande ne touche pas à ce choix : elle n'écrit `affiche` que pour un nouvel avis, toujours masqué (`false`), même au premier import ; il n'apparaît sur l'accueil que si Mélanie l'affiche. Ordre de l'accueil (`AvisGoogle::ordreDuSite`) : les mieux notés, ceux qui ont un texte avant les autres à note égale, puis les plus récents.
- Si la mise à jour échoue, la note et les avis enregistrés restent affichés, sans limite de durée, et la commande réessaie chaque matin. C'est un choix de Mélanie (octobre 2026), contraire aux règles des Business Profile APIs (contenu stocké 30 jours au plus), dont la violation permet à Google de désactiver le projet API : ne pas réintroduire d'effacement sans son accord. Aucun avis n'est modifié. `updated_at` date la dernière actualisation : un choix d'affichage ne la change pas (`withoutTimestamps`).
- Une erreur passagère de Google (service indisponible ou surchargé, coupure réseau) est retentée trois fois, après 2, 10 et 30 secondes (`FicheGoogle::requete`). Si l'actualisation échoue malgré tout, l'erreur est seulement consignée dans le journal de Laravel (`Log::error`, dans `storage/logs`) : à la demande de Mélanie, le site n'envoie aucun e-mail d'erreur. La page « Avis Google » de l'espace de gestion affiche la date de la dernière mise à jour réussie. Le jeton d'actualisation n'expire pas tant qu'il sert et que l'écran de consentement OAuth est publié ; la façon de le créer est décrite dans le README (« Avis Google »).
- RGPD : de l'auteur, seuls le prénom et l'initiale du nom sont gardés ; aucune photo de profil (elle ferait charger une image depuis Google).
- La section (`components/AvisGoogle.vue`, sur l'accueil) n'apparaît que si la fiche a une note ; la politique de confidentialité n'a sa section « Avis Google » que si des avis sont enregistrés. Sur mobile, les avis défilent de côté, avec une flèche de chaque côté, posée sur les cartes, qui se cache quand le bandeau est au bout ; à partir de `md`, ils sont en colonnes, sans flèches.
- Pas de JSON-LD pour ces avis ni pour la note : Google ignore les avis qu'une entreprise publie sur elle-même et interdit d'y reprendre ceux d'un autre site.

**Espace de gestion** (`/admin`). Deux rubriques, bons cadeaux et avis Google, liées dans l'en-tête de `AdminLayout.vue` ; sous `md`, elles passent dans un menu burger, déroulé sous l'en-tête, qui se ferme au changement de page, sur Échap et au clic à côté.
- Accès : une seule personne, avec `ADMIN_EMAIL` et `ADMIN_MOT_DE_PASSE` (`config/admin.php`, `App\Support\Admin` ; mot de passe en clair ou empreinte bcrypt). Sans eux, toutes les adresses `/admin…` répondent 404 (middleware `EspaceAdmin`). La session garde un jeton tiré des identifiants : les changer déconnecte tout le monde. Connexion limitée à 5 essais par minute. Une action envoyée après l'expiration de la session ramène à la connexion, avec le flash `sessionExpiree`. Pages jamais indexées (`noindex` dans `AdminLayout.vue` et en-tête `X-Robots-Tag`), absentes du sitemap ; leurs routes étant exclues de Ziggy, les pages `Admin/*` écrivent leurs adresses en dur.
- Avis Google (`/admin/avis`, `Admin\AvisGoogleController`) : les avis gardés, les plus récents d'abord, avec un interrupteur « Afficher sur le site » par avis (`role="switch"`). L'interrupteur change d'état sans attendre la réponse du serveur (option `optimistic` d'Inertia, annulée si l'enregistrement échoue).

**Bons cadeaux** (`/admin/bons-cadeaux`). Mélanie y crée les bons, télécharge leur image à imprimer et suit ceux déjà créés (valides, utilisés, expirés ; recherche par nom ou numéro). Dans la liste, les actions sur un bon (télécharger, modifier, marquer comme utilisé ou l'annuler, supprimer) sont des boutons à icône seule, sans texte : chacun est nommé par `aria-label` (avec le numéro du bon, pour les lecteurs d'écran) et `title` (bulle au survol).
- Table `bons_cadeaux`, modèle `App\Models\BonCadeau` : numéro unique attribué à la création (`MP-` et 6 caractères qui ne se confondent pas, pour être dicté au téléphone), bénéficiaire, « de la part de », séance, message, dates d'émission, de fin de validité et d'utilisation. Le statut (valide, utilisé, expiré) se calcule à la date du jour en France. Les longueurs maximales (`BonCadeauRequest`) sont celles qui tiennent sur l'image.
- Image à imprimer (`resources/js/admin/bonCadeau.js`) : dessinée dans le navigateur (canvas), avec les polices du site, `monogramme-mp-impression.webp` (750 px, réservé à l'impression) et les fleurs ; A5 paysage à 300 dpi (2480 × 1748 px), téléchargée en JPEG avec cette densité dans son en-tête. Les textes trop longs rapetissent, puis passent sur deux lignes ; l'aperçu de la page est dessiné à sa taille d'affichage, plus net. Le contenu vient de `seances.js` (nom, accroche, ce qui est inclus), la validité proposée de `validiteBonCadeauMois` (`conditions.js`), la réservation du téléphone et du domaine du site. Seules les informations du bon sont gardées : l'image est redessinée à chaque téléchargement, et suit donc les changements de `seances.js`, anciens bons compris.
- RGPD : la politique de confidentialité décrit ces données. Les bons sont effacés trois ans après la fin de leur validité ou leur utilisation (`BonCadeau::prunable`, commande `model:prune` planifiée chaque matin).

**Erreurs** (`bootstrap/app.php`). Les codes 403, 404, 429, 500 et 503 affichent la page Inertia `Erreur` (en mode debug, les erreurs 500 gardent la page détaillée de Laravel). Une 419 (session expirée) ramène l'utilisateur sur la page précédente avec le flash `sessionExpiree`. Elle est devenue rare : Laravel 13 (`PreventRequestForgery`) accepte sans jeton CSRF les requêtes que le navigateur déclare venues du site lui-même (en-tête `Sec-Fetch-Site`).

**Adresses uniques.** Chaque page n'a qu'une adresse. Le middleware global `RefuserAdressesEnDouble` renvoie une 404, sans redirection, aux variantes que Laravel accepterait sinon : barre oblique finale (`/tarifs/`), `index.php` dans l'adresse (`/index.php/tarifs`), caractère encodé (`/%74arifs`). Une adresse du site ne doit donc jamais finir par `/` ni contenir de `%` (les noms des photos passent par `Str::slug`). Les paramètres (`?seance=…`, `?utm_…`) restent acceptés : l'URL canonique les écarte.

**Contact.** `ContactController` et `DemandeContactRequest` (champ piège `site_web`, 5 envois par minute et par adresse IP au maximum) envoient `App\Mail\DemandeContact` à l'adresse du site, `MAIL_FROM_ADDRESS` (`config('mail.from.address')`), qui l'expédie aussi : OVH redirige ce qu'elle reçoit vers la boîte personnelle de Mélanie, jamais affichée. Le contact passe par téléphone : pas de champ e-mail, numéro obligatoire et vérifié par `App\Support\Telephone` (10 chiffres français hors 08, ou format international ; faux numéros évidents refusés). Mélanie rappelle le client, « généralement dans l’heure » (texte de `Contact.vue`, repris dans `llms.txt`) ; l'objet de l'e-mail donne le nom et le numéro, cliquable dans le tableau de l'e-mail. Sa mise en page (`resources/views/vendor/mail/`) affiche le logo en en-tête et n'a pas de pied de page. Ce logo, `public/images/marque/logo-mp-email.png`, est tiré de `logo-mp.webp` et posé sur une carte de la couleur du fond de l'e-mail (#fafafa), qui le garde lisible quand Gmail assombrit le fond : le refaire, sous un nouveau nom, si le logo change. L'adresse e-mail du site n'apparaît que dans les mentions légales et la politique de confidentialité, où la loi l'impose : ailleurs (carte contact, pied de page, données structurées, `llms.txt`), le contact passe par le téléphone, Instagram et le formulaire. Les paramètres `/contact?seance=…` et `?objet=bon-cadeau` pré-remplissent le formulaire : le premier contact passe par cette page, d'où les boutons « Me contacter » sous chaque prix (`CarteTarif.vue`, séance cochée), sous les tarifs de l'accueil et sur la carte bon cadeau de la page tarifs. Mélanie préfère ce formulaire au téléphone et à Instagram, qui restent possibles : les textes qui expliquent comment la contacter (FAQ « Comment réserver une séance photo ? », étapes de la page bon cadeau, `llms.txt`) le proposent en premier. Les champs obligatoires (prénom, nom, téléphone, type de séance, message) ont un astérisque rouge ; les champs facultatifs n'ont aucune mention.

Tous les formulaires du site, espace de gestion compris, utilisent les mêmes composants : `components/Champ.vue` pour les champs (libellé, aide, erreur ; avec `revelable`, un bouton en forme d'œil affiche le mot de passe, de nouveau masqué à l'envoi du formulaire) et `components/ChoixSeances.vue` pour les types de séance (cases illustrées : plusieurs choix ici, un seul pour un bon cadeau). En cas d'erreur, la bordure et le message sont en `rouge` (5,2:1 sur `creme-50`). Chaque champ de saisie a un texte indicatif (`placeholder`, « Ex. : … » pour un exemple), sauf les dates, que le navigateur présente lui-même. Une erreur disparaît dès que le champ est modifié, et après un envoi refusé le premier champ en erreur reçoit le focus.

Anti-spam : champ piège, limite d'envois et hCaptcha (`components/Captcha.vue`, `App\Support\Captcha`). Le script d'hCaptcha n'est chargé qu'à l'approche de la case « Je suis un humain ». Le jeton n'est vérifié qu'une fois les autres champs valides (il ne sert qu'une fois) ; si hCaptcha ne répond pas, la demande passe. Clés `HCAPTCHA_SITE_KEY` et `HCAPTCHA_SECRET` dans le `.env` : sans elles, pas de vérification (clés de test dans `.env.example`). La politique de confidentialité mentionne hCaptcha : la tenir à jour si le service change.

## Images : toujours en WebP optimisé

Toute image affichée sur le site est en WebP optimisé, avec un `alt` descriptif (voir Règles de contenu). À chaque ajout ou remplacement d'image :

1. **Photo** (séance, accueil, portrait, bon cadeau…) : déposer le fichier d'origine, en grand, dans `resources/photos/{dossier}/` (JPEG, PNG ou WebP). Rien à convertir : le site crée lui-même les variantes WebP redimensionnées (480 à 2000 px de large, qualité 82). Ne jamais mettre une photo dans `public/`.
2. **Autre image** (logo, décor, illustration) : la convertir en WebP avant de l'ajouter à `public/images/`, par exemple avec Pillow (`Image.open('image.png').save('image.webp', 'WEBP', quality=80, method=6)`) ou `cwebp -q 80 -m 6`.
   - Largeur : 2 à 3 fois la largeur d'affichage maximale.
   - Qualité : 80, ou 85 pour un logo aux bords nets. Pour une image détourée à la transparence fine (fleurs à l'aquarelle), ajouter `alpha_quality=50` : la transparence pèse souvent plus que l'image, et la différence ne se voit pas.
   - Pas de `loading="lazy"` sur une image qui peut s'afficher en haut de page : différer le plus grand élément visible (LCP) retarde tout l'affichage. Sur mobile, une fleur en haut de page doit rester plus petite que le titre : sinon elle devient le LCP, mesuré à l'arrivée de l'image et non à l'affichage du texte (d'où `w-36` sur la page tarifs). Options de `Fleur.vue` : `prioritaire` (chargement en priorité) pour la fleur qui est le plus grand élément de sa page sur grand écran (tarifs) ; `differee` (chargement à l'approche) seulement pour une fleur toujours loin du haut de page (appel à réserver, pied de page, milieu de page) ; `visible-des="md"` (ou `sm`, `lg`) pour une fleur masquée sur petit écran (`hidden md:block`), car un navigateur télécharge une image même masquée : la balise `<picture>` ne lui envoie rien sous ce point de rupture.
   - Ensuite : utiliser le `.webp` dans le code, avec ses vraies dimensions dans `width` et `height`, et ne pas garder l'original dans `public/`.
   - Remplacement d'une image sous le même nom : Cloudflare et les navigateurs la gardent en cache jusqu'à un an. Après le déploiement, purger le cache Cloudflare (ou donner un nouveau nom au fichier).
3. **Vérifier** : `grep -rnE "\.(png|jpe?g)" resources/js resources/views public/site.webmanifest` ne doit lister que les exceptions ci-dessous.

Exceptions, à garder dans leur format, car le WebP n'y est pas lu partout :
- `favicon.ico`, `apple-touch-icon.png` et les icônes PNG de `public/site.webmanifest` : formats exigés par les navigateurs et iOS ;
- l'image de partage `og-image.jpg` (balise `og:image` et `image` des données structurées) : LinkedIn et d'anciennes versions de WhatsApp n'affichent pas le WebP ;
- les images d'e-mails : Outlook (Windows et macOS) affiche une image cassée à la place d'un WebP ;
- l'image à imprimer des bons cadeaux, téléchargée en JPEG (`bonCadeau.js`) : le format qu'attendent imprimeurs et laboratoires photo. Elle n'est pas affichée sur le site.

## Style (Tailwind CSS 4)

- Couleurs et polices sont définies dans le bloc `@theme` de `resources/css/app.css` : `creme`, `poudre`, `taupe`, `cacao`, `brique`, `or`, `rouge`.
- **Contraste (WCAG AA, contrôlé par PageSpeed)** : tout texte doit atteindre 4,5:1 (3:1 à partir de 24 px, ou 18,66 px en gras).
  - Sur les fonds crème et poudre, `taupe-500` est la couleur de texte la plus claire autorisée.
  - `taupe-400`, `or-300` à `or-500` et `poudre-*` servent aux décors, bordures et icônes, jamais au texte ; `or-600` passe pour du texte sur `creme-50` et `creme-100` seulement.
  - Un petit texte posé sur un décor (fleur, pinceau) doit avoir un fond uni derrière lui, comme le fil d'Ariane (`FilAriane.vue`).
- Utilitaires maison (en `@utility`) : `surtitre`, `manuscrit`, `conteneur`, `texte-courant`. La classe `.prose-mp` met en forme le HTML de la FAQ et des pages légales ; `.a-completer` signale les champs à remplir.
- Pièges de Tailwind 4 : une classe maison doit être déclarée en `@utility` pour marcher avec `@apply` et les variantes ; le modificateur important s'écrit en suffixe (`px-6!`).
- Polices : Gilda Display pour les titres, Jost pour le texte, Allison (manuscrite) pour les phrases courtes seulement. Leurs fichiers WOFF2 (sous-ensemble latin) sont dans `resources/fonts`, déclarés avec `local()` dans `vite.config.js` : aucun appel externe (RGPD). `optimizedFallbacks` (paquet `fontaine`) affiche une police système aux mêmes dimensions le temps du chargement, sans faire bouger le texte.
  - Ne pas revenir à `bunny()` : avec les plages Unicode de Bunny, le plugin écrit une règle `@font-face` par format, et la règle WOFF remplace la WOFF2 préchargée (chaque police téléchargée deux fois). Graisses explicites (`variants`) : le plugin lit « normal » dans un nom de fichier comme 400.
  - La police de secours générée au build (« Jost fallback »…) est réglée sur Arial, absente d'Android et de Linux. `app.css` ajoute donc, aux mêmes dimensions, « … fallback Roboto » (Android, d'où viennent les mesures Core Web Vitals de Google) et « … fallback Liberation » (Linux).
  - Si une police change, recalculer ces valeurs avec `readMetrics` (paquet `fontaine`), comme le plugin : `size-adjust` = (xWidthAvg ÷ unitsPerEm de la police) ÷ (même rapport pour la police de secours) ; ascent, descent et line-gap = valeur ÷ (unitsPerEm × size-adjust).
  - Dans `@theme`, chaque police est suivie de ses polices de secours, dans cet ordre : `'Jost', 'Jost fallback', 'Jost fallback Roboto', 'Jost fallback Liberation', …`.
- Boutons : `components/Bouton.vue`, variantes `plein`, `contour` et `lien`. Une ancre `#…` ou un lien `externe` donne une balise `<a>` classique, sans navigation Inertia.

## Règles de contenu

- Ne jamais attribuer à Mélanie une formation, une certification ou un label qu'elle n'a pas (notamment sur la sécurité des nouveau-nés). Sa seule formation connue est le cours en ligne de `parcours.js`, sanctionné par un certificat de suivi : ne pas parler de diplôme ni de certification en sécurité. Faits confirmés par elle : maman de quatre enfants, vaccinée contre la coqueluche, passionnée de photo depuis toute petite, travaille dans le milieu de la santé, activité de photographe lancée il y a moins de deux ans (non mentionné sur le site).
- Ne pas inventer d'informations légales : les mentions `[À compléter]` des pages Mentions légales et CGV restent à remplir par Mélanie.
- **Toute image a un texte alternatif (`alt`) descriptif, jamais vide** :
  - photos : texte tiré du nom de fichier (voir Photos) ;
  - logo et monogramme : « Mélanie Photographie » ;
  - ornements (`Fleur.vue`, monogramme au-dessus d'un titre) : courte description, et `aria-hidden="true"` pour que les lecteurs d'écran les ignorent.

## Production

- `APP_URL` doit être l'URL https avec www (`https://www.melanie-photographie.fr`) : les URL canoniques, le sitemap et `robots.txt` en dépendent. En production, le middleware global `RedirigerVersWww` redirige en 301 le domaine sans www vers cet hôte (les fichiers statiques ne passent pas par Laravel). Nginx ne doit jamais rediriger www vers le domaine sans www : boucle de redirections.
- Hébergement sur Laravel Forge : le serveur SSR tourne dans le daemon de l'option « Inertia SSR » ; l'option « Laravel Scheduler » crée la tâche cron qui lance `php artisan schedule:run` chaque minute, donc les tâches de `routes/console.php` (`avis:actualiser` et `model:prune`, chaque matin). La base de données de production n'est pas SQLite : elle survit aux déploiements, et les migrations y tournent à chaque déploiement. Déploiements sans interruption : chaque déploiement crée un nouveau dossier de release, où `sitemap.xml` et `robots.txt` n'existent que si `seo:generer` y tourne (générés à la main, ils disparaissent au déploiement suivant). Dans le script de déploiement : `npm run build` et `php artisan seo:generer` avant `$ACTIVATE_RELEASE()`, puis `php artisan inertia:stop-ssr --graceful` après (Forge relance le daemon avec le nouveau bundle ; sans `--graceful`, un serveur SSR arrêté fait échouer le déploiement).
- Le serveur SSR écoute sur `127.0.0.1:13728`, pas sur 13714, le port par défaut d'Inertia, déjà pris par d'autres sites du serveur. Les sites s'y arrêteraient les uns les autres, car `inertia:start-ssr` commence par envoyer `/shutdown` au port configuré. Le port est écrit dans `vite.config.js` (compilé dans le bundle SSR) et dans `config/inertia.php` : les changer ensemble.
- Sans serveur SSR actif, le site reste fonctionnel en rendu côté client.
- Cloudflare est devant le site. Les mentions légales (« Hébergement ») nomment Cloudflare, Inc., avec l'adresse et le téléphone publiés dans sa politique de confidentialité : les tenir à jour si l'hébergement change. Le middleware global `AdresseVisiteurCloudflare` remplace l'adresse IP vue par Laravel par celle de l'en-tête `CF-Connecting-IP`, seulement pour les requêtes venues des plages de Cloudflare (`App\Support\Cloudflare`, à mettre à jour si Cloudflare en publie de nouvelles). Ne pas lire `X-Forwarded-For` : un Worker Cloudflare peut y placer l'adresse de son choix, alors que Cloudflare impose la sienne dans `CF-Connecting-IP`. Dans Cloudflare, laisser désactivée la transformation « Remove visitor IP headers », qui supprime cet en-tête. Cette adresse sert à la limite d'envois du formulaire et est enregistrée avec la session : la politique de confidentialité (« Données de connexion ») annonce une expiration après deux heures d'inactivité, à garder en accord avec `SESSION_LIFETIME` (120).
- Réglages Cloudflare qui touchent le site : Web Analytics est actif (balise injectée par Cloudflare, sans cookie) et la politique de confidentialité le mentionne (« Mesure d’audience ») : la tenir à jour si ce réglage change. Bot Fight Mode est désactivé, mais l'offre gratuite ne permet pas de couper la détection JavaScript de Cloudflare : son script (`/cdn-cgi/challenge-platform/…`), injecté dans chaque page, occupe le processeur sur mobile, dépose le cookie `cf_clearance` et coûte des points en « Bonnes pratiques » (« Uses deprecated APIs » vient de ce script, pas du site). La politique de confidentialité la mentionne (« Protection contre les robots », « Cookies »).
- E-mails : boîte `contact@melanie-photographie.fr` chez OVH (MX Plan), redirigée vers la boîte personnelle de Mélanie. Le site envoie par le SMTP d'OVH (`smtp.mail.ovh.net`, port 587, STARTTLS, identifiants de la boîte). DNS chez Cloudflare : MX d'OVH, SPF `include:mx.ovh.com`, DKIM d'OVH (CNAME `ovhmo-selector-1` et `-2._domainkey`), DMARC.
- PHP doit avoir les extensions `gd` (WebP) et `exif`.
