@if($categories->isEmpty())
    <p class="text-stone-500">{{ $pageContent['empty_state'] ?? 'La carte sera bientôt en ligne.' }}</p>
@else
    <div class="space-y-0">
        @foreach($categories as $category)
            <section class="bistro-menu-section scroll-mt-28" id="cat-{{ $category->id }}" aria-labelledby="heading-cat-{{ $category->id }}">
                <div class="bistro-menu-section__header">
                    <h2 id="heading-cat-{{ $category->id }}" class="bistro-menu-section__title">
                        {{ $category->name }}
                    </h2>
                    @if(filled($category->menu_pdf_url))
                        <a href="{{ $category->menu_pdf_url }}" target="_blank" rel="noopener noreferrer" class="bistro-menu-pdf-link">
                            {{ $pageContent['pdf_link_label'] ?? '↓ Télécharger le PDF' }}
                        </a>
                    @endif
                </div>

                @if(filled($category->description))
                    <p class="bistro-menu-section__desc">{{ $category->description }}</p>
                @endif

                @if($category->menuItems->isEmpty())
                    <p class="mt-4 text-sm" style="color: color-mix(in srgb, #105a41 55%, #fff);">
                        {{ $pageContent['empty_category_items'] ?? 'Aucun plat dans cette catégorie pour le moment.' }}
                    </p>
                @else
                    <ul class="mt-0 space-y-4 list-none p-0">
                        @foreach($category->menuItems as $item)
                            <li class="bistro-menu-item">
                                <div class="bistro-menu-item__header">
                                    <h3 class="bistro-menu-item__name">{{ $item->name }}</h3>
                                    @if($item->price !== null)
                                        <span class="bistro-menu-item__price">
                                            {{ number_format((float) $item->price, 2, ',', '\u{202F}') }}&nbsp;€
                                        </span>
                                    @endif
                                </div>

                                @if(filled($item->description))
                                    <p class="bistro-menu-item__desc">{{ $item->description }}</p>
                                @endif

                                @php
                                    $opts = \App\Support\AllergenCatalog::options();
                                    $allergens = $item->allergens ?? [];
                                    $flags = $item->dietary_flags ?? [];
                                @endphp

                                @if(! empty($allergens))
                                    <p class="bistro-menu-item__allergens">
                                        <strong>{{ $pageContent['allergens_label'] ?? 'Allergènes :' }}</strong>
                                        {{ collect($allergens)->map(fn ($k) => $opts[$k] ?? $k)->implode(' · ') }}
                                    </p>
                                @endif

                                @if(! empty($flags))
                                    <p class="bistro-menu-item__flags">
                                        {{ collect($flags)->map(fn ($f) => match ($f) {
                                            'vegetarian' => '🌿 Végétarien',
                                            'vegan' => '🌱 Vegan',
                                            'gluten_free' => '✦ Sans gluten',
                                            'spicy' => '🌶 Épicé',
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
