@php
    $items = $data['items'] ?? [];
@endphp

<section class="py-8 md:py-10" aria-labelledby="essentials-heading">
    <div class="bistro-container">
        <div class="bistro-section-card">
            <header class="bistro-section-header">
                <p class="bistro-section-kicker">Informations pratiques</p>
                <h2 id="essentials-heading" class="bistro-section-title">
                    {{ $data['heading'] ?? 'Infos essentielles' }}
                </h2>
                @if (filled($data['intro'] ?? null))
                    <p class="bistro-section-intro">
                        {{ $data['intro'] }}
                    </p>
                @endif
            </header>

            @if (! empty($items))
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($items as $item)
                        @if (filled($item['value'] ?? null))
                            <article class="rounded-2xl border border-stone-200/90 bg-gradient-to-br from-stone-50 to-white p-4 shadow-sm">
                                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[var(--bistro-color-primary)]">
                                    {{ $item['label'] ?? 'Info' }}
                                </p>
                                <p class="mt-2 text-sm font-medium leading-relaxed text-stone-800">
                                    {{ $item['value'] }}
                                </p>
                            </article>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>
