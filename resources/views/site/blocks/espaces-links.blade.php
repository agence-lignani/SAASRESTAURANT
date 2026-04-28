@php
    $variant = $data['variant'] ?? 'about_founder_focus';
@endphp

<section class="bistro-section-plain py-20 md:py-28" aria-labelledby="espaces-heading">
    <div class="bistro-container">
        @if ($variant === 'bakery_featured_treats')
            <header class="mx-auto mb-12 max-w-4xl text-center">
                <span class="v2-chip mb-4">Nos classiques</span>
                <p class="epicure-kicker mb-3">{{ $data['eyebrow'] ?? 'Featured' }}</p>
                <h2 id="espaces-heading" class="v2-display">{{ $data['heading'] ?? 'Featured Treats' }}</h2>
            </header>
            @php
                $treats = $data['links'] ?? [];
            @endphp
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($treats as $item)
                    <article class="overflow-hidden border border-stone-200 bg-white p-3">
                        <div class="v2-image-frame aspect-[4/3] bg-stone-100">
                            @if (filled($data['image_url'] ?? null))
                                <img src="{{ $data['image_url'] }}" alt="{{ $data['image_alt'] ?? '' }}" class="h-full w-full object-cover" loading="lazy">
                            @endif
                        </div>
                        <div class="mt-3 flex items-center justify-between gap-2">
                            <h3 class="text-sm font-semibold text-[var(--v2-ink)]">{{ $item['label'] ?? 'Treat' }}</h3>
                            <span class="text-xs font-semibold text-[var(--v2-accent)]">$8</span>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
        <div class="grid items-center gap-12 lg:grid-cols-2 lg:gap-20">
            <div class="order-2 lg:order-1">
                <span class="v2-chip mb-4">{{ $variant === 'about_side_label' ? 'Atelier du chef' : 'Portrait culinaire' }}</span>
                <p class="epicure-kicker mb-4">{{ $data['eyebrow'] ?? 'Le chef' }}</p>
                <h2 id="espaces-heading" class="v2-display">
                    {{ $data['heading'] ?? 'Antoine Dubois' }}
                </h2>
                <div class="mt-8 h-px w-24 bg-[var(--bistro-color-primary)]"></div>

                <div class="prose prose-stone mt-8 max-w-none text-lg leading-relaxed text-stone-600 prose-p:my-0">
                    {!! \App\Support\SiteContent\SiteContentHtml::safe($data['body'] ?? '<p>Formé dans les maisons parisiennes, le chef défend une cuisine française précise, lisible et de saison.</p><p>Chaque assiette équilibre tradition, modernité et gourmandise, avec un soin particulier porté aux cuissons et aux jus.</p>') !!}
                </div>

                @if (filled($data['recognition_value'] ?? null))
                    <div class="mt-10 flex items-center gap-6">
                        <div class="h-16 w-px bg-[var(--bistro-color-primary)]"></div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[var(--bistro-color-primary)]">
                                {{ $data['recognition_label'] ?? 'Reconnaissance' }}
                            </p>
                            <p class="mt-1 font-[family-name:var(--bistro-font-heading)] text-xl text-stone-900">
                                {{ $data['recognition_value'] }}
                            </p>
                        </div>
                    </div>
                @endif

                @if(! empty($data['links']))
                    <div class="mt-10 flex flex-wrap gap-3">
                        @foreach($data['links'] as $index => $link)
                            @if(filled($link['href'] ?? null))
                                <a
                                    href="{{ $link['href'] }}"
                                    class="{{ $index === 0 ? 'bistro-btn-primary' : 'bistro-btn-secondary' }}"
                                >
                                    {{ $link['label'] }}
                                </a>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="order-1 lg:order-2 {{ $variant === 'about_side_label' ? 'lg:pr-12' : '' }}">
                <div class="relative h-[520px] md:h-[640px]">
                    <div class="absolute left-10 top-0 h-3/4 w-4/5 overflow-hidden shadow-2xl">
                        @if (filled($data['image_url'] ?? null))
                            <img src="{{ $data['image_url'] }}" alt="{{ $data['image_alt'] ?? '' }}" class="h-full w-full object-cover" loading="lazy">
                        @else
                            <div class="flex h-full w-full items-center justify-center bg-stone-200 text-stone-500">Photo du chef</div>
                        @endif
                    </div>
                    <div class="absolute bottom-0 right-0 h-1/2 w-2/3 border-4 border-[var(--bistro-color-primary)]"></div>
                </div>
            </div>
        </div>
        @endif
    </div>
</section>
