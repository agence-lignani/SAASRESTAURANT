{{-- Bande contact légère type export Stitch (surface discrète) --}}
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
    <div class="border-b border-stone-200/80 bg-white/90 text-stone-600">
        <div class="bistro-container flex flex-wrap items-center justify-center gap-x-6 gap-y-1.5 py-2 text-xs sm:justify-end">
            @if($telHref)
                <a href="{{ $telHref }}" class="font-medium hover:text-[var(--bistro-color-primary)]">{{ $phone }}</a>
            @endif
            @if($mailHref)
                <span class="hidden text-stone-300 sm:inline" aria-hidden="true">·</span>
                <a href="{{ $mailHref }}" class="font-medium hover:text-[var(--bistro-color-primary)]">Email</a>
            @endif
            @if($mapsHref)
                <span class="hidden text-stone-300 sm:inline" aria-hidden="true">·</span>
                <a href="{{ $mapsHref }}" target="_blank" rel="noopener noreferrer" class="font-medium hover:text-[var(--bistro-color-primary)]">Itinéraire</a>
            @endif
        </div>
    </div>
@endif
