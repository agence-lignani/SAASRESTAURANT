@extends('layouts.bistro', ['cssVars' => $cssVars, 'bistroFontStylesheet' => $bistroFontStylesheet ?? null])

@section('title', $restaurant->name)

@section('content')
    @php
        $homeOrder = $content['section_order'] ?? \App\Support\SiteContent\PageSectionCatalog::defaultOrder('home');
        if (! is_array($homeOrder) || $homeOrder === []) {
            $homeOrder = \App\Support\SiteContent\PageSectionCatalog::defaultOrder('home');
        }

        $hero = $content['hero'] ?? [];
        $manifesto = $content['manifesto'] ?? [];
        $menus = $content['menus'] ?? [];
        $gallery = $content['gallery_highlights'] ?? [];
        $spotlight = $content['spotlight'] ?? [];
        $faq = $content['faq'] ?? [];
        $reviews = $content['reviews_widget'] ?? [];
        $practical = $content['practical'] ?? [];

        $heroImage = $hero['image_url'] ?? 'https://images.unsplash.com/photo-1541745537411-b8046dc6d66c?w=2000&q=80&auto=format&fit=crop';
        $heroTitle = filled($hero['title'] ?? null) ? $hero['title'] : 'Zero scuse, solo pizza !';
        $heroButtons = $hero['cta_buttons'] ?? [];
        $phraseText = filled($hero['subtitle'] ?? null)
            ? $hero['subtitle']
            : 'Palazzo vi accoglie, dove la passione per la pizza incontra la tradizione napoletana in ogni morso.';

        $discoverTitle = filled($menus['heading'] ?? null)
            ? $menus['heading']
            : 'Try the Magic of Fire-Kissed Flavors';
        $discoverIntro = filled($menus['intro'] ?? null)
            ? $menus['intro']
            : 'Introducing the Leek & Chorizo Pizza — A Neapolitan-style sourdough masterpiece, proofed for 72 hours to craft the perfect crust.';
        $discoverImage = $menus['items'][0]['image_url'] ?? ($manifesto['image_url'] ?? null);

        $openingLines = $practical['opening_lines'] ?? [];
        if (! is_array($openingLines) || $openingLines === []) {
            $openingLines = ['Lun — Jeu · 11:30 — 22:00', 'Ven — Sam · 11:30 — 23:00', 'Dim · 12:00 — 21:00'];
        }

        $pizzaItems = is_array($menus['items'] ?? null) ? array_values($menus['items']) : [];
        if ($pizzaItems === []) {
            $pizzaItems = [
                ['title' => 'Margherita', 'price' => '12 €', 'description' => 'Tomate San Marzano, mozzarella fior di latte, basilic frais'],
                ['title' => 'Marinara', 'price' => '10 €', 'description' => 'Tomate, ail, origan, huile d’olive (sans fromage)'],
                ['title' => 'Diavola', 'price' => '14 €', 'description' => 'Tomate, mozzarella, salami piquant, huile d’olive'],
                ['title' => 'Quattro Formaggi', 'price' => '15 €', 'description' => 'Mozzarella, gorgonzola, fontina, parmigiano'],
            ];
        }

        $valueCards = [
            ['title' => 'Prodotti', 'text' => 'Ingrédients frais et sélectionnés pour garder l’authenticité napolitaine.'],
            ['title' => 'Forno', 'text' => 'Four à bois pour une cuisson vive et croustillante.'],
            ['title' => 'Tradizione', 'text' => 'Recettes transmises de génération en génération.'],
            ['title' => 'Passione', 'text' => 'Chaque pizza est montée avec précision et caractère.'],
        ];

        $carousel = [];
        if (is_array($gallery['items'] ?? null)) {
            foreach ($gallery['items'] as $item) {
                if (filled($item['image_url'] ?? null)) {
                    $carousel[] = $item;
                }
            }
        }
        if ($carousel === []) {
            $carousel = [
                ['image_url' => $discoverImage],
                ['image_url' => $manifesto['image_url'] ?? null],
                ['image_url' => $heroImage],
                ['image_url' => $spotlight['image_url'] ?? null],
            ];
            $carousel = array_values(array_filter($carousel, fn (array $item): bool => filled($item['image_url'] ?? null)));
        }

        $heroPos = array_search('hero', $homeOrder, true);
        $manifestoPos = array_search('manifesto', $homeOrder, true);
        $manifestoFirst = $manifestoPos !== false && ($heroPos === false || $manifestoPos < $heroPos);
    @endphp

    <div class="palazzo-root">
        <header class="palazzo-nav">
            <div class="palazzo-nav__inner">
                <a href="{{ route('site.home') }}" class="palazzo-brand">
                    PALAZZO!
                </a>
                <nav class="palazzo-nav__links">
                    <a href="{{ route('site.carte') }}">Menu</a>
                    <a href="#about-band">About</a>
                    <a href="#contact">Contact</a>
                </nav>
            </div>
        </header>

        <main id="contenu-principal" class="palazzo-main">
            @if($manifestoFirst)
                <section id="about-band" class="palazzo-phrase">
                    <div class="palazzo-shell">
                        <h2 class="palazzo-title-md">{{ $manifesto['heading'] ?? 'Our Story' }}</h2>
                        <p class="palazzo-title-lg">{{ $phraseText }}</p>
                    </div>
                </section>
            @endif

            <section class="palazzo-hero">
                <img src="{{ $heroImage }}" alt="{{ $hero['image_alt'] ?? '' }}" class="palazzo-hero__img" />
                <div class="palazzo-hero__overlay"></div>
                <div class="palazzo-hero__content">
                    <h1 class="palazzo-display">{{ $heroTitle }}</h1>
                    @if(! empty($heroButtons))
                        <div class="palazzo-hero__cta">
                            @foreach($heroButtons as $button)
                                @if(filled($button['href'] ?? null))
                                    <a href="{{ $button['href'] }}" class="palazzo-btn">
                                        {{ $button['label'] ?? 'La carta' }}
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </section>

            @if(! $manifestoFirst)
                <section id="about-band" class="palazzo-phrase">
                    <div class="palazzo-shell">
                        <h2 class="palazzo-title-md">{{ $manifesto['heading'] ?? 'Our Story' }}</h2>
                        <p class="palazzo-title-lg">{{ $phraseText }}</p>
                    </div>
                </section>
            @endif

            <section class="palazzo-discover">
                <div class="palazzo-discover__media">
                    @if(filled($discoverImage))
                        <img src="{{ $discoverImage }}" alt="" />
                    @endif
                </div>
                <div class="palazzo-discover__text">
                    <div class="palazzo-shell">
                        <p class="palazzo-chip">LEEK &amp; CHORIZO</p>
                        <h2 class="palazzo-title-md">{{ $discoverTitle }}</h2>
                        <p class="palazzo-copy">{{ $discoverIntro }}</p>
                    </div>
                </div>
            </section>

            <section class="palazzo-photo-break">
                @if(filled($spotlight['image_url'] ?? null))
                    <img src="{{ $spotlight['image_url'] }}" alt="" />
                @elseif(filled($manifesto['image_url'] ?? null))
                    <img src="{{ $manifesto['image_url'] }}" alt="" />
                @endif
            </section>

            <section class="palazzo-hours">
                <div class="palazzo-shell palazzo-center">
                    <p class="palazzo-hours__kicker">{{ $practical['heading'] ?? 'Venir au restaurant' }}</p>
                    <h2 class="palazzo-title-md">Our Opening Hours</h2>
                    <div class="palazzo-hours__list">
                        @foreach($openingLines as $line)
                            <p>{{ $line }}</p>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="palazzo-menu">
                <div class="palazzo-shell">
                    <h2 class="palazzo-title-md palazzo-center">Our Pizzas</h2>
                    <div class="palazzo-menu__card">
                        <div class="palazzo-menu__grid">
                            @foreach($pizzaItems as $item)
                                @if(filled($item['title'] ?? null))
                                    <article class="palazzo-menu__item">
                                        <header>
                                            <h3>{{ $item['title'] }}</h3>
                                            <span>{{ $item['price'] ?? '' }}</span>
                                        </header>
                                        <p>{{ $item['description'] ?? '' }}</p>
                                    </article>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            <section class="palazzo-values">
                <div class="palazzo-shell">
                    <h2 class="palazzo-title-md palazzo-center">Heavy on the good stuff, easy on the sweet stuff</h2>
                    <div class="palazzo-values__grid">
                        @foreach($valueCards as $value)
                            <article class="palazzo-value">
                                <div class="palazzo-value__icon"></div>
                                <h3>{{ $value['title'] }}</h3>
                                <p>{{ $value['text'] }}</p>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="palazzo-carousel">
                <div class="palazzo-carousel__track">
                    @foreach($carousel as $card)
                        @if(filled($card['image_url'] ?? null))
                            <figure class="palazzo-carousel__item">
                                <img src="{{ $card['image_url'] }}" alt="{{ $card['image_alt'] ?? '' }}" loading="lazy" />
                            </figure>
                        @endif
                    @endforeach
                </div>
            </section>
        </main>
    </div>
@endsection

@section('footer')
    <footer id="contact" class="palazzo-footer">
        <div class="palazzo-shell palazzo-footer__grid">
            <div>
                <p class="palazzo-footer__label">Site map</p>
                <div class="palazzo-footer__lines">
                    <p><a href="{{ route('site.carte') }}">Menu</a></p>
                    <p><a href="#about-band">About</a></p>
                    <p><a href="#contact">Contact</a></p>
                </div>
            </div>
            <div>
                <p class="palazzo-footer__label">Find us</p>
                <div class="palazzo-footer__lines">
                    @if(filled($restaurant->address_line1))
                        <p>{{ $restaurant->address_line1 }}</p>
                    @endif
                    @if(filled($restaurant->contact_phone))
                        <p>{{ $restaurant->contact_phone }}</p>
                    @endif
                    @if(filled($restaurant->contact_email))
                        <p>{{ $restaurant->contact_email }}</p>
                    @endif
                </div>
            </div>
            <div>
                <p class="palazzo-footer__label">Hours</p>
                <div class="palazzo-footer__lines">
                    @foreach($content['practical']['opening_lines'] ?? [] as $line)
                        <p>{{ $line }}</p>
                    @endforeach
                </div>
            </div>
        </div>
    </footer>
@endsection
