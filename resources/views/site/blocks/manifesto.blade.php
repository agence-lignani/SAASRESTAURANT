@php
    $variant = $data['variant'] ?? 'about_chapter_layout';
    $paragraphs = $data['paragraphs'] ?? (isset($data['body']) ? [$data['body']] : []);
    $eyebrow = $data['eyebrow'] ?? 'Notre histoire';
    $heading = $data['heading'] ?? 'Notre maison';
    $imageUrl = $data['image_url'] ?? null;
    $imageAlt = $data['image_alt'] ?? '';
    if (! is_array($paragraphs) || $paragraphs === []) {
        $paragraphs = [
            'Nous avons ouvert cette maison avec une idée simple : proposer une table de quartier exigeante, où l’on mange juste et où l’on revient souvent.',
            'Le menu suit le marché, les saisons et les rencontres avec nos producteurs. Ici, la cuisine reste lisible, généreuse et sincère.',
        ];
    }
@endphp

<section class="bistro-section-soft py-16 md:py-20" aria-labelledby="manifesto-heading">
    <div class="bistro-container">
        @if ($variant === 'bakery_about_band')
            <div class="v2-image-frame relative overflow-hidden">
                @if (filled($imageUrl))
                    <img src="{{ $imageUrl }}" alt="{{ $imageAlt }}" class="h-[260px] w-full object-cover md:h-[320px]" loading="lazy" decoding="async">
                @else
                    <div class="h-[260px] w-full bg-stone-700 md:h-[320px]"></div>
                @endif
                <div class="absolute inset-0 bg-black/55"></div>
                <div class="absolute inset-0 flex items-center justify-center px-6 text-center">
                    <div class="max-w-2xl">
                        <p class="text-xs uppercase tracking-[0.2em] text-white/80">{{ $eyebrow }}</p>
                        <h2 id="manifesto-heading" class="mt-3 font-[family-name:var(--bistro-font-heading)] text-4xl text-white md:text-5xl">{{ $heading }}</h2>
                        @if (! empty($paragraphs))
                            <p class="mx-auto mt-4 max-w-xl text-sm leading-relaxed text-white/85">
                                {{ strip_tags((string) ($paragraphs[0] ?? '')) }}
                            </p>
                        @endif
                        @if (! empty($data['more_links'][0]['href'] ?? null))
                            <a href="{{ $data['more_links'][0]['href'] }}" class="mt-6 inline-flex h-10 items-center justify-center bg-[#d97a3a] px-5 text-[0.65rem] font-semibold uppercase tracking-[0.18em] text-white transition hover:brightness-105">
                                {{ $data['more_links'][0]['label'] ?? 'Read More' }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @elseif ($variant === 'about_cinematic_panel')
            <div class="bistro-section-card v2-section-shell">
                <div class="grid gap-8 lg:grid-cols-12 lg:items-stretch">
                    <div class="v2-image-frame lg:col-span-7">
                        @if (filled($imageUrl))
                            <img src="{{ $imageUrl }}" alt="{{ $imageAlt }}" class="h-full w-full object-cover" loading="lazy" decoding="async">
                        @else
                            <div class="flex h-full min-h-[340px] items-center justify-center bg-stone-100 text-stone-500">Photo de la maison</div>
                        @endif
                    </div>
                    <div class="lg:col-span-5 lg:flex lg:flex-col lg:justify-center">
                        <span class="v2-chip mb-4">Racines & Maison</span>
                        <p class="epicure-kicker mb-3">{{ $eyebrow }}</p>
                        <h2 id="manifesto-heading" class="v2-display">{{ $heading }}</h2>
                        <div class="bistro-gold-line mt-6"></div>
                        <div class="mt-7 space-y-4">
                            @foreach (array_slice($paragraphs, 0, 2) as $paragraph)
                                <div class="bistro-manifesto-body">
                                    {!! \App\Support\SiteContent\SiteContentHtml::paragraph($paragraph) !!}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @elseif ($variant === 'about_image_stack')
            <div class="grid items-center gap-12 lg:grid-cols-2">
                <div class="relative h-[420px] md:h-[520px]">
                    <div class="v2-image-frame absolute left-0 top-0 h-3/4 w-4/5">
                        @if (filled($imageUrl))
                            <img src="{{ $imageUrl }}" alt="{{ $imageAlt }}" class="h-full w-full object-cover" loading="lazy" decoding="async">
                        @else
                            <div class="flex h-full items-center justify-center bg-stone-100 text-stone-500">Photo de la maison</div>
                        @endif
                    </div>
                    <div class="v2-section-shell absolute bottom-0 right-0 h-1/2 w-2/3 rounded-2xl bg-white p-5">
                        <p class="text-[0.68rem] font-semibold uppercase tracking-[0.2em] text-[var(--v2-accent)]">Expérience</p>
                        <p class="mt-3 text-sm leading-relaxed text-[var(--v2-muted)]">
                            Produits de saison, accueil attentif, tempo de service pensé pour le confort.
                        </p>
                    </div>
                </div>
                <div class="bistro-section-card v2-section-shell">
                    <span class="v2-chip mb-4">Racines & Maison</span>
                    <p class="epicure-kicker mb-3">{{ $eyebrow }}</p>
                    <h2 id="manifesto-heading" class="v2-display">{{ $heading }}</h2>
                    <div class="bistro-gold-line mt-6"></div>
                    <div class="mt-7 space-y-5">
                        @foreach (array_slice($paragraphs, 0, 3) as $paragraph)
                            <div class="{{ $loop->first ? 'bistro-manifesto-lead' : 'bistro-manifesto-body' }}">
                                {!! \App\Support\SiteContent\SiteContentHtml::paragraph($paragraph) !!}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @else
        <div class="bistro-section-card v2-section-shell">
            <header class="mb-10 text-center md:text-left">
                <span class="v2-chip mb-4">Raisons de choisir la maison</span>
                <p class="epicure-kicker mb-3">{{ $eyebrow }}</p>
                <h2 id="manifesto-heading" class="v2-display">
                    {{ $heading }}
                </h2>
                <div class="bistro-gold-line mt-6"></div>
            </header>

            <div class="grid gap-5 md:grid-cols-3">
                @foreach ($paragraphs as $index => $paragraph)
                    @if ($index < 3)
                        <article class="bistro-accent-card p-5 md:p-6">
                            <p class="mb-3 text-[0.68rem] font-semibold uppercase tracking-[0.2em] text-[var(--v2-accent)]">
                                {{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}
                            </p>
                            <div class="{{ $index === 0 ? 'bistro-manifesto-lead' : 'bistro-manifesto-body' }}">
                                {!! \App\Support\SiteContent\SiteContentHtml::paragraph($paragraph) !!}
                            </div>
                        </article>
                    @endif
                @endforeach

                @if (filled($imageUrl))
                    <article class="v2-image-frame overflow-hidden md:col-span-3 lg:col-span-1">
                        <img src="{{ $imageUrl }}" alt="{{ $imageAlt }}" class="h-56 w-full object-cover md:h-72" loading="lazy" decoding="async">
                    </article>
                @endif
            </div>

            @if (! empty($data['signature']) || ! empty($data['more_links']))
                <div class="mt-9 flex flex-col gap-4 border-t border-stone-200 pt-7 sm:flex-row sm:items-center sm:justify-between">
                @if (! empty($data['signature']))
                    <p class="bistro-manifesto-signature">{{ $data['signature'] }}</p>
                @endif
                @if (! empty($data['more_links']))
                    <div class="flex flex-wrap gap-3">
                        @foreach ($data['more_links'] as $link)
                            @if (filled($link['href'] ?? null))
                                <a href="{{ $link['href'] }}" class="bistro-btn-secondary">
                                    {{ $link['label'] ?? 'En savoir plus' }}
                                </a>
                            @endif
                        @endforeach
                    </div>
                @endif
                </div>
            @endif
        </div>
        @endif
    </div>
</section>
