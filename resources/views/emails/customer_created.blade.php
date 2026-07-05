<x-mail::message>
# Bonjour {{ $user->name }},

Votre compte a été créé avec succès par l'administrateur de **{{ config('app.name') }}**.

Voici vos identifiants pour vous connecter à votre espace client :

* **Adresse e-mail :** {{ $user->email }}
* **Mot de passe :** `{{ $password }}`

Vous pouvez vous connecter dès maintenant en cliquant sur le bouton ci-dessous :

<x-mail::button :url="route('login')">
Se connecter
</x-mail::button>

Pour votre sécurité, nous vous recommandons de modifier votre mot de passe depuis votre profil après votre première connexion.

Cordialement,<br>
L'équipe de {{ config('app.name') }}
</x-mail::message>
