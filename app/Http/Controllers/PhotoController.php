<?php

namespace App\Http\Controllers;

use App\Support\Photos;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PhotoController extends Controller
{
    /**
     * Sert une variante WebP ; une fois générée, le serveur web la sert directement depuis public/photos.
     */
    public function __invoke(Photos $photos, int $largeur, string $dossier, string $fichier): BinaryFileResponse
    {
        preg_match('/^(?<nom>[a-z0-9-]+)-(?<empreinte>[a-f0-9]{8})\.webp$/', $fichier, $parties) || abort(404);

        $chemin = $photos->variante($largeur, $dossier, $parties['nom'], $parties['empreinte']) ?? abort(404);

        return response()->file($chemin, [
            'Content-Type' => 'image/webp',
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }
}
