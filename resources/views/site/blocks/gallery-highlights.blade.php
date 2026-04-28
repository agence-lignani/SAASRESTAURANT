@php
    $variant = $data['variant'] ?? 'gallery_bento';
    $items = $data['items'] ?? [];
    $lead = $items[0] ?? null;      // grand visuel horizontal
    $reserve = $items[1] ?? null;   // visuel vertical "reserve"
    $kitchen = $items[2] ?? null;   // visuel détail
    $lounge = $items[3] ?? null;    // carte éditoriale

    $heading = $data['heading'] ?? null;
    if (is_array($heading)) {
        $heading = $heading['content'] ?? $heading['text'] ?? null;
        if (is_array($heading)) {
            $heading = null;
        }
    }

    $intro = $data['intro'] ?? null;
    if (is_array($intro)) {
        $intro = $intro['content'] ?? $intro['text'] ?? null;
        if (is_array($intro)) {
            $intro = null;
        }
    }
@endphp

<section class="bistro-section-soft py-20 md:py-28" aria-labelledby="home-gallery-highlights-heading">
    <div class="bistro-container">
        <div class="space-y-10">
            <header class="mx-auto max-w-4xl text-center">
                <span class="v2-chip mb-4">Expérience sensorielle</span>
                <p class="epicure-kicker mb-4">L’atmosphère</p>
                <h2 id="home-gallery-highlights-heading" class="v2-display">
                    {{ is_string($heading) && filled($heading) ? $heading : 'Galerie ambiance' }}
                </h2>
                <p class="bistro-text-lead mt-5">
                    {{ is_string($intro) && filled($intro) ? $intro : 'Salle, terrasse, cuisine et service : un aperçu de l’expérience sur place.' }}
                </p>
                <div class="bistro-gold-line mx-auto mt-6"></div>
            </header>

            @if ($variant === 'bakery_explore_grid')
            <div class="mb-6 flex flex-wrap items-center justify-center gap-5 text-[0.65rem] uppercase tracking-[0.2em] text-[var(--v2-muted)]">
                <span>Muffins</span><span>Cookies</span><span>Bread</span><span>Cake</span><span>Favorite</span>
            </div>
            <div class="grid grid-cols-2 gap-3 md:grid-cols-3">
                @foreach ($items as $item)
                    @if (filled($item['image_url'] ?? null))
                        <figure class="v2-image-frame overflow-hidden bg-stone-100">
                            <img src="{{ $item['image_url'] }}" alt="{{ $item['image_alt'] ?? '' }}" class="h-40 w-full object-cover md:h-48" loading="lazy">
                        </figure>
                    @endif
                @endforeach
            </div>
            @elseif ($variant === 'gallery_film_strip')
            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                @foreach ($items as $item)
                    @if (filled($item['image_url'] ?? null))
                        <figure class="group v2-image-frame relative min-h-[260px] bg-stone-100">
                            <img src="{{ $item['image_url'] }}" alt="{{ $item['image_alt'] ?? '' }}" class="h-full w-full object-cover transition duration-700 group-hover:scale-[1.04]" loading="lazy">
                            @if (filled($item['caption'] ?? null))
                                <figcaption class="absolute bottom-4 left-4 text-sm uppercase tracking-[0.16em] text-white/90">{{ $item['caption'] }}</figcaption>
                            @endif
                        </figure>
                    @endif
                @endforeach
            </div>
            @elseif ($variant === 'gallery_collage_editorial')
            <div class="grid grid-cols-1 gap-6 md:grid-cols-10 md:auto-rows-[220px]">
                @foreach ($items as $index => $item)
                    @if (filled($item['image_url'] ?? null))
                        <figure class="group v2-image-frame relative bg-stone-100 {{ $index === 0 ? 'md:col-span-6 md:row-span-2' : ($index === 1 ? 'md:col-span-4' : 'md:col-span-5') }}">
                            <img src="{{ $item['image_url'] }}" alt="{{ $item['image_alt'] ?? '' }}" class="h-full w-full object-cover transition duration-700 group-hover:scale-[1.04]" loading="lazy">
                            @if (filled($item['caption'] ?? null))
                                <figcaption class="absolute bottom-4 left-4 text-sm uppercase tracking-[0.16em] text-white/90">{{ $item['caption'] }}</figcaption>
                            @endif
                        </figure>
                    @endif
                @endforeach
            </div>
            @else
            <div class="grid grid-cols-1 gap-6 md:grid-cols-12 md:auto-rows-[280px]">
                @if (! empty($lead) && filled($lead['image_url'] ?? null))
                    <figure class="group v2-image-frame relative bg-stone-100 md:col-span-8">
                        <img
                            src="{{ $lead['image_url'] }}"
                            alt="{{ $lead['image_alt'] ?? '' }}"
                            class="h-72 w-full object-cover transition duration-700 group-hover:scale-[1.03] md:h-full"
                            loading="lazy"
                        >
                        <div class="epicure-fade-top absolute inset-0"></div>
                        @if (filled($lead['caption'] ?? null))
                            <figcaption class="absolute bottom-5 left-5 text-lg text-white md:text-2xl">
                                {{ $lead['caption'] }}
                            </figcaption>
                        @endif
                    </figure>
                @endif

                @if (! empty($reserve) && filled($reserve['image_url'] ?? null))
                    <figure class="group v2-image-frame relative bg-stone-100 shadow-[0px_20px_40px_rgba(28,28,25,0.06)] md:col-span-4 md:row-span-2">
                        <img
                            src="{{ $reserve['image_url'] }}"
                            alt="{{ $reserve['image_alt'] ?? '' }}"
                            class="h-72 w-full object-cover transition duration-700 group-hover:scale-[1.03] md:h-full"
                            loading="lazy"
                        >
                        <div class="epicure-fade-top absolute inset-0"></div>
                        @if (filled($reserve['caption'] ?? null))
                            <figcaption class="absolute bottom-5 left-5 text-lg text-white md:text-2xl">
                                {{ $reserve['caption'] }}
                            </figcaption>
                        @endif
                    </figure>
                @endif

                @if (! empty($kitchen) && filled($kitchen['image_url'] ?? null))
                    <figure class="group v2-image-frame bg-stone-100 md:col-span-4">
                        <img
                            src="{{ $kitchen['image_url'] }}"
                            alt="{{ $kitchen['image_alt'] ?? '' }}"
                            class="h-64 w-full object-cover transition duration-700 group-hover:scale-[1.03] md:h-full"
                            loading="lazy"
                        >
                    </figure>
                @endif

                <article class="v2-section-shell flex flex-col items-center justify-center rounded-xl bg-stone-200/70 p-8 text-center md:col-span-4">
                    <p class="mb-3 text-xs uppercase tracking-[0.2em] text-stone-500">Ambiance</p>
                    <h3 class="bistro-title text-2xl text-stone-900">
                        {{ $lounge['caption'] ?? 'Le lounge' }}
                    </h3>
                    <p class="mt-3 max-w-sm text-sm leading-relaxed text-stone-600">
                        {{ $lounge['image_alt'] ?? 'Un espace plus intimiste pour prolonger l’expérience autour d’un digestif ou d’un accord mets-vins.' }}
                    </p>
                </article>
            </div>
            @endif

            @if (empty($items))
                <div class="rounded-xl bg-stone-100 p-8 text-sm text-stone-600">
                    Ajoute au moins 4 visuels dans l’administration pour obtenir la composition complète type maquette.
                </div>
            @endif

            @if (filled($data['gallery_link_label'] ?? null) && filled($data['gallery_link_href'] ?? null))
                <div class="pt-2">
                    <a href="{{ $data['gallery_link_href'] }}" class="bistro-btn-secondary w-full sm:w-auto">
                        {{ $data['gallery_link_label'] }}
                    </a>
                </div>
            @endif
        </div>
    </div>
</section>
