@php
    $primary = $reservation->restaurant->themeSetting->color_primary ?? '#8B4513';
@endphp
<div style="margin:0;padding:24px;background:#f5f7fb;font-family:Arial,sans-serif;color:#1f2937;">
    <div style="max-width:620px;margin:0 auto;background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #e5e7eb;">
        <div style="background:{{ $primary }};padding:20px 24px;color:#ffffff;">
            <p style="margin:0;font-size:13px;opacity:.9;">{{ $reservation->restaurant->name }}</p>
            <h1 style="margin:8px 0 0;font-size:22px;line-height:1.2;">Rappel — votre table approche</h1>
        </div>
        <div style="padding:24px;">
            <p style="margin:0 0 14px;">Bonjour {{ $reservation->customer_name }},</p>
            <p style="margin:0 0 16px;color:#4b5563;">Nous avons le plaisir de vous rappeler votre réservation.</p>

            <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;padding:14px 16px;margin:0 0 18px;">
                <p style="margin:0 0 6px;"><strong>Date</strong> : {{ $reservation->reservation_at->format('d/m/Y H:i') }}</p>
                <p style="margin:0 0 6px;"><strong>Service</strong> : {{ $reservation->bookingService->name }}</p>
                <p style="margin:0;"><strong>Couverts</strong> : {{ $reservation->covers }}</p>
            </div>

            <p style="margin:0 0 8px;color:#4b5563;">Besoin de modifier ou d’annuler ?</p>
            <p style="margin:0 0 14px;">
                <a href="{{ route('site.reservation.manage', ['token' => $reservation->cancel_token]) }}" style="color:{{ $primary }};font-weight:700;text-decoration:underline;">
                    Gérer ma réservation (annuler / reprogrammer)
                </a>
            </p>

            <p style="margin:0;">À très bientôt,<br><strong>{{ $reservation->restaurant->name }}</strong></p>
        </div>
    </div>
</div>
