<?php

namespace App\Console\Commands;

use App\Models\AvisGoogle;
use App\Models\NoteGoogle;
use App\Support\FicheGoogle;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Actualise la note globale, le nombre d'avis et les avis, depuis la fiche Google.
 *
 * Lancée chaque matin (routes/console.php). Elle garde tous les avis, même sans texte (note
 * seule) : modifiés ou supprimés sur Google, ils le sont aussi sur le site. Un nouvel avis arrive
 * masqué : les avis à afficher se choisissent dans l'espace de gestion, et ce choix est conservé.
 * Les règles de Google limitent ce stockage à 30 jours : sans actualisation réussie depuis, les
 * données sont effacées. Un échec est consigné dans le journal de Laravel, sans e-mail.
 */
class ActualiserAvisGoogle extends Command
{
    protected $signature = 'avis:actualiser';

    protected $description = 'Actualise la note et les avis de la fiche Google';

    public function handle(FicheGoogle $ficheGoogle): int
    {
        if (! FicheGoogle::actif()) {
            $this->warn('Accès à la fiche Google non configuré (GOOGLE_BUSINESS_* dans le .env) : rien à actualiser.');

            return self::SUCCESS;
        }

        try {
            $fiche = $ficheGoogle->lire();
        } catch (Throwable $exception) {
            // Seulement dans le journal (storage/logs) : le site n'envoie aucun e-mail d'erreur. Une
            // panne passagère de Google se règle en général d'elle-même avant le lendemain matin.
            Log::error('Avis Google : actualisation impossible.', [
                'erreur' => $exception->getMessage(),
                'derniere_reussite' => NoteGoogle::find(1)?->updated_at?->toIso8601String(),
            ]);
            $this->error("Actualisation impossible : {$exception->getMessage()}");
            $this->effacerPerimes();

            return self::FAILURE;
        }

        NoteGoogle::upsert([['id' => 1, 'note' => $fiche['note'], 'nombre_avis' => $fiche['nombre']]], ['id'], ['note', 'nombre_avis']);

        $avis = collect($fiche['avis']);

        AvisGoogle::whereNotIn('identifiant', $avis->pluck('identifiant'))->delete();

        // Un nouvel avis arrive masqué. « affiche » n'est écrit qu'à son ajout : un avis déjà gardé
        // conserve le choix fait dans l'espace de gestion.
        if ($avis->isNotEmpty()) {
            AvisGoogle::upsert(
                $avis->map(fn (array $unAvis) => [...$unAvis, 'affiche' => false])->values()->all(),
                ['identifiant'],
                ['auteur', 'note', 'texte', 'publie_le'],
            );
        }

        $this->line(sprintf(
            '  Note %s/5 (%d avis sur Google) · %d avis gardés, dont %d sans commentaire · %d affichés',
            $fiche['note'] === null ? '—' : number_format($fiche['note'], 1, ',', ''),
            $fiche['nombre'],
            $avis->count(),
            $avis->where('texte', '')->count(),
            AvisGoogle::affiches()->count(),
        ));

        return self::SUCCESS;
    }

    /** Règles de Google : rien n'est gardé plus de 30 jours sans actualisation. */
    private function effacerPerimes(): void
    {
        $limite = now()->subDays(30);

        AvisGoogle::where('updated_at', '<', $limite)->delete();
        NoteGoogle::where('updated_at', '<', $limite)->delete();
    }
}
