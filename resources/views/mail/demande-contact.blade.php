<x-mail::message>
# Nouvelle demande de séance

**{{ $demande['prenom'] }} {{ $demande['nom'] }}** souhaite une séance **{{ $seances }}**.

<x-mail::table>
| | |
|:--|:--|
| E-mail | {{ $demande['email'] }} |
| Téléphone | {{ $demande['telephone'] ?? '—' }} |
| Séance(s) | {{ $seances }} |
| Date prévue d'accouchement | {{ $dateAccouchement ?? '—' }} |
| Nombre de personnes | {{ $demande['nombre_personnes'] ?? '—' }} |
| Période souhaitée | {{ $demande['periode'] ?? '—' }} |
</x-mail::table>

@if (filled($demande['message'] ?? null))
**Message :**

{!! nl2br(e($demande['message'])) !!}
@endif

Répondez simplement à cet e-mail pour écrire à {{ $demande['prenom'] }}.
</x-mail::message>
