<x-mail::message>
# Bonjour {{ $user->name }},

Bonne nouvelle ! Votre compte client sur **{{ config('app.name') }}** a été accepté par notre équipe d'administration.

Vous pouvez désormais vous connecter et passer des commandes.

<x-mail::button :url="route('login')">
Se connecter à mon espace
</x-mail::button>

À très bientôt !

Cordialement,<br>
L'équipe de {{ config('app.name') }}
</x-mail::message>
