<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\PortfolioController;
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
