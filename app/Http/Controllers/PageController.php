<?php

namespace App\Http\Controllers;

use App\Models\AvisGoogle;
use App\Models\NoteGoogle;
use App\Support\Photos;
use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    public function __construct(private Photos $photos) {}

    public function accueil(): Response
    {
        return Inertia::render('Accueil', [
            'photos' => [
                'hero' => $this->photos->nommee('accueil', 'grossesse'),
                'univers' => [
                    'grossesse' => $this->photos->une('grossesse'),
                    'nouveau-ne' => $this->photos->une('nouveau-ne'),
                    'famille' => $this->photos->une('famille'),
                ],
                'mosaique' => $this->melanger(['grossesse', 'nouveau-ne', 'famille'], 2, 1),
                'portrait' => $this->photos->nommee('portrait'),
            ],
            'avis' => $this->avisGoogle(),
        ]);
    }

    public function grossesse(): Response
    {
        return $this->seance('Grossesse', 'grossesse');
    }

    public function nouveauNe(): Response
    {
        return $this->seance('NouveauNe', 'nouveau-ne');
    }

    public function famille(): Response
    {
        return $this->seance('Famille', 'famille');
    }

    public function aPropos(): Response
    {
        return Inertia::render('APropos', [
            'photos' => [
                'portrait' => $this->photos->nommee('portrait'),
            ],
        ]);
    }

    public function tarifs(): Response
    {
        return Inertia::render('Tarifs', [
            'photos' => [
                'grossesse' => $this->photos->une('grossesse'),
                'nouveau-ne' => $this->photos->une('nouveau-ne'),
                'famille' => $this->photos->une('famille'),
            ],
        ]);
    }

    public function bonCadeau(): Response
    {
        return Inertia::render('BonCadeau', [
            'photo' => $this->photos->nommee('bon-cadeau', 'famille', 1),
        ]);
    }

    public function faq(): Response
    {
        return Inertia::render('Faq');
    }

    // Adresse e-mail du site (MAIL_FROM_ADDRESS) : seulement dans ces deux pages, où la loi
    // l'impose (pas dans les props partagées, sinon elle figurerait dans le code de chaque page).
    public function mentionsLegales(): Response
    {
        return Inertia::render('Legal/MentionsLegales', ['email' => config('mail.from.address')]);
    }

    public function confidentialite(): Response
    {
        return Inertia::render('Legal/Confidentialite', [
            'email' => config('mail.from.address'),
            'avisGoogle' => AvisGoogle::aJour()->exists(),
        ]);
    }

    public function cgv(): Response
    {
        return Inertia::render('Legal/Cgv');
    }

    /**
     * Note de la fiche Google et avis choisis (commande avis:actualiser), les mieux notés puis
     * les plus récents d'abord ; null tant que la fiche n'a pas de note.
     *
     * @return array{note: float, noteTexte: string, nombre: int, avis: list<array{auteur: string, note: int, texte: string, date: string}>}|null
     */
    private function avisGoogle(): ?array
    {
        $note = NoteGoogle::aJour()->first();

        if ($note?->note === null) {
            return null;
        }

        return [
            'note' => $note->note,
            'noteTexte' => number_format($note->note, 1, ',', ''),
            'nombre' => $note->nombre_avis,
            'avis' => AvisGoogle::aJour()->orderByDesc('note')->orderByDesc('publie_le')->get()
                ->map(fn (AvisGoogle $avis) => [
                    'auteur' => $avis->auteur,
                    'note' => $avis->note,
                    'texte' => $avis->texte,
                    'date' => $avis->publie_le->locale('fr')->translatedFormat('F Y'),
                ])
                ->all(),
        ];
    }

    /**
     * Pages « photographe-{séance}-dijon » : la 1re photo du dossier sert de photo principale.
     */
    private function seance(string $page, string $dossier): Response
    {
        return Inertia::render($page, [
            'photos' => [
                'hero' => $this->photos->une($dossier),
                'grandes' => $this->photos->dossier($dossier, 2, 1),
                'galerie' => $this->photos->dossier($dossier, 9, 3),
            ],
        ]);
    }

    /**
     * Alterne les photos de plusieurs dossiers (g1, n1, f1, g2, n2, f2…).
     *
     * @param  list<string>  $dossiers
     * @return list<array<string, mixed>>
     */
    private function melanger(array $dossiers, int $parDossier, int $decalage = 0): array
    {
        $listes = array_map(fn (string $d) => $this->photos->dossier($d, $parDossier, $decalage), $dossiers);

        return array_values(array_filter(array_merge(...array_map(null, ...$listes))));
    }
}
