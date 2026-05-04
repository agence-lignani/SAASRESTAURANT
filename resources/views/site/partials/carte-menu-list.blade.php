@if($categories->isEmpty())
    <p class="theme-lumen-readable">{{ $pageContent['empty_state'] ?? 'La carte sera bientôt en ligne.' }}</p>
@else
    <div class="space-y-10 md:space-y-12">
        @foreach($categories as $category)
            <section class="theme-lumen-menu-card scroll-mt-28" id="cat-{{ $category->id }}" aria-labelledby="heading-cat-{{ $category->id }}">
                <div class="flex flex-wrap items-start justify-between gap-4 border-b border-[#d8c6b4] pb-5">
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
                    <p class="theme-lumen-readable mt-4 max-w-3xl">{{ $category->description }}</p>
                @endif

                @if($category->menuItems->isEmpty())
                    <p class="theme-lumen-readable mt-6">{{ $pageContent['empty_category_items'] ?? 'Aucun plat dans cette catégorie pour le moment.' }}</p>
                @else
                    <ul class="mt-7 space-y-4">
                        @foreach($category->menuItems as $item)
                            <li class="theme-lumen-menu-item">
                                <div class="flex flex-wrap items-start justify-between gap-4">
                                    <h3 class="theme-lumen-heading-md max-w-2xl text-2xl">{{ $item->name }}</h3>
                                    @if($item->price !== null)
                                        <span class="theme-lumen-price-badge tabular-nums">
                                            {{ number_format((float) $item->price, 2, ',', ' ') }}&nbsp;{{ $item->currency ?? 'EUR' }}
                                        </span>
                                    @endif
                                </div>
                                @if(filled($item->description))
                                    <p class="theme-lumen-readable mt-3 max-w-3xl">{{ $item->description }}</p>
                                @endif
                                @php
                                    $opts = \App\Support\AllergenCatalog::options();
                                    $allergens = $item->allergens ?? [];
                                    $flags = $item->dietary_flags ?? [];
                                @endphp
                                @if(! empty($allergens))
                                    <p class="mt-4 text-sm text-[#5e5046]">
                                        <span class="font-bold text-[#2a241f]">{{ $pageContent['allergens_label'] ?? 'Allergènes :' }}</span>
                                        {{ collect($allergens)->map(fn ($k) => $opts[$k] ?? $k)->implode(' · ') }}
                                    </p>
                                @endif
                                @if(! empty($flags))
                                    <p class="mt-3 inline-flex rounded-full bg-[#f1e3d5] px-3 py-1 text-sm font-bold text-[#5a2e12]">
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
