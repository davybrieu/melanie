<?php

namespace App\Console\Commands;

use App\Models\AvisGoogle;
use App\Models\NoteGoogle;
use App\Support\FicheGoogle;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Actualise la note globale, le nombre d'avis et les avis affichés, depuis la fiche Google.
 *
 * Lancée chaque matin (routes/console.php). Au premier import, ou avec --nouvelle-selection,
 * elle choisit les avis affichés : 10 avis avec un texte, les mieux notés puis les plus récents.
 * Ensuite, seuls ces avis sont actualisés : modifiés ou supprimés sur Google, ils le sont aussi
 * sur le site. Les règles de Google limitent ce stockage à 30 jours : sans actualisation
 * réussie depuis, les données sont effacées.
 */
class ActualiserAvisGoogle extends Command
{
    private const NOMBRE_AFFICHES = 10;

    protected $signature = 'avis:actualiser {--nouvelle-selection : Choisit de nouveau les avis affichés}';

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
            Log::error('Avis Google : actualisation impossible.', ['erreur' => $exception->getMessage()]);
            $this->error("Actualisation impossible : {$exception->getMessage()}");
            $this->effacerPerimes();

            return self::FAILURE;
        }

        NoteGoogle::upsert([['id' => 1, 'note' => $fiche['note'], 'nombre_avis' => $fiche['nombre']]], ['id'], ['note', 'nombre_avis']);

        $avecTexte = collect($fiche['avis'])->filter(fn (array $avis) => $avis['texte'] !== '');
        $choisis = $this->option('nouvelle-selection') || AvisGoogle::doesntExist()
            ? $avecTexte->sortBy([['note', 'desc'], ['publie_le', 'desc']])->take(self::NOMBRE_AFFICHES)
            : $avecTexte->whereIn('identifiant', AvisGoogle::pluck('identifiant'));

        AvisGoogle::whereNotIn('identifiant', $choisis->pluck('identifiant'))->delete();

        if ($choisis->isNotEmpty()) {
            AvisGoogle::upsert($choisis->values()->all(), ['identifiant'], ['auteur', 'note', 'texte', 'publie_le']);
        }

        $this->line(sprintf(
            '  Note %s/5 (%d avis sur Google) · %d avis affichés',
            $fiche['note'] === null ? '—' : number_format($fiche['note'], 1, ',', ''),
            $fiche['nombre'],
            $choisis->count(),
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
