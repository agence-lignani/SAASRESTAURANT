@if($categories->isEmpty())
    <p class="text-stone-500">{{ $pageContent['empty_state'] ?? 'La carte sera bientôt en ligne.' }}</p>
@else
    <div class="space-y-14">
        @foreach($categories as $category)
            <section class="lumen-card scroll-mt-28 p-6 md:p-8" id="cat-{{ $category->id }}" aria-labelledby="heading-cat-{{ $category->id }}">
                <div class="flex flex-wrap items-end justify-between gap-4 border-b border-[#eadfd4] pb-4">
                    <h2 id="heading-cat-{{ $category->id }}" class="theme-lumen-heading-md">
                        {{ $category->name }}
                    </h2>
                    @if(filled($category->menu_pdf_url))
                        <a href="{{ $category->menu_pdf_url }}" target="_blank" rel="noopener noreferrer" class="lumen-text-link text-sm">
                            {{ $pageContent['pdf_link_label'] ?? 'Télécharger le PDF' }}
                        </a>
                    @endif
                </div>
                @if(filled($category->description))
                    <p class="theme-lumen-copy mt-3 text-sm">{{ $category->description }}</p>
                @endif

                @if($category->menuItems->isEmpty())
                    <p class="mt-6 text-sm text-stone-500">{{ $pageContent['empty_category_items'] ?? 'Aucun plat dans cette catégorie pour le moment.' }}</p>
                @else
                    <ul class="mt-8 divide-y divide-[#eadfd4]">
                        @foreach($category->menuItems as $item)
                            <li class="py-5 first:pt-0 last:pb-0">
                                <div class="flex flex-wrap items-baseline justify-between gap-2">
                                    <h3 class="theme-lumen-heading-md text-xl">{{ $item->name }}</h3>
                                    @if($item->price !== null)
                                        <span class="theme-lumen-price tabular-nums">
                                            {{ number_format((float) $item->price, 2, ',', ' ') }}&nbsp;{{ $item->currency ?? 'EUR' }}
                                        </span>
                                    @endif
                                </div>
                                @if(filled($item->description))
                                    <p class="theme-lumen-copy mt-2 text-sm">{{ $item->description }}</p>
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
