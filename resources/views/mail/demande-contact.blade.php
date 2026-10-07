<x-mail::message>
# Nouvelle demande de séance

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
</x-mail::message>
