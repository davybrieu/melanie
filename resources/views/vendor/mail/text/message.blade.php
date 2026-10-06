<x-mail::layout>
    {{-- En-tête : le nom du site, à la place du logo de la version HTML --}}
    <x-slot:header>
        <x-mail::header :url="config('app.url')">
            {{ config('site.nom') }}
        </x-mail::header>
    </x-slot:header>

    {{-- Corps --}}
    {{ $slot }}

    {{-- Sous-texte --}}
    @isset($subcopy)
        <x-slot:subcopy>
            <x-mail::subcopy>
                {{ $subcopy }}
            </x-mail::subcopy>
        </x-slot:subcopy>
    @endisset

    {{-- Pas de pied de page --}}
</x-mail::layout>
