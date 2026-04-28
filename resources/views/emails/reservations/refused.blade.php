<x-mail::message>
# Réservation non acceptée

Bonjour {{ $reservation->customer_name }},

Nous ne pouvons pas confirmer votre réservation pour ce créneau.

<x-mail::panel>
**Date demandée** : {{ $reservation->reservation_at->format('d/m/Y H:i') }}  
**Service** : {{ $reservation->bookingService->name }}
</x-mail::panel>

N'hésitez pas à nous contacter pour trouver une alternative.

<x-mail::button :url="url('/reservation')">
Choisir un autre créneau
</x-mail::button>

{{ $reservation->restaurant->name }}
</x-mail::message>
