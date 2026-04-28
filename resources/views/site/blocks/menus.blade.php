@php
    $variant = $data['variant'] ?? 'menu_featured_primary';
    $items = $data['items'] ?? [];
    $featured = $items[0] ?? null;
    $secondary = array_slice($items, 1, 2);
    $intro = $data['intro'] ?? null;
    if (is_array($intro)) {
        $intro = $intro['content'] ?? $intro['text'] ?? null;
        if (is_array($intro)) {
            $intro = null;
        }
    }
@endphp

<section class="bistro-section-plain py-20 md:py-28" aria-labelledby="home-menus-heading">
    <div class="bistro-container">
        <header class="mx-auto mb-16 max-w-4xl text-center">
            <span class="v2-chip mb-4">Sélection du chef</span>
            <p class="epicure-kicker mb-4">Notre carte</p>
            <h2 id="home-menus-heading" class="v2-display">
                {{ $data['heading'] ?? 'Signatures culinaires' }}
            </h2>
            <div class="mx-auto mt-6 h-px w-24 bg-[var(--bistro-color-primary)]"></div>
            <p class="bistro-text-lead mt-6">
                {{ is_string($intro) && filled($intro) ? $intro : 'Une sélection des assiettes qui racontent notre cuisine.' }}
            </p>
        </header>

        @if (! empty($items))
            @if ($variant === 'bakery_top_products')
            <div class="mx-auto max-w-5xl">
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($items as $item)
                        @if (filled($item['title'] ?? null))
                            <article class="overflow-hidden border border-stone-700 bg-[#17181d] p-3">
                                <div class="v2-image-frame aspect-[4/3] bg-stone-900">
                                    @if (filled($item['image_url'] ?? null))
                                        <img src="{{ $item['image_url'] }}" alt="{{ $item['image_alt'] ?? $item['title'] }}" class="h-full w-full object-cover" loading="lazy">
                                    @endif
                                </div>
                                <div class="mt-3 flex items-start justify-between gap-2">
                                    <div>
                                        <p class="text-sm font-semibold text-white">{{ $item['price'] ?? '$10' }}</p>
                                        <h3 class="mt-1 text-sm font-medium text-white/90">{{ $item['title'] }}</h3>
                                    </div>
                                    <span class="inline-flex h-6 items-center justify-center bg-[#d97a3a] px-2 text-[0.6rem] font-semibold uppercase tracking-[0.14em] text-white">Add</span>
                                </div>
                            </article>
                        @endif
                    @endforeach
                </div>
            </div>
            @elseif ($variant === 'menu_grid_minimal')
            <div class="grid gap-8 md:grid-cols-3">
                @foreach ($items as $item)
                    @if (filled($item['title'] ?? null))
                        <article class="group">
                            <div class="v2-image-frame mb-5 aspect-[3/4] bg-stone-100">
                                @if (filled($item['image_url'] ?? null))
                                    <img src="{{ $item['image_url'] }}" alt="{{ $item['image_alt'] ?? $item['title'] }}" class="h-full w-full object-cover transition duration-700 group-hover:scale-105" loading="lazy">
                                @endif
                            </div>
                            <h3 class="font-[family-name:var(--bistro-font-heading)] text-3xl text-[var(--v2-ink)]">{{ $item['title'] }}</h3>
                            @if (filled($item['description'] ?? null))
                                <p class="mt-3 text-sm leading-relaxed text-[var(--v2-muted)]">{{ $item['description'] }}</p>
                            @endif
                            @if (filled($item['price'] ?? null))
                                <p class="mt-3 text-[0.75rem] font-semibold uppercase tracking-[0.2em] text-[var(--v2-accent)]">{{ $item['price'] }}</p>
                            @endif
                        </article>
                    @endif
                @endforeach
            </div>
            @elseif ($variant === 'menu_masonry_cards')
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($items as $index => $item)
                    @if (filled($item['title'] ?? null))
                        <article class="bistro-accent-card v2-section-shell p-4 md:p-5 {{ $index % 3 === 0 ? 'md:row-span-2' : '' }}">
                            <div class="v2-image-frame mb-4 {{ $index % 3 === 0 ? 'aspect-[3/4]' : 'aspect-[4/3]' }} bg-stone-100">
                                @if (filled($item['image_url'] ?? null))
                                    <img src="{{ $item['image_url'] }}" alt="{{ $item['image_alt'] ?? $item['title'] }}" class="h-full w-full object-cover" loading="lazy">
                                @endif
                            </div>
                            <h3 class="font-[family-name:var(--bistro-font-heading)] text-2xl text-[var(--v2-ink)]">{{ $item['title'] }}</h3>
                            @if (filled($item['description'] ?? null))
                                <p class="mt-2 text-sm leading-relaxed text-[var(--v2-muted)]">{{ $item['description'] }}</p>
                            @endif
                            @if (filled($item['price'] ?? null))
                                <p class="mt-3 text-[0.75rem] font-semibold uppercase tracking-[0.2em] text-[var(--v2-accent)]">{{ $item['price'] }}</p>
                            @endif
                        </article>
                    @endif
                @endforeach
            </div>
            @else
            <div class="grid gap-8 lg:grid-cols-12">
                @if (filled($featured['title'] ?? null))
                    <article class="group bistro-accent-card v2-section-shell p-5 md:p-6 lg:col-span-7">
                        <div class="grid gap-6 md:grid-cols-2 md:items-center">
                            <div class="v2-image-frame aspect-[4/5] bg-stone-100">
                                @if (filled($featured['image_url'] ?? null))
                                    <img src="{{ $featured['image_url'] }}" alt="{{ $featured['image_alt'] ?? $featured['title'] }}" class="h-full w-full object-cover transition duration-700 group-hover:scale-105" loading="lazy">
                                @endif
                            </div>
                            <div>
                                <p class="mb-3 text-[0.7rem] font-semibold uppercase tracking-[0.24em] text-[var(--v2-accent)]">Plat vedette</p>
                                <h3 class="font-[family-name:var(--bistro-font-heading)] text-4xl leading-tight text-[var(--v2-ink)]">
                                    {{ $featured['title'] }}
                                </h3>
                                @if (filled($featured['description'] ?? null))
                                    <p class="mt-4 text-base leading-relaxed text-[var(--v2-muted)]">
                                        {{ $featured['description'] }}
                                    </p>
                                @endif
                                @if (filled($featured['price'] ?? null))
                                    <p class="mt-5 text-sm font-semibold uppercase tracking-[0.2em] text-[var(--v2-accent)]">
                                        {{ $featured['price'] }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </article>
                @endif

                <div class="grid gap-6 md:grid-cols-2 lg:col-span-5 lg:grid-cols-1">
                    @foreach ($secondary as $item)
                        @if (filled($item['title'] ?? null))
                            <article class="group bistro-accent-card v2-section-shell p-4 md:p-5">
                                <div class="grid gap-4 sm:grid-cols-[120px_1fr] sm:items-center">
                                    <div class="v2-image-frame aspect-square bg-stone-100">
                                        @if (filled($item['image_url'] ?? null))
                                            <img src="{{ $item['image_url'] }}" alt="{{ $item['image_alt'] ?? $item['title'] }}" class="h-full w-full object-cover transition duration-700 group-hover:scale-105" loading="lazy">
                                        @endif
                                    </div>
                                    <div>
                                        <h4 class="font-[family-name:var(--bistro-font-heading)] text-2xl leading-tight text-[var(--v2-ink)]">
                                            {{ $item['title'] }}
                                        </h4>
                                        @if (filled($item['description'] ?? null))
                                            <p class="mt-2 text-sm leading-relaxed text-[var(--v2-muted)]">
                                                {{ $item['description'] }}
                                            </p>
                                        @endif
                                        @if (filled($item['price'] ?? null))
                                            <p class="mt-3 text-[0.75rem] font-semibold uppercase tracking-[0.2em] text-[var(--v2-accent)]">
                                                {{ $item['price'] }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif
        @endif

        @if (! empty($data['cta_buttons']))
            <div class="mt-16 flex justify-center">
                @foreach($data['cta_buttons'] as $button)
                    @php
                        $variant = $button['variant'] ?? 'primary';
                        $btnClass = $variant === 'secondary' ? 'bistro-btn-secondary' : 'bistro-btn-primary';
                    @endphp
                    @if (filled($button['href'] ?? null))
                        <a href="{{ $button['href'] }}" class="{{ $btnClass }}">
                            {{ $button['label'] ?? 'Voir la carte complète' }}
                        </a>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
</section>
