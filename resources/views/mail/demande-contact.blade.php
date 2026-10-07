<x-mail::message>
# Nouvelle demande de séance

{{-- Tableau en HTML plutôt qu'en Markdown : le trait sous le titre se place à égale distance du titre et de la première ligne. --}}
<div class="table">
<table width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td style="border-top: 1px solid #e4e4e7; padding-top: 34px;">Prénom</td>
<td style="border-top: 1px solid #e4e4e7; padding-top: 34px;">{{ $demande['prenom'] }}</td>
</tr>
<tr>
<td>Nom</td>
<td>{{ $demande['nom'] }}</td>
</tr>
<tr>
<td>Téléphone</td>
<td><a href="{{ $lienTelephone }}">{{ $telephone }}</a></td>
</tr>
<tr>
<td>Séance(s)</td>
<td>{{ $seances }}</td>
</tr>
<tr>
<td>Nombre de personnes</td>
<td>{{ $demande['nombre_personnes'] ?? '—' }}</td>
</tr>
<tr>
<td>Période souhaitée</td>
<td>{{ $demande['periode'] ?? '—' }}</td>
</tr>
</table>
</div>

**Message :**

{!! nl2br(e($demande['message'])) !!}
</x-mail::message>
