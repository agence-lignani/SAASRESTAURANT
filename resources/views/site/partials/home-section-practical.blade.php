<section id="contact" class="bistro-section-plain scroll-mt-24 py-20 md:py-28" aria-labelledby="practical-heading">
    <div class="bistro-container">
        @php
            $variant = $content['practical']['variant'] ?? 'contact_map_dominant';
            $hasContact = ! empty($content['practical']['contact_lines']);
            $hasHours = ! empty($content['practical']['opening_lines']);
            $address = trim(implode(', ', array_filter([
                $restaurant->address_line1,
                $restaurant->address_line2,
                trim(($restaurant->postal_code ?? '').' '.($restaurant->city ?? '')),
                $restaurant->country,
            ])));
            $hasAddress = $address !== '';
            $mapEmbedUrl = $hasAddress ? 'https://www.google.com/maps?q='.urlencode($address).'&output=embed' : null;
            $mapsDirectionsUrl = $hasAddress ? 'https://www.google.com/maps/dir/?api=1&destination='.urlencode($address) : null;
        @endphp
        @if(! $hasContact && ! $hasHours)
            <div class="bistro-section-card text-sm leading-relaxed text-stone-600">
                <p>Ajoutez l’adresse, le téléphone et les horaires dans l’administration (Identité & contact) pour qu’ils s’affichent ici.</p>
            </div>
        @else
            @if($variant === 'bakery_footer_contact')
                <div class="bistro-section-card bg-[#11151c] text-white">
                    <div class="grid gap-8 md:grid-cols-3">
                        <div>
                            <h3 class="font-[family-name:var(--bistro-font-heading)] text-2xl">About Us</h3>
                            <p class="mt-3 text-sm leading-relaxed text-white/75">
                                {{ $restaurant->tagline ?? 'Bakery craft and fresh treats every day.' }}
                            </p>
                        </div>
                        <div>
                            <h3 class="font-[family-name:var(--bistro-font-heading)] text-2xl">Explore</h3>
                            <ul class="mt-3 space-y-2 text-sm text-white/80">
                                <li><a href="{{ route('site.home') }}">Home</a></li>
                                <li><a href="{{ route('site.carte') }}">Menu</a></li>
                                <li><a href="{{ route('site.contact') }}">Contact</a></li>
                            </ul>
                        </div>
                        <div>
                            <h3 class="font-[family-name:var(--bistro-font-heading)] text-2xl">Contact</h3>
                            <ul class="mt-3 space-y-2 text-sm text-white/80">
                                @foreach($content['practical']['contact_lines'] ?? [] as $line)
                                    <li>{{ $line }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @else
            <div class="bistro-section-card">
                <header class="bistro-section-header">
                    <span class="v2-chip mb-4">Venir facilement</span>
                    <p class="epicure-kicker">Contact & accès</p>
                    <h2 id="practical-heading" class="v2-display mt-3">
                        {{ $content['practical']['heading'] ?? 'Infos pratiques' }}
                    </h2>
                    <div class="bistro-gold-line mt-6"></div>
                </header>

                @php
                    $isInfoDominant = $variant === 'contact_info_dominant';
                    $isCardsUtility = $variant === 'contact_cards_utility';
                    $leftColClass = $isInfoDominant ? 'space-y-6 lg:col-span-7' : 'space-y-6 lg:col-span-5';
                    $rightColClass = $isInfoDominant ? 'lg:col-span-5' : 'lg:col-span-7';
                @endphp
                <div class="grid gap-8 lg:grid-cols-12">
                    <div class="{{ $leftColClass }}">
                        @if($hasContact)
                            <div class="bistro-accent-card p-5 md:p-6">
                                <h3 class="text-sm font-semibold uppercase tracking-[0.16em] text-[var(--bistro-color-primary)]">{{ $content['practical']['contact_title'] ?? 'Contact' }}</h3>
                                <ul class="mt-4 {{ $isCardsUtility ? 'grid gap-3 md:grid-cols-2' : 'space-y-3' }} text-sm leading-relaxed text-stone-600">
                                    @foreach($content['practical']['contact_lines'] as $line)
                                        <li class="rounded-lg bg-white px-3 py-2">{{ $line }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if($hasHours)
                            <div class="bistro-accent-card p-5 md:p-6">
                                <h3 class="text-sm font-semibold uppercase tracking-[0.16em] text-[var(--bistro-color-primary)]">{{ $content['practical']['opening_title'] ?? 'Horaires' }}</h3>
                                <ul class="mt-4 {{ $isCardsUtility ? 'grid gap-3 md:grid-cols-2' : 'space-y-3' }} text-sm leading-relaxed text-stone-600">
                                    @foreach($content['practical']['opening_lines'] as $line)
                                        <li class="rounded-lg bg-white px-3 py-2">{{ $line }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>

                    <div class="{{ $rightColClass }}">
                        <div class="overflow-hidden rounded-xl border border-stone-200 bg-white">
                            @if($mapEmbedUrl)
                                <iframe
                                    src="{{ $mapEmbedUrl }}"
                                    class="h-[340px] w-full md:h-[420px]"
                                    loading="lazy"
                                    referrerpolicy="no-referrer-when-downgrade"
                                    title="Carte Google Maps du restaurant"
                                ></iframe>
                            @else
                                <div class="flex h-[340px] items-center justify-center bg-stone-100 text-sm text-stone-500 md:h-[420px]">
                                    Ajoutez une adresse dans l’administration pour afficher la carte Google Maps.
                                </div>
                            @endif

                            @if($mapsDirectionsUrl)
                                <div class="border-t border-stone-200 bg-stone-50 px-5 py-4 md:px-6">
                                    <a
                                        href="{{ $mapsDirectionsUrl }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="bistro-btn-primary"
                                    >
                                        Voir le trajet sur Google Maps
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif
        @endif
    </div>
</section>
