<?php

use App\Http\Controllers\Admin\AvisGoogleController;
use App\Http\Controllers\Admin\BonCadeauController;
use App\Http\Controllers\Admin\ConnexionController;
use App\Http\Controllers\Admin\PhotosController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\PortfolioController;
use App\Http\Middleware\EspaceAdmin;
use App\Support\Photos;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'accueil'])->name('accueil');

// Pages séances (silo local)
Route::get('/photographe-grossesse-dijon', [PageController::class, 'grossesse'])->name('grossesse');
Route::get('/photographe-nouveau-ne-dijon', [PageController::class, 'nouveauNe'])->name('nouveau-ne');
Route::get('/photographe-famille-dijon', [PageController::class, 'famille'])->name('famille');

Route::get('/portfolio', [PortfolioController::class, 'index'])->name('portfolio');
Route::get('/portfolio/{categorie}', [PortfolioController::class, 'categorie'])
    ->whereIn('categorie', ['grossesse', 'nouveau-ne', 'famille'])
    ->name('portfolio.categorie');

Route::get('/a-propos', [PageController::class, 'aPropos'])->name('a-propos');
Route::get('/tarifs', [PageController::class, 'tarifs'])->name('tarifs');
Route::get('/bon-cadeau', [PageController::class, 'bonCadeau'])->name('bon-cadeau');
Route::get('/faq', [PageController::class, 'faq'])->name('faq');

Route::get('/contact', [ContactController::class, 'create'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:5,1')->name('contact.envoyer');

Route::get('/mentions-legales', [PageController::class, 'mentionsLegales'])->name('mentions-legales');
Route::get('/confidentialite', [PageController::class, 'confidentialite'])->name('confidentialite');
Route::get('/cgv', [PageController::class, 'cgv'])->name('cgv');

// Variantes WebP des photos (générées à la première demande)
Route::get('/photos/{largeur}/{dossier}/{fichier}', PhotoController::class)
    ->whereNumber('largeur')
    ->whereIn('dossier', Photos::DOSSIERS)
    ->name('photo');

// Espace de gestion (bons cadeaux, avis Google, photos), réservé à Mélanie (identifiants ADMIN_* du
// .env ; sans eux, 404). Ces routes sont exclues de Ziggy : les pages Admin/* écrivent leurs adresses.
Route::prefix('admin')->name('admin.')->middleware(EspaceAdmin::class)->group(function () {
    Route::get('/connexion', [ConnexionController::class, 'create'])->name('connexion');
    Route::post('/connexion', [ConnexionController::class, 'store'])->middleware('throttle:5,1')->name('connexion.envoyer');
    Route::post('/deconnexion', [ConnexionController::class, 'destroy'])->name('deconnexion');

    Route::redirect('/', '/admin/bons-cadeaux')->name('accueil');
    Route::get('/bons-cadeaux', [BonCadeauController::class, 'index'])->name('bons-cadeaux');
    Route::post('/bons-cadeaux', [BonCadeauController::class, 'store'])->name('bons-cadeaux.creer');
    Route::put('/bons-cadeaux/{bonCadeau}', [BonCadeauController::class, 'update'])->name('bons-cadeaux.modifier');
    Route::patch('/bons-cadeaux/{bonCadeau}/utilisation', [BonCadeauController::class, 'utilisation'])->name('bons-cadeaux.utilisation');
    Route::delete('/bons-cadeaux/{bonCadeau}', [BonCadeauController::class, 'destroy'])->name('bons-cadeaux.supprimer');

    Route::get('/avis', [AvisGoogleController::class, 'index'])->name('avis');
    Route::patch('/avis/{avisGoogle}', [AvisGoogleController::class, 'affichage'])->name('avis.affichage');

    Route::get('/photos', [PhotosController::class, 'index'])->name('photos');
    Route::post('/photos', [PhotosController::class, 'store'])->name('photos.ajouter');
    Route::patch('/photos/{photo}', [PhotosController::class, 'update'])->name('photos.modifier');
    Route::patch('/photos/{photo}/position', [PhotosController::class, 'deplacer'])->name('photos.deplacer');
    Route::delete('/photos/{photo}', [PhotosController::class, 'destroy'])->name('photos.supprimer');
});
