<?php

namespace App\Console\Commands;

use App\Models\AvisGoogle;
use App\Models\NoteGoogle;
use App\Support\FicheGoogle;
use Carbon\CarbonInterface;
use Illuminate\Console\Command;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * Actualise la note globale, le nombre d'avis et les avis, depuis la fiche Google.
 *
 * Lancée chaque matin (routes/console.php). Elle garde tous les avis, même sans texte (note
 * seule) : modifiés ou supprimés sur Google, ils le sont aussi sur le site. Un nouvel avis arrive
 * masqué : les avis à afficher se choisissent dans l'espace de gestion, et ce choix est conservé.
 * Les règles de Google limitent ce stockage à 30 jours : sans actualisation réussie depuis, les
 * données sont effacées. L'adresse du site est prévenue au troisième matin d'échec de suite.
 */
class ActualiserAvisGoogle extends Command
{
    // Échec déjà signalé par e-mail : pas de nouvel e-mail avant une semaine.
    private const CLE_ALERTE = 'avis-google.alerte-envoyee';

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
            Log::error('Avis Google : actualisation impossible.', ['erreur' => $exception->getMessage()]);
            $this->error("Actualisation impossible : {$exception->getMessage()}");
            $this->effacerPerimes();

            // Une panne de Google se règle en général d'elle-même : l'alerte attend le troisième matin
            // d'échec de suite (aucune actualisation réussie ni ce jour-là, ni la veille, ni
            // l'avant-veille). Sans actualisation réussie enregistrée, elle part aussitôt.
            $derniereReussite = NoteGoogle::find(1)?->updated_at;

            if ($derniereReussite === null || $derniereReussite->lt(now('Europe/Paris')->startOfDay()->subDays(2))) {
                $this->prevenir($exception->getMessage(), $derniereReussite);
            }

            return self::FAILURE;
        }

        Cache::forget(self::CLE_ALERTE);

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

    /**
     * Prévient l'adresse du site (MAIL_FROM_ADDRESS), au plus une fois par semaine tant que
     * l'erreur dure : sans actualisation réussie pendant 30 jours, les avis disparaissent du site.
     */
    private function prevenir(string $erreur, ?CarbonInterface $derniereReussite): void
    {
        if (! Cache::add(self::CLE_ALERTE, true, now()->addWeek())) {
            return;
        }

        $texte = implode("\n\n", array_filter([
            ($derniereReussite
                ? 'Les avis Google du site n’ont pas pu être mis à jour depuis le '.$derniereReussite->timezone('Europe/Paris')->locale('fr')->translatedFormat('j F Y').'. Dernière erreur :'
                : 'La mise à jour quotidienne des avis Google du site a échoué :')."\n".trim($erreur),
            str_contains($erreur, 'invalid_grant')
                ? 'Google refuse le jeton d’actualisation : générez-en un nouveau et remplacez GOOGLE_BUSINESS_REFRESH_TOKEN dans Forge (voir « Avis Google » dans le README).'
                : null,
            'Sans mise à jour réussie pendant 30 jours, la note et les avis disparaissent du site. Ce message est renvoyé au plus une fois par semaine tant que l’erreur dure.',
        ]));

        try {
            Mail::raw($texte, fn (Message $message) => $message
                ->to(config('mail.from.address'), config('site.nom'))
                ->subject('Avis Google : mise à jour impossible'));
        } catch (Throwable $exception) {
            Log::error('Avis Google : e-mail d’alerte impossible.', ['erreur' => $exception->getMessage()]);
        }
    }
}
