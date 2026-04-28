@php
    $items = $data['items'] ?? [];
    $variant = $data['variant'] ?? 'faq_accordion_cards';
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

<section class="bistro-section-soft py-20 md:py-28" aria-labelledby="faq-heading">
    <div class="bistro-container">
        <header class="mx-auto mb-16 max-w-4xl text-center">
            <span class="v2-chip mb-4">Assistance rapide</span>
            <p class="epicure-kicker mb-4">Questions fréquentes</p>
            <h2 id="faq-heading" class="v2-display">
                {{ is_string($heading) && filled($heading) ? $heading : 'FAQ' }}
            </h2>
            <div class="bistro-gold-line mx-auto mt-6"></div>
            @if (is_string($intro) && filled($intro))
                <p class="mx-auto mt-6 max-w-3xl text-base leading-relaxed text-stone-600">
                    {{ $intro }}
                </p>
            @endif
        </header>

        @if (! empty($items))
            @if ($variant === 'faq_two_columns_qa')
                <div class="mx-auto grid max-w-5xl gap-4 md:grid-cols-2">
                    @foreach ($items as $item)
                        @if (filled($item['question'] ?? null))
                            <article class="bistro-accent-card v2-section-shell p-5 md:p-6">
                                <h3 class="font-[family-name:var(--bistro-font-heading)] text-2xl text-[var(--v2-ink)]">{{ $item['question'] }}</h3>
                                @if (filled($item['answer'] ?? null))
                                    <div class="mt-4 text-base leading-relaxed text-slate-600">
                                        {!! \App\Support\SiteContent\SiteContentHtml::safe($item['answer']) !!}
                                    </div>
                                @endif
                            </article>
                        @endif
                    @endforeach
                </div>
            @elseif ($variant === 'faq_minimal_lines')
                <div class="mx-auto max-w-4xl divide-y divide-slate-200">
                    @foreach ($items as $item)
                        @if (filled($item['question'] ?? null))
                            <details class="group py-4">
                                <summary class="flex cursor-pointer list-none items-center justify-between text-left">
                                    <span class="pr-8 font-[family-name:var(--bistro-font-heading)] text-2xl text-[var(--v2-ink)]">{{ $item['question'] }}</span>
                                    <span class="text-[var(--v2-accent)] transition group-open:rotate-180">⌄</span>
                                </summary>
                                @if (filled($item['answer'] ?? null))
                                    <div class="pt-4 text-base leading-relaxed text-slate-600">
                                        {!! \App\Support\SiteContent\SiteContentHtml::safe($item['answer']) !!}
                                    </div>
                                @endif
                            </details>
                        @endif
                    @endforeach
                </div>
            @else
                <div class="mx-auto max-w-4xl space-y-3">
                    @foreach ($items as $item)
                        @if (filled($item['question'] ?? null))
                            <details class="group bistro-accent-card v2-section-shell px-5 md:px-7">
                                <summary class="flex cursor-pointer list-none items-center justify-between py-6 text-left">
                                    <span class="pr-8 font-[family-name:var(--bistro-font-heading)] text-2xl text-[#1a1614]">
                                        {{ $item['question'] }}
                                    </span>
                                    <span class="text-[var(--v2-accent)] transition group-open:rotate-180">⌄</span>
                                </summary>
                                @if (filled($item['answer'] ?? null))
                                    <div class="pb-6 text-lg leading-relaxed text-slate-600">
                                        {!! \App\Support\SiteContent\SiteContentHtml::safe($item['answer']) !!}
                                    </div>
                                @endif
                            </details>
                        @endif
                    @endforeach
                </div>
            @endif
        @endif

        @if (filled($data['contact_label'] ?? null) && filled($data['contact_href'] ?? null))
            <div class="mt-10 text-center">
                <a href="{{ $data['contact_href'] }}" class="inline-flex border-b border-[var(--v2-accent)]/40 pb-1 text-xs font-semibold uppercase tracking-[0.2em] text-[var(--v2-ink)] hover:text-[var(--v2-accent)]">
                    {{ $data['contact_label'] }}
                </a>
            </div>
        @endif
    </div>
</section>
