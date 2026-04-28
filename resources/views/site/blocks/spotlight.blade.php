@php
    $variant = $data['variant'] ?? 'cta_floating_card';
@endphp

<section class="relative overflow-hidden py-24 md:py-36" aria-labelledby="reservation-cta-heading">
    <div class="absolute inset-0">
        @if (filled($data['image_url'] ?? null))
            <img src="{{ $data['image_url'] }}" alt="" class="h-full w-full object-cover" loading="lazy">
        @else
            <div class="h-full w-full bg-stone-900"></div>
        @endif
        <div class="absolute inset-0 bg-gradient-to-b from-[#0f172abf] via-[#312e8199] to-[#0f172ae6]"></div>
    </div>

    <div class="relative bistro-container">
        @if ($variant === 'bakery_promo_banner')
            <div class="mx-auto grid max-w-5xl gap-6 border border-stone-200 bg-[#f3e2d7] p-6 md:grid-cols-12 md:items-center md:p-8">
                <div class="md:col-span-8">
                    <p class="text-xs uppercase tracking-[0.2em] text-[#8f4f2d]">Offre spéciale</p>
                    <h2 id="reservation-cta-heading" class="mt-2 font-[family-name:var(--bistro-font-heading)] text-4xl text-[#1f1a16] md:text-5xl">
                        {{ $data['heading'] ?? '20% Off Your First Order' }}
                    </h2>
                    @if (filled($data['body'] ?? null))
                        <div class="mt-3 max-w-xl text-sm leading-relaxed text-[#5a473b]">
                            {!! \App\Support\SiteContent\SiteContentHtml::safe($data['body']) !!}
                        </div>
                    @endif
                </div>
                <div class="md:col-span-4 md:text-right">
                    @foreach($data['buttons'] ?? [] as $button)
                        @if(filled($button['href'] ?? null))
                            <a href="{{ $button['href'] }}" class="inline-flex h-10 items-center justify-center bg-[#d97a3a] px-5 text-[0.65rem] font-semibold uppercase tracking-[0.18em] text-white transition hover:brightness-105">
                                {{ $button['label'] ?? 'Learn More' }}
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        @elseif ($variant === 'cta_split_action')
            <div class="mx-auto grid max-w-5xl gap-8 rounded-[1.25rem] border border-white/20 bg-white/10 p-8 backdrop-blur-md md:p-12 lg:grid-cols-12 lg:items-center">
                <div class="lg:col-span-8">
                    <span class="v2-chip mb-4 border-white/40 bg-white/12 text-white">Réservation</span>
                    <h2 id="reservation-cta-heading" class="font-[family-name:var(--bistro-font-heading)] text-4xl leading-tight text-white md:text-6xl">
                        {{ $data['heading'] ?? 'Réservez votre table' }}
                    </h2>
                    @if (filled($data['body'] ?? null))
                        <div class="prose prose-invert mt-6 max-w-2xl text-base leading-relaxed text-white/90 prose-p:my-0 md:text-lg">
                            {!! \App\Support\SiteContent\SiteContentHtml::safe($data['body']) !!}
                        </div>
                    @endif
                </div>
                <div class="lg:col-span-4">
                    <div class="flex flex-col gap-3">
                        @foreach($data['buttons'] ?? [] as $button)
                            @if(filled($button['href'] ?? null))
                                <a href="{{ $button['href'] }}" class="bistro-btn-primary w-full justify-center">
                                    {{ $button['label'] ?? 'Réserver maintenant' }}
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        @elseif ($variant === 'cta_full_bleed_impact')
            <div class="mx-auto max-w-5xl text-center">
                <span class="v2-chip mb-4 border-white/40 bg-white/12 text-white">Réservation</span>
                <h2 id="reservation-cta-heading" class="font-[family-name:var(--bistro-font-heading)] text-6xl leading-[0.95] tracking-[-0.03em] text-white md:text-8xl">
                    {{ $data['heading'] ?? 'Réservez votre table' }}
                </h2>
                <div class="mx-auto mt-6 h-px w-24 bg-gradient-to-r from-[#7c5cff] to-[#20c5ba]"></div>
                @if (filled($data['body'] ?? null))
                    <div class="prose prose-invert mx-auto mt-8 max-w-2xl text-lg leading-relaxed text-white/90 prose-p:my-0 md:text-xl">
                        {!! \App\Support\SiteContent\SiteContentHtml::safe($data['body']) !!}
                    </div>
                @endif
                <div class="mt-12 flex flex-wrap justify-center gap-3">
                    @foreach($data['buttons'] ?? [] as $button)
                        @if(filled($button['href'] ?? null))
                            <a href="{{ $button['href'] }}" class="bistro-btn-primary min-w-[12rem] px-10">
                                {{ $button['label'] ?? 'Réserver maintenant' }}
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        @else
            <div class="mx-auto max-w-4xl rounded-[1.25rem] border border-white/15 bg-white/10 p-8 text-center shadow-2xl backdrop-blur-md md:p-12">
            <span class="v2-chip mb-4 border-white/40 bg-white/12 text-white">Réservation</span>
            <h2 id="reservation-cta-heading" class="font-[family-name:var(--bistro-font-heading)] text-5xl leading-tight text-white md:text-7xl">
                {{ $data['heading'] ?? 'Réservez votre table' }}
            </h2>
            <div class="mx-auto mt-6 h-px w-24 bg-gradient-to-r from-[#7c5cff] to-[#20c5ba]"></div>
            @if (filled($data['body'] ?? null))
                <div class="prose prose-invert mx-auto mt-8 max-w-2xl text-lg leading-relaxed text-white/90 prose-p:my-0 md:text-xl">
                    {!! \App\Support\SiteContent\SiteContentHtml::safe($data['body']) !!}
                </div>
            @endif
            <div class="mt-12 flex flex-wrap justify-center gap-3">
                @foreach($data['buttons'] ?? [] as $button)
                    @if(filled($button['href'] ?? null))
                        <a href="{{ $button['href'] }}" class="bistro-btn-primary min-w-[12rem] px-10">
                            {{ $button['label'] ?? 'Réserver maintenant' }}
                        </a>
                    @endif
                @endforeach
            </div>
            </div>
        @endif
    </div>
</section>
