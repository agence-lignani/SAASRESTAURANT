<x-mail::message>
# Invitation à rejoindre {{ $restaurant->name }}

Bonjour,

Vous avez été invité·e à accéder au back-office en tant que **@switch($invitation->role)
@case('owner') gérant @break
@case('reservation') gestionnaire réservations @break
@case('editor') rédacteur contenu @break
@case('server') serveur (consultation réservations) @break
@default {{ $invitation->role }}
@endswitch**.

<x-mail::button :url="$acceptUrl">
Accepter l’invitation
</x-mail::button>

Ce lien expire le {{ $invitation->expires_at->translatedFormat('d/m/Y à H:i') }}.

Après acceptation, la connexion au back-office se fait avec votre **nom de famille** et le **code à 6 chiffres** que vous aurez choisis.

Si vous n’attendiez pas cet e-mail, vous pouvez l’ignorer.

{{ config('app.name') }}
</x-mail::message>
