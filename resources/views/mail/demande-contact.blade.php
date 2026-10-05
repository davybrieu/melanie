<x-mail::message>
# Nouvelle demande de séance

**{{ $demande['prenom'] }} {{ $demande['nom'] }}** souhaite une séance **{{ $seances }}**.

<x-mail::button :url="$lienTelephone">
Appeler {{ $demande['prenom'] }} au {{ $telephone }}
</x-mail::button>

<x-mail::table>
| | |
|:--|:--|
| Téléphone | [{{ $telephone }}]({{ $lienTelephone }}) |
| Séance(s) | {{ $seances }} |
| Nombre de personnes | {{ $demande['nombre_personnes'] ?? '—' }} |
| Période souhaitée | {{ $demande['periode'] ?? '—' }} |
</x-mail::table>

**Message :**

{!! nl2br(e($demande['message'])) !!}

Le site annonce un rappel dans l'heure : appelez {{ $demande['prenom'] }} dès que possible.
</x-mail::message>
