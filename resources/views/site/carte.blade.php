@extends('layouts.bistro', ['cssVars' => $cssVars, 'bistroFontStylesheet' => $bistroFontStylesheet ?? null])

@section('title', $restaurant->name.' — '.($pageContent['title'] ?? 'Carte'))

@section('content')
    @include('site.partials.top-contact-bar', ['restaurant' => $restaurant])

    <header class="bistro-main-header sticky top-0 z-50 border-b border-stone-200/80 bg-white/75 backdrop-blur-md supports-[backdrop-filter]:bg-white/60">
        <div class="bistro-container flex flex-wrap items-center justify-between gap-4 py-4">
            <a href="{{ route('site.home') }}" class="bistro-title text-lg text-stone-900 md:text-xl">
                {{ $restaurant->name }}
            </a>
            @include('site.partials.main-nav')
        </div>
    </header>

    <main id="contenu-principal" class="pb-16 pt-8 md:pb-24 md:pt-12">
        <div class="bistro-container">
            @php($sectionOrder = $pageContent['section_order'] ?? \App\Support\SiteContent\PageSectionCatalog::defaultOrder('carte'))
            @foreach($sectionOrder as $sectionKey)
                @if($sectionKey === 'header')
                    <section>
                        <h1 class="bistro-title text-3xl text-stone-900 md:text-4xl">{{ $pageContent['title'] ?? 'Notre carte' }}</h1>
                        <div class="prose prose-stone mt-3 max-w-2xl text-sm leading-relaxed text-stone-600 prose-p:my-1">
                            {!! \App\Support\SiteContent\SiteContentHtml::paragraph($pageContent['intro'] ?? 'Prix et disponibilités donnés à titre indicatif ; merci de vous adresser à la salle pour toute allergie ou variante du jour.') !!}
                        </div>
                    </section>
                @endif

                @if($sectionKey === 'menu_list')
                    <section class="mt-10">
                        @include('site.partials.carte-menu-list', ['categories' => $categories, 'pageContent' => $pageContent])
                    </section>
                @endif
            @endforeach
        </div>
    </main>
@endsection
