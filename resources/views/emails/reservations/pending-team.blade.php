<x-mail::message>
# Nouvelle demande de réservation

Une nouvelle demande vient d'être enregistrée.

- Client: {{ $reservation->customer_name }}
- Email: {{ $reservation->customer_email }}
- Téléphone: {{ $reservation->customer_phone ?: '—' }}
- Date: {{ $reservation->reservation_at->format('d/m/Y H:i') }}
- Service: {{ $reservation->bookingService->name }}
- Couverts: {{ $reservation->covers }}

@if(filled($reservation->notes))
Notes: {{ $reservation->notes }}
@endif

<x-mail::button :url="url('/admin/reservations')">
Voir dans l'admin
</x-mail::button>
</x-mail::message>
