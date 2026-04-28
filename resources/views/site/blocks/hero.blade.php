@php
    $variant = $data['variant'] ?? 'hero_card_glass';
    $eyebrow = $data['eyebrow'] ?? 'Depuis 1985';
    $subtitle = $data['subtitle'] ?? '';
    $title = $data['title'] ?? $restaurant->name;
    $heroImage = $data['image_url'] ?? 'https://images.unsplash.com/photo-1715782414430-e1f0f4df354e?w=1800&q=80&auto=format&fit=crop';

    $buttons = $data['cta_buttons'] ?? [];
    if ($buttons === [] && (isset($data['cta_primary']) || isset($data['cta_secondary']))) {
        $buttons = array_values(array_filter([
            ($data['cta_primary'] ?? null),
            ($data['cta_secondary'] ?? null),
        ], fn ($button) => filled($button['href'] ?? null) && filled($button['label'] ?? null)));
    }
@endphp

<section class="relative min-h-[84vh] overflow-hidden" aria-labelledby="hero-title">
    <div class="absolute inset-0">
        <img
            src="{{ $heroImage }}"
            alt="{{ $data['image_alt'] ?? '' }}"
            class="h-full w-full object-cover"
            loading="eager"
        >
        <div class="absolute inset-0 bg-gradient-to-b from-[#0f172abf] via-[#1e1b4b99] to-[#0f172ae6]"></div>
    </div>

    @php
        $containerClass = match ($variant) {
            'bakery_hero_classic' => 'relative bistro-container flex min-h-[84vh] items-center py-20',
            'hero_split_editorial' => 'relative bistro-container grid min-h-[84vh] items-center gap-8 py-20 lg:grid-cols-12',
            'hero_minimal_mono' => 'relative bistro-container flex min-h-[84vh] items-end py-20',
            default => 'relative bistro-container flex min-h-[84vh] items-center py-20',
        };
        $panelClass = match ($variant) {
            'bakery_hero_classic' => 'max-w-xl bg-black/55 p-7 text-left md:p-10',
            'hero_full_bleed_cinema' => 'max-w-5xl border-l-2 border-white/60 pl-8 text-left md:pl-12',
            'hero_split_editorial' => 'max-w-3xl rounded-[1.1rem] border border-white/25 bg-black/25 p-8 text-left shadow-2xl backdrop-blur-sm md:p-10 lg:col-span-7',
            'hero_minimal_mono' => 'max-w-3xl text-left',
            default => 'max-w-4xl rounded-[1.25rem] border border-white/20 bg-white/10 p-8 text-center shadow-2xl backdrop-blur-md md:p-12',
        };
        $kickerClass = $variant === 'hero_card_glass'
            ? 'mx-auto mb-7 inline-flex rounded-full border border-white/25 bg-white/10 px-6 py-2 text-xs font-semibold uppercase tracking-[0.28em] text-white/90'
            : 'mb-7 inline-flex rounded-full border border-white/35 bg-black/25 px-6 py-2 text-xs font-semibold uppercase tracking-[0.28em] text-white/90';
        $lineClass = $variant === 'hero_card_glass'
            ? 'mx-auto mt-7 h-px w-24 bg-gradient-to-r from-[#7c5cff] to-[#20c5ba]'
            : 'mt-7 h-px w-24 bg-gradient-to-r from-[#7c5cff] to-[#20c5ba]';
        $titleClass = match ($variant) {
            'hero_minimal_mono' => 'font-[family-name:var(--bistro-font-heading)] text-4xl leading-[0.95] tracking-[-0.03em] text-white sm:text-6xl md:text-7xl',
            'hero_full_bleed_cinema' => 'font-[family-name:var(--bistro-font-heading)] text-5xl leading-[0.92] tracking-[-0.03em] text-white sm:text-6xl md:text-7xl lg:text-8xl',
            default => 'font-[family-name:var(--bistro-font-heading)] text-5xl leading-[0.95] tracking-[-0.03em] text-white sm:text-6xl md:text-7xl lg:text-8xl',
        };
        $subtitleClass = $variant === 'hero_card_glass'
            ? 'mx-auto mt-8 max-w-2xl text-lg leading-relaxed text-white/90 md:text-xl'
            : 'mt-8 max-w-2xl text-lg leading-relaxed text-white/90 md:text-xl';
        $ctaWrapClass = $variant === 'hero_card_glass'
            ? 'mt-12 flex flex-col items-center justify-center gap-3 sm:flex-row'
            : 'mt-12 flex flex-col gap-3 sm:flex-row';
        if ($variant === 'bakery_hero_classic') {
            $kickerClass = 'mb-4 inline-flex text-xs uppercase tracking-[0.2em] text-white/80';
            $titleClass = 'font-[family-name:var(--bistro-font-heading)] text-4xl leading-[1.02] text-white md:text-5xl';
            $lineClass = 'mt-5 h-px w-16 bg-[#d97a3a]';
            $subtitleClass = 'mt-5 max-w-md text-sm leading-relaxed text-white/85';
        }
    @endphp

    <div class="{{ $containerClass }}">
        @if($variant === 'hero_split_editorial')
            <div class="hidden lg:col-span-5 lg:block">
                <div class="v2-section-shell rounded-[1.1rem] bg-white/15 p-5 backdrop-blur-sm">
                    <p class="text-[0.68rem] font-semibold uppercase tracking-[0.2em] text-white/85">Réservation rapide</p>
                    <p class="mt-3 text-sm leading-relaxed text-white/85">
                        Cuisine de saison, service attentif, table prête en quelques clics.
                    </p>
                </div>
            </div>
        @endif

        <div class="{{ $panelClass }}">
            @if (filled($eyebrow))
                <p class="{{ $kickerClass }}">
                    {{ $eyebrow }}
                </p>
            @endif

            <h1 id="hero-title" class="{{ $titleClass }}">
                {!! nl2br(e($title)) !!}
            </h1>
            <div class="{{ $lineClass }}"></div>

            @if (filled($subtitle))
                <p class="{{ $subtitleClass }}">
                    {{ $subtitle }}
                </p>
            @endif

            @if (! empty($buttons))
                <div class="{{ $ctaWrapClass }}">
                    @foreach($buttons as $index => $button)
                        @php $isPrimary = ($button['variant'] ?? ($index === 0 ? 'primary' : 'secondary')) !== 'secondary'; @endphp
                        @if (filled($button['href'] ?? null))
                            <a
                                href="{{ $button['href'] }}"
                                class="{{ $variant === 'bakery_hero_classic' ? ($isPrimary ? 'inline-flex h-10 min-w-[8rem] items-center justify-center bg-[#d97a3a] px-5 text-[0.65rem] font-semibold uppercase tracking-[0.18em] text-white transition hover:brightness-105' : 'inline-flex h-10 min-w-[8rem] items-center justify-center border border-white/40 px-5 text-[0.65rem] font-semibold uppercase tracking-[0.18em] text-white transition hover:bg-white/10') : ($isPrimary ? 'bistro-btn-primary' : 'v2-btn-ghost-light') }}"
                            >
                                {{ $button['label'] }}
                            </a>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>
