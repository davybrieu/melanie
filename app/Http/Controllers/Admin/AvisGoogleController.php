<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AvisGoogle;
use App\Models\NoteGoogle;
use App\Support\FicheGoogle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Avis Google : liste des avis avec un texte (commande avis:actualiser) et choix de ceux qui
 * sont affichés sur la page d'accueil.
 */
class AvisGoogleController extends Controller
{
    public function index(): Response
    {
        $note = NoteGoogle::first();

        return Inertia::render('Admin/Avis', [
            // Accès à la fiche configuré (GOOGLE_BUSINESS_*) : sans lui, aucun avis n'est importé.
            'configure' => FicheGoogle::actif(),
            'fiche' => $note ? [
                'note' => $note->note,
                'noteTexte' => $note->note === null ? null : number_format($note->note, 1, ',', ''),
                'nombre' => $note->nombre_avis,
                'actualiseLe' => $note->updated_at->timezone('Europe/Paris')->locale('fr')->translatedFormat('j F Y à G \h i'),
            ] : null,
            'avis' => AvisGoogle::latest('publie_le')->get()->map(fn (AvisGoogle $avis) => [
                'id' => $avis->id,
                'auteur' => $avis->auteur,
                'note' => $avis->note,
                'texte' => $avis->texte,
                'date' => $avis->publie_le->timezone('Europe/Paris')->locale('fr')->translatedFormat('j F Y'),
                'affiche' => $avis->affiche,
            ]),
        ]);
    }

    /** Affiche l'avis sur le site, ou le masque. */
    public function affichage(Request $request, AvisGoogle $avisGoogle): RedirectResponse
    {
        // updated_at date la dernière actualisation depuis Google : un choix d'affichage ne doit
        // pas la changer.
        AvisGoogle::withoutTimestamps(fn () => $avisGoogle->update(['affiche' => $request->boolean('affiche')]));

        return to_route('admin.avis');
    }
}
