@php
    $imageUrl = $data['image_url'] ?? null;
    $imageAlt = $data['image_alt'] ?? '';
    $eyebrow = $data['eyebrow'] ?? 'Carte signature';
    $heading = $data['heading'] ?? 'Nos plats signatures';
    if (is_array($eyebrow)) {
        $eyebrow = $eyebrow['content'] ?? $eyebrow['text'] ?? 'Carte signature';
    }
    if (is_array($heading)) {
        $heading = $heading['content'] ?? $heading['text'] ?? 'Nos plats signatures';
    }
    $paragraphs = $data['paragraphs'] ?? [];
    $variant = $data['variant'] ?? 'menu_featured_primary';
    if (! is_array($paragraphs) || $paragraphs === []) {
        $paragraphs = [
            'Entrées généreuses, viandes et poissons au gré des arrivages, options végétariennes : une carte construite autour du produit.',
            'Fromages affinés, desserts maison et accords mets-vins pensés pour une dégustation lisible, élégante et de saison.',
        ];
    }
@endphp

<section id="carte" class="bistro-section-soft scroll-mt-24 py-20 md:py-28" aria-labelledby="carte-narrative-heading">
    <div class="bistro-container">
        <div class="bistro-section-card">
            <header class="bistro-section-header text-center md:text-left">
                <span class="v2-chip mb-4">Signature du moment</span>
                <p class="epicure-kicker mb-4">{{ $eyebrow }}</p>
                <h2 id="carte-narrative-heading" class="v2-display">
                    {{ $heading }}
                </h2>
                <div class="bistro-gold-line mt-6"></div>
            </header>

            @php
                $textColClass = $variant === 'menu_grid_minimal' ? 'lg:col-span-12' : 'lg:col-span-7';
                $imageColClass = $variant === 'menu_grid_minimal' ? 'lg:col-span-6' : 'lg:col-span-5';
            @endphp
            <div class="grid items-start gap-8 lg:grid-cols-12">
                <div class="{{ $textColClass }}">
                    <div class="bistro-accent-card v2-section-shell p-6 md:p-8 {{ $variant === 'menu_grid_minimal' ? '' : '' }}">
                        <div class="bistro-carte-narrative-body space-y-5">
                            @foreach($paragraphs as $paragraph)
                                {!! \App\Support\SiteContent\SiteContentHtml::paragraph($paragraph) !!}
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center">
                        @foreach(($data['cta_buttons'] ?? [
                            ['label' => 'Réserver une table', 'href' => route('site.reservation'), 'variant' => 'primary'],
                            ['label' => 'Parcourir la carte', 'href' => route('site.carte'), 'variant' => 'secondary'],
                        ]) as $button)
                            @php
                                $variant = $button['variant'] ?? 'primary';
                            @endphp
                            @if (filled($button['href'] ?? null))
                                @if ($variant === 'secondary')
                                    <a
                                        href="{{ $button['href'] }}"
                                        class="bistro-btn-secondary w-full sm:w-auto"
                                    >
                                        {{ $button['label'] }}
                                    </a>
                                @else
                                    <a href="{{ $button['href'] }}" class="bistro-btn-primary w-full sm:w-auto">
                                        {{ $button['label'] }}
                                    </a>
                                @endif
                            @endif
                        @endforeach
                    </div>
                </div>

                <div class="relative min-h-[min(22rem,55vh)] {{ $imageColClass }} lg:min-h-0">
                    @if (filled($imageUrl))
                        <img
                            src="{{ $imageUrl }}"
                            alt="{{ $imageAlt }}"
                            class="h-full w-full rounded-2xl object-cover"
                            loading="lazy"
                            decoding="async"
                        >
                        <div
                            class="pointer-events-none absolute inset-0 bg-gradient-to-t from-stone-950/55 via-stone-950/10 to-transparent lg:bg-gradient-to-r lg:from-stone-950/45 lg:via-stone-950/5 lg:to-transparent"
                            aria-hidden="true"
                        ></div>
                    @else
                        <div
                            class="flex h-full min-h-[min(22rem,55vh)] flex-col items-center justify-center bg-gradient-to-br from-stone-800 via-stone-900 to-[color-mix(in_srgb,var(--bistro-color-primary)_22%,#0c0a09)] px-8 lg:min-h-0"
                            role="img"
                            aria-label="{{ filled($imageAlt) ? $imageAlt : 'Signature culinaire' }}"
                        >
                            <span class="font-[family-name:var(--bistro-font-heading)] text-7xl font-light text-white/[0.07] sm:text-8xl" aria-hidden="true">✦</span>
                            <p class="mt-6 max-w-xs text-center text-sm leading-relaxed text-stone-400">
                                Ajoutez une photo d’assiette ou de cuisine dans l’administration pour illustrer ce bloc.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
