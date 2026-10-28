<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PhotoRequest;
use App\Models\Photo;
use App\Support\Photos;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use RuntimeException;

/**
 * Photos du site : ajout, texte alternatif, ordre et suppression. Les pages publiques les
 * reçoivent par App\Support\Photos.
 */
class PhotosController extends Controller
{
    public function __construct(private Photos $photos) {}

    public function index(): Response
    {
        $donnees = fn (Photo $photo) => ['id' => $photo->id, ...$this->photos->publique($photo)];

        return Inertia::render('Admin/Photos', [
            // Photos de chaque séance, dans l'ordre du site.
            'seances' => collect(Photos::SEANCES)->mapWithKeys(fn (string $dossier) => [
                $dossier => Photo::seance($dossier)->get()->map($donnees),
            ]),
            // Photos du site, par emplacement (absent : emplacement vide).
            'emplacements' => (object) Photo::where('dossier', 'site')->whereNotNull('emplacement')->get()
                ->mapWithKeys(fn (Photo $photo) => [$photo->emplacement => $donnees($photo)])
                ->all(),
            // Taille maximale d'un envoi (réglages PHP du serveur) : le navigateur réduit chaque photo en dessous.
            'tailleMax' => UploadedFile::getMaxFilesize(),
            'coteMinimum' => Photos::COTE_MINIMUM,
        ]);
    }

    /** Une photo par envoi : le navigateur les envoie l'une après l'autre. */
    public function store(PhotoRequest $request): RedirectResponse
    {
        try {
            $this->photos->ajouter($request->file('photo'), $request->validated('dossier'), $request->validated('emplacement'), $request->validated('alt'));
        } catch (RuntimeException) {
            throw ValidationException::withMessages(['photo' => 'Cette photo est illisible : enregistrez-la en JPEG, puis réessayez.']);
        }

        return to_route('admin.photos');
    }

    public function update(PhotoRequest $request, Photo $photo): RedirectResponse
    {
        $this->photos->modifierAlt($photo, $request->validated('alt'));

        Inertia::flash('photos', 'Le texte alternatif est enregistré.');

        return to_route('admin.photos');
    }

    /** Photos de séance seulement : celles du site ont chacune leur emplacement. */
    public function deplacer(Request $request, Photo $photo): RedirectResponse
    {
        abort_if($photo->emplacement !== null, 404);

        $this->photos->deplacer($photo, $request->validate(['sens' => ['required', Rule::in(['avant', 'apres', 'premiere'])]])['sens']);

        return to_route('admin.photos');
    }

    public function destroy(Photo $photo): RedirectResponse
    {
        $this->photos->supprimer($photo);

        Inertia::flash('photos', 'La photo est supprimée du site.');

        return to_route('admin.photos');
    }
}
