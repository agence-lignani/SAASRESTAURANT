@php
    $phone = $restaurant->contact_phone ?? null;
    $email = $restaurant->contact_email ?? null;
    $telHref = $phone ? 'tel:'.preg_replace('/\s+/', '', $phone) : null;
    $mailHref = $email ? 'mailto:'.$email : null;
    $addressQuery = trim(implode(', ', array_filter([
        $restaurant->address_line1,
        $restaurant->address_line2,
        trim(($restaurant->postal_code ?? '').' '.($restaurant->city ?? '')),
        $restaurant->country,
    ])));
    $mapsHref = $addressQuery !== '' ? 'https://www.google.com/maps/search/?api=1&query='.urlencode($addressQuery) : null;
@endphp
@if($telHref || $mailHref || $mapsHref)
    <div class="bistro-top-bar" role="complementary" aria-label="Informations de contact rapide">
        <div class="bistro-container">
            <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:flex-end;gap:0.2rem 1.2rem;padding-top:0.42rem;padding-bottom:0.42rem;">
                @if($telHref)
                    <a href="{{ $telHref }}">{{ $phone }}</a>
                @endif
                @if($mailHref)
                    <a href="{{ $mailHref }}">E-mail</a>
                @endif
                @if($mapsHref)
                    <a href="{{ $mapsHref }}" target="_blank" rel="noopener noreferrer">Itinéraire</a>
                @endif
            </div>
        </div>
    </div>
@endif
