@php
    $reviews = $content['reviews_widget'] ?? [];
    $variant = $reviews['variant'] ?? 'reviews_card_deck';
    $hasTripUrl = filled($reviews['url'] ?? null);
    $cards = $reviews['cards'] ?? [];
    $reviewsCtas = $reviews['cta_buttons'] ?? [];
@endphp
<section id="avis" class="bistro-section-plain scroll-mt-24 py-20 md:py-28">
    <div class="bistro-container">
        <header class="mx-auto mb-16 max-w-4xl text-center">
            <span class="v2-chip mb-4">Voix de nos clients</span>
            <p class="epicure-kicker mb-4">{{ $reviews['section_eyebrow'] ?? 'Témoignages' }}</p>
            <h2 class="v2-display">
                {{ $reviews['heading'] ?? 'Nos clients' }}
            </h2>
            <div class="bistro-gold-line mx-auto mt-6"></div>
            <div class="prose prose-stone mx-auto mt-6 max-w-2xl text-base leading-relaxed text-stone-600 prose-p:my-0">
                {!! \App\Support\SiteContent\SiteContentHtml::safe($reviews['description'] ?? '') !!}
            </div>
        </header>

        @if (! empty($cards))
            @if ($variant === 'reviews_quote_wall')
                <div class="grid gap-6 md:grid-cols-2">
                    @foreach ($cards as $index => $card)
                        <article class="bistro-accent-card v2-section-shell p-6 md:p-8 {{ $index === 0 ? 'md:col-span-2' : '' }}">
                            <p class="bistro-reviews-quote">"{{ $card['quote'] ?? '' }}"</p>
                            <p class="mt-5 text-xs font-semibold uppercase tracking-[0.2em] text-[var(--v2-accent)]">{{ $card['author'] ?? '' }}</p>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="grid gap-10 {{ $variant === 'reviews_split_with_widget' ? 'md:grid-cols-2' : 'md:grid-cols-3 md:gap-12' }}">
                    @foreach ($cards as $card)
                        <article class="bistro-accent-card v2-section-shell relative border-l-2 p-6 md:p-8">
                            <span class="pointer-events-none absolute left-0 top-0 font-[family-name:var(--bistro-font-heading)] text-6xl leading-none text-[var(--bistro-color-primary)]/20" aria-hidden="true">“</span>
                            <p class="mb-5 text-sm tracking-[0.16em] text-[var(--bistro-color-primary)]">★★★★★</p>
                            <p class="bistro-reviews-quote">
                                "{{ $card['quote'] ?? '' }}"
                            </p>
                            <p class="mt-6 text-xs font-semibold uppercase tracking-[0.2em] text-[var(--bistro-color-primary)]">
                                {{ $card['author'] ?? '' }}
                            </p>
                        </article>
                    @endforeach
                </div>
            @endif
        @endif

        @if(! $hasTripUrl)
            <p class="mt-8 rounded-2xl border border-dashed border-stone-300 bg-stone-50/80 p-4 text-sm leading-relaxed text-stone-600">
                Pour afficher le widget TripAdvisor ici, renseignez l’URL de votre fiche, le <span class="font-medium">location_id</span> et les types de widget dans l’administration (Contenus du site → Accueil → Avis clients).
            </p>
        @else
            <div class="tripadvisor-shell mt-12">
                <div class="tripadvisor-shell-header">
                    <p class="tripadvisor-shell-title">
                        Avis vérifiés sur {{ $reviews['platform'] ?? 'TripAdvisor' }}
                    </p>
                    <p class="tripadvisor-shell-subtitle">
                        Dernières notes et commentaires publiés sur votre fiche.
                    </p>
                </div>

                <div class="mb-5 flex flex-wrap gap-2">
                    @foreach($reviewsCtas as $cta)
                        @if(filled($cta['href'] ?? null))
                            <a
                                href="{{ $cta['href'] }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="tripadvisor-shell-cta"
                            >
                                {{ $cta['label'] ?? '' }}
                            </a>
                        @endif
                    @endforeach
                </div>

                <div class="tripadvisor-shell-widgets">
                    <div class="min-w-[260px]">
                        @php
                            $tripWidgetType = $reviews['widget_type'] ?? 'cdsratingsonlynarrow';
                            $tripLocationId = $reviews['location_id'] ?? null;
                            $tripUniq = 'trip-widget-'.substr(md5($restaurant->id.'-'.$tripWidgetType), 0, 6);
                        @endphp

                        @php
                            $reviewsFirstCtaLabel = $reviewsCtas[0]['label'] ?? 'Voir les avis';
                        @endphp

                        @if(! empty($tripLocationId))
                            <div id="TA_{{ $tripWidgetType }}{{ $tripUniq }}" class="tripadvisor-widget-box">
                                <ul id="{{ $tripUniq }}" class="TA_links">
                                    <li>
                                        <a href="{{ $reviews['url'] }}" target="_blank" rel="noopener noreferrer">
                                            {{ $reviewsFirstCtaLabel }}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <script async src="https://www.jscache.com/wejs?wtype={{ urlencode($tripWidgetType) }}&uniq={{ urlencode($tripUniq) }}&locationId={{ urlencode($tripLocationId) }}&lang=fr&border=true&display_version=2"></script>

                            @php
                                $tripCommentsWidgetType = $reviews['widget_type_comments'] ?? 'cdsrr_reviewlist';
                                $tripCommentsUniq = 'trip-comments-'.substr(md5($restaurant->id.'-'.$tripCommentsWidgetType), 0, 6);
                            @endphp

                            <div id="TA_{{ $tripCommentsWidgetType }}{{ $tripCommentsUniq }}" class="tripadvisor-widget-box mt-5">
                                <ul id="{{ $tripCommentsUniq }}" class="TA_links">
                                    <li>
                                        <a href="{{ $reviews['url'] }}" target="_blank" rel="noopener noreferrer">
                                            {{ $reviews['comments_link_label'] ?? 'Lire les derniers commentaires' }}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <script async src="https://www.jscache.com/wejs?wtype={{ urlencode($tripCommentsWidgetType) }}&uniq={{ urlencode($tripCommentsUniq) }}&locationId={{ urlencode($tripLocationId) }}&lang=fr&border=true&display_version=2"></script>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>
