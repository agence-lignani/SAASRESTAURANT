<x-mail::message>
# Réinitialisation du mot de passe

Vous recevez cet e-mail car une demande de réinitialisation a été faite pour votre compte d’administration.

<x-mail::button :url="$url">
Choisir un nouveau mot de passe
</x-mail::button>

Ce lien expire dans {{ $expiresMinutes }} minutes.

Si vous n’êtes pas à l’origine de cette demande, ignorez ce message.

Cordialement,<br>
{{ config('app.name') }}
</x-mail::message>
