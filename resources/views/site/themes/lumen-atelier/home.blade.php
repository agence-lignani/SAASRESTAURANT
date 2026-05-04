@php
    $hero = $content['hero'] ?? [];
    $carteNarrative = $content['carte_narrative'] ?? [];
    $menus = $content['menus'] ?? [];
    $items = $menus['items'] ?? [];
    $gallery = $content['gallery_highlights'] ?? [];
    $galleryItems = $gallery['items'] ?? [];
    $about = $content['manifesto'] ?? [];
    $chef = $content['espaces'] ?? [];
    $reviews = $content['reviews_widget'] ?? [];
    $faq = $content['faq'] ?? [];
    $spotlight = $content['spotlight'] ?? [];
    $sectionOrder = $content['section_order'] ?? \App\Support\SiteContent\HomeSectionCatalog::defaultOrder();
@endphp

<div class="theme-lumen">
    @include('site.partials.lumen-header', ['restaurant' => $restaurant])

    @foreach($sectionOrder as $sectionKey)
        @switch($sectionKey)
            @case('hero')
                <section class="theme-lumen-section theme-lumen-hero theme-palazzo-hero">
                    <div class="bistro-container">
                        <div class="theme-palazzo-hero-grid">
                            <div class="theme-palazzo-hero-copy">
                                <p class="theme-lumen-kicker">{{ $hero['eyebrow'] ?? 'Depuis 1985' }}</p>
                                <h1 class="theme-palazzo-display">
                                    {{ $hero['title'] ?? $restaurant->name }}
                                </h1>
                                <p class="theme-palazzo-lead">
                                    {{ is_string($hero['subtitle'] ?? null) ? $hero['subtitle'] : '' }}
                                </p>

                                @if(! empty($hero['cta_buttons']) && is_array($hero['cta_buttons']))
                                    <div class="theme-palazzo-actions">
                                        @foreach($hero['cta_buttons'] as $index => $button)
                                            @if(filled($button['href'] ?? null))
                                                <a href="{{ $button['href'] }}" class="{{ $index === 0 ? 'theme-palazzo-btn-primary' : 'theme-palazzo-btn-secondary' }}">
                                                    {{ $button['label'] ?? 'Découvrir' }}
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif

                                <div class="theme-palazzo-proof" aria-label="Informations clés">
                                    <div>
                                        <span>Maison</span>
                                        <strong>1985</strong>
                                    </div>
                                    <div>
                                        <span>Cuisine</span>
                                        <strong>Saison</strong>
                                    </div>
                                    <div>
                                        <span>Service</span>
                                        <strong>Midi & soir</strong>
                                    </div>
                                </div>
                            </div>

                            <div class="theme-palazzo-hero-visual">
                                <figure class="theme-palazzo-image-card">
                                    @if(filled($hero['image_url'] ?? null))
                                        <img src="{{ $hero['image_url'] }}" alt="{{ $hero['image_alt'] ?? '' }}" loading="eager">
                                    @endif
                                </figure>
                                <aside class="theme-palazzo-floating-card">
                                    <span>À table</span>
                                    <strong>Réserver</strong>
                                    <p>Une expérience chaleureuse, précise et contemporaine.</p>
                                </aside>
                            </div>
                        </div>
                    </div>
                </section>
                @break

            @case('manifesto')
                <section class="theme-lumen-section theme-lumen-block">
                    <div class="bistro-container py-14 md:py-20">
                        <div class="theme-palazzo-split">
                            <figure class="theme-palazzo-section-image">
                                @if(filled($about['image_url'] ?? null))
                                    <img src="{{ $about['image_url'] }}" alt="{{ $about['image_alt'] ?? '' }}" loading="lazy">
                                @endif
                            </figure>
                            <article class="theme-palazzo-text-panel">
                                <p class="theme-lumen-kicker">{{ $about['eyebrow'] ?? 'Notre histoire' }}</p>
                                <h2 class="theme-lumen-title mt-3">{{ $about['heading'] ?? 'Manifesto' }}</h2>
                                <div class="theme-lumen-readable mt-6 space-y-5">
                                    @foreach(($about['paragraphs'] ?? []) as $paragraph)
                                        <div>{!! \App\Support\SiteContent\SiteContentHtml::paragraph($paragraph) !!}</div>
                                    @endforeach
                                </div>
                                @if(filled($about['signature'] ?? null))
                                    <p class="theme-palazzo-signature">{{ $about['signature'] }}</p>
                                @endif
                            </article>
                        </div>
                    </div>
                </section>
                @break

            @case('carte_narrative')
                <section class="theme-lumen-section theme-palazzo-dark">
                    <div class="bistro-container py-14 md:py-20">
                        <div class="theme-palazzo-menu-feature">
                            <div>
                                <p class="theme-lumen-kicker text-white/75">{{ $carteNarrative['eyebrow'] ?? 'Carte signature' }}</p>
                                <h2 class="theme-lumen-title mt-3 text-white">{{ $carteNarrative['heading'] ?? 'La carte' }}</h2>
                                <div class="mt-6 space-y-4 text-base leading-8 text-white/82 md:text-lg">
                                    @foreach(($carteNarrative['paragraphs'] ?? []) as $paragraph)
                                        <div>{!! \App\Support\SiteContent\SiteContentHtml::paragraph($paragraph) !!}</div>
                                    @endforeach
                                </div>
                                @if(! empty($carteNarrative['cta_buttons']) && is_array($carteNarrative['cta_buttons']))
                                    <div class="theme-palazzo-actions mt-8">
                                        @foreach($carteNarrative['cta_buttons'] as $index => $button)
                                            @if(filled($button['href'] ?? null))
                                                <a href="{{ $button['href'] }}" class="{{ $index === 0 ? 'theme-palazzo-btn-primary' : 'theme-palazzo-btn-secondary theme-palazzo-btn-light' }}">
                                                    {{ $button['label'] ?? 'Découvrir' }}
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <figure class="theme-palazzo-menu-image">
                                @if(filled($carteNarrative['image_url'] ?? null))
                                    <img src="{{ $carteNarrative['image_url'] }}" alt="{{ $carteNarrative['image_alt'] ?? '' }}" loading="lazy">
                                @endif
                            </figure>
                        </div>
                    </div>
                </section>
                @break

            @case('menus')
                <section class="theme-lumen-section theme-lumen-block theme-lumen-cream">
                    <div class="bistro-container py-14 md:py-20">
                        <div class="theme-lumen-section-heading">
                            <p class="theme-lumen-kicker">À la carte</p>
                            <h2 class="theme-lumen-title mt-3">{{ $menus['heading'] ?? 'Selection' }}</h2>
                            @if(filled($menus['intro'] ?? null))
                                <p class="theme-lumen-readable mt-4">{{ $menus['intro'] }}</p>
                            @endif
                        </div>
                        <div class="theme-palazzo-dish-grid">
                            @foreach($items as $item)
                                @if(filled($item['title'] ?? null))
                                    <article class="theme-palazzo-dish-card">
                                        <figure>
                                            @if(filled($item['image_url'] ?? null))
                                                <img src="{{ $item['image_url'] }}" alt="{{ $item['image_alt'] ?? $item['title'] }}" loading="lazy">
                                            @endif
                                        </figure>
                                        <div class="p-5 md:p-6">
                                            <div class="flex items-start justify-between gap-4">
                                                <h3 class="theme-lumen-heading-md">{{ $item['title'] }}</h3>
                                                @if(filled($item['price'] ?? null))
                                                    <span class="theme-lumen-menu-price">{{ $item['price'] }}</span>
                                                @endif
                                            </div>
                                            <p class="theme-lumen-readable mt-3">{{ $item['description'] ?? '' }}</p>
                                        </div>
                                    </article>
                                @endif
                            @endforeach
                        </div>
                        <div class="mt-8">
                            <a href="{{ route('site.carte') }}" class="theme-palazzo-btn-primary">Voir la carte complète</a>
                        </div>
                    </div>
                </section>
                @break

            @case('espaces')
                <section class="theme-lumen-section theme-lumen-block">
                    <div class="bistro-container py-14 md:py-20">
                        <div class="theme-palazzo-split theme-palazzo-split-reverse">
                            <figure class="theme-palazzo-section-image">
                                @if(filled($chef['image_url'] ?? null))
                                    <img src="{{ $chef['image_url'] }}" alt="{{ $chef['image_alt'] ?? '' }}" loading="lazy">
                                @endif
                            </figure>
                            <article class="theme-palazzo-text-panel">
                                <p class="theme-lumen-kicker">{{ $chef['eyebrow'] ?? 'Le chef' }}</p>
                                <h2 class="theme-lumen-title mt-3">{{ $chef['heading'] ?? 'Le chef' }}</h2>
                                <div class="theme-lumen-readable mt-6">
                                    {!! \App\Support\SiteContent\SiteContentHtml::safe($chef['body'] ?? '') !!}
                                </div>
                                @if(filled($chef['recognition_value'] ?? null))
                                    <div class="theme-palazzo-badge">
                                        <span>{{ $chef['recognition_label'] ?? 'Reconnaissance' }}</span>
                                        <strong>{{ $chef['recognition_value'] }}</strong>
                                    </div>
                                @endif
                            </article>
                        </div>
                    </div>
                </section>
                @break

            @case('gallery_highlights')
                <section class="theme-lumen-section theme-lumen-block theme-lumen-gallery-section">
                    <div class="bistro-container py-14 md:py-20">
                        <div class="theme-lumen-section-heading">
                            <p class="theme-lumen-kicker">Ambiance</p>
                            <h2 class="theme-lumen-title mt-3">{{ $gallery['heading'] ?? 'Visual Notes' }}</h2>
                            @if(filled($gallery['intro'] ?? null))
                                <p class="theme-lumen-readable mt-4">{{ $gallery['intro'] }}</p>
                            @endif
                        </div>
                        <div class="theme-lumen-gallery-grid">
                            @foreach($galleryItems as $index => $photo)
                                @if(filled($photo['image_url'] ?? null))
                                    <figure class="theme-lumen-tile theme-lumen-gallery-tile {{ $index === 0 ? 'md:col-span-7 md:row-span-2' : ($index === 1 ? 'md:col-span-5' : 'md:col-span-4') }}">
                                        <img src="{{ $photo['image_url'] }}" alt="{{ $photo['image_alt'] ?? '' }}" loading="lazy">
                                    </figure>
                                @endif
                            @endforeach
                        </div>
                        @if(filled($gallery['gallery_link_href'] ?? null))
                            <div class="mt-8">
                                <a href="{{ $gallery['gallery_link_href'] }}" class="theme-palazzo-btn-secondary">
                                    {{ $gallery['gallery_link_label'] ?? 'Voir la galerie' }}
                                </a>
                            </div>
                        @endif
                    </div>
                </section>
                @break

            @case('reviews_widget')
                <section class="theme-lumen-section theme-lumen-block">
                    <div class="bistro-container py-14 md:py-20">
                        <div class="theme-lumen-section-heading">
                            <p class="theme-lumen-kicker">{{ $reviews['section_eyebrow'] ?? 'Témoignages' }}</p>
                            <h2 class="theme-lumen-title mt-3">{{ $reviews['heading'] ?? 'Avis clients' }}</h2>
                        </div>
                        <div class="theme-palazzo-review-grid">
                            @foreach(($reviews['cards'] ?? []) as $card)
                                <article class="theme-palazzo-review-card">
                                    <p>“{{ $card['quote'] ?? '' }}”</p>
                                    <strong>{{ $card['author'] ?? '' }}</strong>
                                </article>
                            @endforeach
                        </div>
                    </div>
                </section>
                @break

            @case('spotlight')
                <section class="theme-lumen-section theme-lumen-promo">
                    <div class="bistro-container py-14 md:py-20">
                        <div class="grid gap-8 md:grid-cols-12 md:items-end">
                            <h2 class="theme-lumen-title text-white md:col-span-8">{{ $spotlight['heading'] ?? 'Réservez votre table' }}</h2>
                            <div class="md:col-span-4 md:text-right">
                                @foreach(($spotlight['buttons'] ?? []) as $button)
                                    @if(filled($button['href'] ?? null))
                                        <a href="{{ $button['href'] }}" class="theme-lumen-btn-primary">{{ $button['label'] ?? 'Réserver' }}</a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        <p class="mt-4 max-w-2xl text-white/82">{{ strip_tags((string) ($spotlight['body'] ?? '')) }}</p>
                    </div>
                </section>
                @break

            @case('faq')
                <section class="theme-lumen-section theme-lumen-block">
                    <div class="bistro-container py-14 md:py-20">
                        <div class="theme-lumen-section-heading">
                            <p class="theme-lumen-kicker">Questions</p>
                            <h2 class="theme-lumen-title mt-3">{{ $faq['heading'] ?? 'FAQ' }}</h2>
                            @if(filled($faq['intro'] ?? null))
                                <p class="theme-lumen-readable mt-4">{{ $faq['intro'] }}</p>
                            @endif
                        </div>
                        <div class="theme-palazzo-faq-grid">
                            @foreach(($faq['items'] ?? []) as $item)
                                @if(filled($item['question'] ?? null))
                                    <article class="theme-palazzo-faq-card">
                                        <h3>{{ $item['question'] }}</h3>
                                        <div>{!! \App\Support\SiteContent\SiteContentHtml::safe($item['answer'] ?? '') !!}</div>
                                    </article>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </section>
                @break

            @case('practical')
                @include('site.partials.home-section-practical', ['content' => $content, 'restaurant' => $restaurant])
                @break
        @endswitch
    @endforeach
</div>
