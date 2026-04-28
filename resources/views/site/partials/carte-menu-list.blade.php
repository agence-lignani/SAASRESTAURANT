@if($categories->isEmpty())
    <p class="text-stone-500">{{ $pageContent['empty_state'] ?? 'La carte sera bientôt en ligne.' }}</p>
@else
    <div class="space-y-14">
        @foreach($categories as $category)
            <section class="scroll-mt-28" id="cat-{{ $category->id }}" aria-labelledby="heading-cat-{{ $category->id }}">
                <div class="flex flex-wrap items-end justify-between gap-4 border-b border-stone-200 pb-4">
                    <h2 id="heading-cat-{{ $category->id }}" class="text-xl font-semibold text-stone-900 md:text-2xl">
                        {{ $category->name }}
                    </h2>
                    @if(filled($category->menu_pdf_url))
                        <a href="{{ $category->menu_pdf_url }}" target="_blank" rel="noopener noreferrer" class="text-sm font-medium text-[var(--bistro-color-primary)] hover:underline">
                            {{ $pageContent['pdf_link_label'] ?? 'Télécharger le PDF' }}
                        </a>
                    @endif
                </div>
                @if(filled($category->description))
                    <p class="mt-3 text-sm text-stone-600">{{ $category->description }}</p>
                @endif
                @if($category->menuItems->isEmpty())
                    <p class="mt-6 text-sm text-stone-500">{{ $pageContent['empty_category_items'] ?? 'Aucun plat dans cette catégorie pour le moment.' }}</p>
                @else
                    <ul class="mt-8 space-y-6">
                        @foreach($category->menuItems as $item)
                            <li class="bistro-card p-5 md:p-6">
                                <div class="flex flex-wrap items-baseline justify-between gap-2">
                                    <h3 class="text-base font-semibold text-stone-900 md:text-lg">{{ $item->name }}</h3>
                                    @if($item->price !== null)
                                        <span class="text-sm font-semibold text-[var(--bistro-color-primary)] tabular-nums">
                                            {{ number_format((float) $item->price, 2, ',', ' ') }}&nbsp;{{ $item->currency ?? 'EUR' }}
                                        </span>
                                    @endif
                                </div>
                                @if(filled($item->description))
                                    <p class="mt-2 text-sm leading-relaxed text-stone-600">{{ $item->description }}</p>
                                @endif
                                @php
                                    $opts = \App\Support\AllergenCatalog::options();
                                    $allergens = $item->allergens ?? [];
                                    $flags = $item->dietary_flags ?? [];
                                @endphp
                                @if(! empty($allergens))
                                    <p class="mt-3 text-xs text-stone-500">
                                        <span class="font-semibold text-stone-700">{{ $pageContent['allergens_label'] ?? 'Allergènes :' }}</span>
                                        {{ collect($allergens)->map(fn ($k) => $opts[$k] ?? $k)->implode(' · ') }}
                                    </p>
                                @endif
                                @if(! empty($flags))
                                    <p class="mt-2 text-xs font-medium text-[var(--bistro-color-primary)]">
                                        {{ collect($flags)->map(fn ($f) => match ($f) {
                                            'vegetarian' => 'Végétarien',
                                            'vegan' => 'Vegan',
                                            'gluten_free' => 'Sans gluten',
                                            'spicy' => 'Épicé',
                                            default => $f,
                                        })->implode(' · ') }}
                                    </p>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        @endforeach
    </div>
@endif
@if($categories->isEmpty())
    <p class="text-stone-500">{{ $pageContent['empty_state'] ?? 'La carte sera bientôt en ligne.' }}</p>
@else
    <div class="space-y-14">
        @foreach($categories as $category)
            <section class="scroll-mt-28" id="cat-{{ $category->id }}" aria-labelledby="heading-cat-{{ $category->id }}">
                <div class="flex flex-wrap items-end justify-between gap-4 border-b border-stone-200 pb-4">
                    <h2 id="heading-cat-{{ $category->id }}" class="text-xl font-semibold text-stone-900 md:text-2xl">
                        {{ $category->name }}
                    </h2>
                    @if(filled($category->menu_pdf_url))
                        <a href="{{ $category->menu_pdf_url }}" target="_blank" rel="noopener noreferrer" class="text-sm font-medium text-[var(--bistro-color-primary)] hover:underline">
                            {{ $pageContent['pdf_link_label'] ?? 'Télécharger le PDF' }}
                        </a>
                    @endif
                </div>
                @if(filled($category->description))
                    <p class="mt-3 text-sm text-stone-600">{{ $category->description }}</p>
                @endif

                @if($category->menuItems->isEmpty())
                    <p class="mt-6 text-sm text-stone-500">{{ $pageContent['empty_category_items'] ?? 'Aucun plat dans cette catégorie pour le moment.' }}</p>
                @else
                    <ul class="mt-8 space-y-6">
                        @foreach($category->menuItems as $item)
                            <li class="bistro-card p-5 md:p-6">
                                <div class="flex flex-wrap items-baseline justify-between gap-2">
                                    <h3 class="text-base font-semibold text-stone-900 md:text-lg">{{ $item->name }}</h3>
                                    @if($item->price !== null)
                                        <span class="text-sm font-semibold text-[var(--bistro-color-primary)] tabular-nums">
                                            {{ number_format((float) $item->price, 2, ',', ' ') }}&nbsp;{{ $item->currency ?? 'EUR' }}
                                        </span>
                                    @endif
                                </div>
                                @if(filled($item->description))
                                    <p class="mt-2 text-sm leading-relaxed text-stone-600">{{ $item->description }}</p>
                                @endif
                                @php
                                    $opts = \App\Support\AllergenCatalog::options();
                                    $allergens = $item->allergens ?? [];
                                    $flags = $item->dietary_flags ?? [];
                                @endphp
                                @if(! empty($allergens))
                                    <p class="mt-3 text-xs text-stone-500">
                                        <span class="font-semibold text-stone-700">{{ $pageContent['allergens_label'] ?? 'Allergènes :' }}</span>
                                        {{ collect($allergens)->map(fn ($k) => $opts[$k] ?? $k)->implode(' · ') }}
                                    </p>
                                @endif
                                @if(! empty($flags))
                                    <p class="mt-2 text-xs font-medium text-[var(--bistro-color-primary)]">
                                        {{ collect($flags)->map(fn ($f) => match ($f) {
                                            'vegetarian' => 'Végétarien',
                                            'vegan' => 'Vegan',
                                            'gluten_free' => 'Sans gluten',
                                            'spicy' => 'Épicé',
                                            default => $f,
                                        })->implode(' · ') }}
                                    </p>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        @endforeach
    </div>
@endif
