@extends('layouts.bistro', ['cssVars' => $cssVars, 'bistroFontStylesheet' => $bistroFontStylesheet ?? null])

@section('title', $restaurant->name)

@section('content')
    @php
        $homeOrder = $content['section_order'] ?? \App\Support\SiteContent\PageSectionCatalog::defaultOrder('home');
        if (! is_array($homeOrder) || $homeOrder === []) {
            $homeOrder = \App\Support\SiteContent\PageSectionCatalog::defaultOrder('home');
        }

        $sectionPartials = [
            'hero' => ['view' => 'site.blocks.hero', 'type' => 'block'],
            'manifesto' => ['view' => 'site.blocks.manifesto', 'type' => 'block'],
            'menus' => ['view' => 'site.blocks.menus', 'type' => 'block'],
            'reviews_widget' => ['view' => 'site.partials.home-section-reviews-widget', 'type' => 'content'],
            'espaces' => ['view' => 'site.blocks.espaces-links', 'type' => 'block'],
            'faq' => ['view' => 'site.blocks.faq', 'type' => 'block'],
            'carte_narrative' => ['view' => 'site.blocks.carte-narrative', 'type' => 'block'],
            'gallery_highlights' => ['view' => 'site.blocks.gallery-highlights', 'type' => 'block'],
            'spotlight' => ['view' => 'site.blocks.spotlight', 'type' => 'block'],
            'practical' => ['view' => 'site.partials.home-section-practical', 'type' => 'content'],
        ];
    @endphp

    <div class="bg-[#f1e8dd] text-[#105a41]">
        <header class="sticky top-0 z-50 border-b border-[#ee371b]/30 bg-[#f1e8dd]/95 backdrop-blur">
            <div class="mx-auto flex h-12 w-full max-w-[1420px] items-center justify-between px-3">
                <a href="{{ route('site.home') }}" class="font-[Oi] text-lg leading-none text-[#ee371b]">
                    PALAZZO!
                </a>
                <nav class="flex items-center gap-6 text-xs font-medium uppercase tracking-[0.16em] text-[#ee371b]">
                    <a href="{{ route('site.carte') }}">Menu</a>
                    <a href="#about-band">About</a>
                    <a href="#contact">Contact</a>
                </nav>
            </div>
        </header>

        <main id="contenu-principal">
            @foreach($homeOrder as $sectionKey)
                @php($entry = $sectionPartials[$sectionKey] ?? null)
                @continue(! is_array($entry))

                @if($entry['type'] === 'block')
                    @include($entry['view'], [
                        'data' => $content[$sectionKey] ?? [],
                        'content' => $content,
                        'restaurant' => $restaurant,
                    ])
                @else
                    @include($entry['view'], [
                        'content' => $content,
                        'restaurant' => $restaurant,
                    ])
                @endif
            @endforeach
        </main>
    </div>
@endsection

@section('footer')
    <footer id="contact" class="bg-[#ee371b] px-5 py-16 text-[#f1e8dd]">
        <div class="mx-auto grid w-full max-w-[1100px] gap-8 md:grid-cols-3">
            <div class="text-center md:text-left">
                <p class="font-semibold uppercase tracking-[0.15em]">Site map</p>
                <div class="mt-3 space-y-1 text-sm">
                    <p><a href="{{ route('site.carte') }}">Menu</a></p>
                    <p><a href="#about-band">About</a></p>
                    <p><a href="#contact">Contact</a></p>
                </div>
            </div>
            <div class="text-center">
                <p class="font-semibold uppercase tracking-[0.15em]">Find us</p>
                <div class="mt-3 space-y-1 text-sm">
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
            <div class="text-center md:text-right">
                <p class="font-semibold uppercase tracking-[0.15em]">Hours</p>
                <div class="mt-3 space-y-1 text-sm">
                    @foreach($content['practical']['opening_lines'] ?? [] as $line)
                        <p>{{ $line }}</p>
                    @endforeach
                </div>
            </div>
        </div>
    </footer>
@endsection
