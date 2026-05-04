@extends('layouts.bistro', ['cssVars' => $cssVars, 'bistroFontStylesheet' => $bistroFontStylesheet ?? null])

@section('title', $restaurant->name.' — '.($pageContent['title'] ?? 'Carte'))

@section('content')
    @include('site.partials.top-contact-bar', ['restaurant' => $restaurant])
    @include('site.partials.lumen-header', ['restaurant' => $restaurant])

    <main id="contenu-principal" class="theme-lumen-page">
        @php($sectionOrder = $pageContent['section_order'] ?? \App\Support\SiteContent\PageSectionCatalog::defaultOrder('carte'))
        <div class="bistro-container">
            @foreach($sectionOrder as $sectionKey)
                @if($sectionKey === 'header')
                    @include('site.partials.lumen-page-hero', [
                        'eyebrow' => 'Carte',
                        'title' => $pageContent['title'] ?? 'Notre carte',
                        'intro' => $pageContent['intro'] ?? 'Prix et disponibilités donnés à titre indicatif ; merci de vous adresser à la salle pour toute allergie ou variante du jour.',
                    ])
                @endif

                @if($sectionKey === 'menu_list')
                    <section class="theme-lumen-content-card mt-10 md:mt-12">
                        @include('site.partials.carte-menu-list', ['categories' => $categories, 'pageContent' => $pageContent])
                    </section>
                @endif
            @endforeach
        </div>
    </main>
@endsection
