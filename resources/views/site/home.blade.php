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
        $values = $content['values'] ?? [];
        $faq = $content['faq'] ?? [];
        $reviews = $content['reviews_widget'] ?? [];
        $practical = $content['practical'] ?? [];

        $heroImage = $hero['image_url'] ?? 'https://images.unsplash.com/photo-1541745537411-b8046dc6d66c?w=2000&q=80&auto=format&fit=crop';
        $heroTitle = filled($hero['title'] ?? null) ? $hero['title'] : 'Zero scuse, solo pizza !';
        $heroImageAlt = filled($hero['image_alt'] ?? null) ? $hero['image_alt'] : 'Image de présentation du restaurant';
        $heroButtons = $hero['cta_buttons'] ?? [];
        $brandLabel = filled($hero['brand_label'] ?? null) ? $hero['brand_label'] : 'PALAZZO!';
        $navMenuLabel = filled($hero['nav_menu_label'] ?? null) ? $hero['nav_menu_label'] : 'Menu';
        $navAboutLabel = filled($hero['nav_about_label'] ?? null) ? $hero['nav_about_label'] : 'About';
        $navContactLabel = filled($hero['nav_contact_label'] ?? null) ? $hero['nav_contact_label'] : 'Contact';

        $manifestoHeading = filled($manifesto['heading'] ?? null) ? $manifesto['heading'] : 'Our Story';
        $manifestoParagraphs = is_array($manifesto['paragraphs'] ?? null) ? $manifesto['paragraphs'] : [];
        $phraseSource = $manifestoParagraphs[0] ?? ($hero['subtitle'] ?? null);
        $phraseText = filled($phraseSource)
            ? trim(strip_tags((string) $phraseSource))
            : 'Palazzo vi accoglie, dove la passione per la pizza incontra la tradizione napoletana in ogni morso.';

        $discoverTitle = filled($menus['heading'] ?? null)
            ? $menus['heading']
            : 'Try the Magic of Fire-Kissed Flavors';
        $discoverIntro = filled($menus['intro'] ?? null)
            ? $menus['intro']
            : 'Introducing the Leek & Chorizo Pizza — A Neapolitan-style sourdough masterpiece, proofed for 72 hours to craft the perfect crust.';
        $discoverChip = filled($menus['chip_label'] ?? null) ? $menus['chip_label'] : 'LEEK & CHORIZO';
        $menuSectionTitle = filled($menus['section_title'] ?? null) ? $menus['section_title'] : 'Our Pizzas';
        $galleryTitle = filled($gallery['heading'] ?? null) ? $gallery['heading'] : 'Moments at Palazzo';
        $galleryIntro = filled($gallery['intro'] ?? null)
            ? $gallery['intro']
            : 'Une galerie élégante pour saisir l’ambiance de la maison, entre salle, cuisine et service.';
        $discoverImage = $menus['items'][0]['image_url'] ?? ($manifesto['image_url'] ?? null);
        $discoverImageAlt = filled($menus['items'][0]['image_alt'] ?? null)
            ? $menus['items'][0]['image_alt']
            : (filled($manifesto['image_alt'] ?? null) ? $manifesto['image_alt'] : 'Image de plat signature');

        $openingLines = $practical['opening_lines'] ?? [];
        if (! is_array($openingLines) || $openingLines === []) {
            $openingLines = ['Lun — Jeu · 11:30 — 22:00', 'Ven — Sam · 11:30 — 23:00', 'Dim · 12:00 — 21:00'];
        }
        $openingTitle = filled($practical['opening_title'] ?? null) ? $practical['opening_title'] : 'Our Opening Hours';
        $footerMapLabel = filled($practical['footer_map_label'] ?? null) ? $practical['footer_map_label'] : 'Site map';
        $footerFindLabel = filled($practical['footer_find_label'] ?? null) ? $practical['footer_find_label'] : 'Find us';
        $footerHoursLabel = filled($practical['footer_hours_label'] ?? null) ? $practical['footer_hours_label'] : 'Hours';
        $footerMetaLines = is_array($practical['footer_meta_lines'] ?? null) ? array_values(array_filter($practical['footer_meta_lines'])) : [];
        if ($footerMetaLines === []) {
            $footerMetaLines = [
                'A template by figma.to.website designed by Alexis Oulès.',
                'Follow us on x.com',
                'Say hello: sales@figweb.com',
                '2025 - divRIOTS',
            ];
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

        $valueHeading = filled($values['heading'] ?? null)
            ? $values['heading']
            : 'Heavy on the good stuff, easy on the sweet stuff';
        $valueCards = [];
        if (is_array($values['items'] ?? null)) {
            foreach ($values['items'] as $item) {
                if (filled($item['title'] ?? null) || filled($item['text'] ?? null)) {
                    $valueCards[] = [
                        'title' => $item['title'] ?? '',
                        'text' => $item['text'] ?? '',
                    ];
                }
            }
        }
        if ($valueCards === []) {
            $valueCards = [
                ['title' => 'Prodotti', 'text' => 'Ingrédients frais et sélectionnés pour garder l’authenticité napolitaine.'],
                ['title' => 'Forno', 'text' => 'Four à bois pour une cuisson vive et croustillante.'],
                ['title' => 'Tradizione', 'text' => 'Recettes transmises de génération en génération.'],
                ['title' => 'Passione', 'text' => 'Chaque pizza est montée avec précision et caractère.'],
            ];
        }

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

    <div class="palazzo-root palazzo-home">
        <header class="palazzo-nav">
            <div class="palazzo-nav__inner">
                <a href="{{ route('site.home') }}" class="palazzo-brand">
                    {{ $brandLabel }}
                </a>
                <nav class="palazzo-nav__links">
                    <a href="{{ route('site.carte') }}">{{ $navMenuLabel }}</a>
                    <a href="#about-band">{{ $navAboutLabel }}</a>
                    <a href="#contact">{{ $navContactLabel }}</a>
                </nav>
            </div>
        </header>

        <main id="contenu-principal" class="palazzo-main">
            @if($manifestoFirst)
                <section id="about-band" class="palazzo-phrase">
                    <div class="palazzo-shell">
                        <h2 class="palazzo-title-md">{{ $manifestoHeading }}</h2>
                        <p class="palazzo-title-lg">{{ $phraseText }}</p>
                    </div>
                </section>
            @endif

            <section class="palazzo-hero">
                <img src="{{ $heroImage }}" alt="{{ $heroImageAlt }}" class="palazzo-hero__img" />
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
                        <h2 class="palazzo-title-md">{{ $manifestoHeading }}</h2>
                        <p class="palazzo-title-lg">{{ $phraseText }}</p>
                    </div>
                </section>
            @endif

            <section class="palazzo-discover">
                <div class="palazzo-discover__media">
                    @if(filled($discoverImage))
                        <img src="{{ $discoverImage }}" alt="{{ $discoverImageAlt }}" />
                    @endif
                </div>
                <div class="palazzo-discover__text">
                    <div class="palazzo-shell">
                        <p class="palazzo-chip">{{ $discoverChip }}</p>
                        <h2 class="palazzo-title-md">{{ $discoverTitle }}</h2>
                        <p class="palazzo-copy">{{ $discoverIntro }}</p>
                    </div>
                </div>
            </section>

            <section class="palazzo-photo-break">
                @if(filled($spotlight['image_url'] ?? null))
                    <img src="{{ $spotlight['image_url'] }}" alt="{{ $spotlight['image_alt'] ?? 'Photo du restaurant' }}" />
                @elseif(filled($manifesto['image_url'] ?? null))
                    <img src="{{ $manifesto['image_url'] }}" alt="{{ $manifesto['image_alt'] ?? 'Photo du restaurant' }}" />
                @endif
            </section>

            <section class="palazzo-hours">
                <div class="palazzo-shell palazzo-center">
                    <p class="palazzo-hours__kicker">{{ $practical['heading'] ?? 'Venir au restaurant' }}</p>
                    <h2 class="palazzo-title-md">{{ $openingTitle }}</h2>
                    <div class="palazzo-hours__list">
                        @foreach($openingLines as $line)
                            <p>{{ $line }}</p>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="palazzo-menu">
                <div class="palazzo-shell">
                    <h2 class="palazzo-title-md palazzo-center">{{ $menuSectionTitle }}</h2>
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
                    <h2 class="palazzo-title-md palazzo-center">{{ $valueHeading }}</h2>
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

            <section class="palazzo-gallery">
                <div class="palazzo-shell">
                    <div class="palazzo-gallery__head palazzo-center">
                        <h2 class="palazzo-title-md">{{ $galleryTitle }}</h2>
                        <p class="palazzo-copy">{{ $galleryIntro }}</p>
                    </div>
                    <div class="palazzo-gallery__grid">
                        @foreach($carousel as $index => $card)
                            @if(filled($card['image_url'] ?? null))
                                <figure class="palazzo-gallery__item {{ $index % 5 === 0 ? 'is-wide' : '' }} {{ $index % 7 === 0 ? 'is-tall' : '' }}">
                                    <img src="{{ $card['image_url'] }}" alt="{{ $card['image_alt'] ?? 'Photo de la galerie du restaurant' }}" loading="lazy" />
                                </figure>
                            @endif
                        @endforeach
                    </div>
                </div>
            </section>
        </main>
    </div>
@endsection

@section('footer')
    <footer id="contact" class="palazzo-footer">
        <div class="palazzo-shell">
            <div class="palazzo-footer__brand palazzo-center">
                <p class="palazzo-footer__kicker">{{ $footerMapLabel }}</p>
                <h2 class="palazzo-title-md">{{ $brandLabel }}</h2>
            </div>
            <div class="palazzo-footer__grid">
                <div class="palazzo-footer__card">
                    <p class="palazzo-footer__label">{{ $footerMapLabel }}</p>
                    <div class="palazzo-footer__lines">
                        <p><a href="{{ route('site.carte') }}">{{ $navMenuLabel }}</a></p>
                        <p><a href="#about-band">{{ $navAboutLabel }}</a></p>
                        <p><a href="#contact">{{ $navContactLabel }}</a></p>
                    </div>
                </div>
                <div class="palazzo-footer__card">
                    <p class="palazzo-footer__label">{{ $footerFindLabel }}</p>
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
                <div class="palazzo-footer__card">
                    <p class="palazzo-footer__label">{{ $footerHoursLabel }}</p>
                    <div class="palazzo-footer__lines">
                        @foreach($openingLines as $line)
                            <p>{{ $line }}</p>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="palazzo-footer__meta palazzo-center">
                @foreach($footerMetaLines as $metaLine)
                    <p>{{ $metaLine }}</p>
                @endforeach
            </div>
        </div>
    </footer>
@endsection
