<x-mail::message>
# Réservation annulée

Bonjour {{ $reservation->customer_name }},

Votre réservation a été annulée.

<x-mail::panel>
**Date** : {{ $reservation->reservation_at->format('d/m/Y H:i') }}  
**Service** : {{ $reservation->bookingService->name }}
</x-mail::panel>

Si besoin, vous pouvez effectuer une nouvelle réservation sur le site.

<x-mail::button :url="url('/reservation')">
Nouvelle réservation
</x-mail::button>

{{ $reservation->restaurant->name }}
</x-mail::message>
