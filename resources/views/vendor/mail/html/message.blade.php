<x-mail::layout>
{{-- En-tête : le logo, en PNG (Outlook n'affiche pas le WebP), sur une carte claire qui le garde lisible quand Gmail assombrit le fond --}}
<x-slot:header>
<x-mail::header :url="config('app.url')">
<img src="{{ rtrim(config('app.url'), '/') }}/images/marque/logo-mp-email.png" width="184" height="118" alt="{{ config('site.nom') }}" style="display: block; width: 184px; height: auto; border: 0;">
</x-mail::header>
</x-slot:header>

{{-- Corps --}}
{!! $slot !!}

{{-- Sous-texte --}}
@isset($subcopy)
<x-slot:subcopy>
<x-mail::subcopy>
{!! $subcopy !!}
</x-mail::subcopy>
</x-slot:subcopy>
@endisset

{{-- Pas de pied de page : une simple marge sous le contenu --}}
<x-slot:footer>
<tr>
<td height="32" style="height: 32px; font-size: 0; line-height: 0;">&nbsp;</td>
</tr>
</x-slot:footer>
</x-mail::layout>
