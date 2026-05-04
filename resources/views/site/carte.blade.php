@extends('layouts.bistro', ['cssVars' => $cssVars, 'bistroFontStylesheet' => $bistroFontStylesheet ?? null])

@section('title', $restaurant->name.' — '.($pageContent['title'] ?? 'Carte'))

@section('content')
    @include('site.partials.top-contact-bar', ['restaurant' => $restaurant])

    <header class="bistro-inner-header">
        <div class="bistro-inner-header__inner">
            <a href="{{ route('site.home') }}" class="bistro-inner-brand">
                {{ $restaurant->name }}
            </a>
            @include('site.partials.main-nav')
        </div>
    </header>

    <section class="bistro-page-hero" aria-hidden="true">
        <div class="palazzo-shell palazzo-center" style="position:relative;">
            <p class="bistro-page-hero__eyebrow">{{ $restaurant->name }}</p>
            <h1 class="bistro-page-hero__title">{{ $pageContent['title'] ?? 'Notre carte' }}</h1>
            @if(filled($pageContent['intro'] ?? null))
                <p class="bistro-page-hero__intro">
                    {!! strip_tags(\App\Support\SiteContent\SiteContentHtml::paragraph($pageContent['intro'])) !!}
                </p>
            @endif
        </div>
    </section>

    <main id="contenu-principal" class="bistro-page-content" aria-labelledby="carte-main-heading">
        <h1 id="carte-main-heading" class="sr-only">{{ $pageContent['title'] ?? 'Notre carte' }}</h1>
        <div class="palazzo-shell">
            @php($sectionOrder = $pageContent['section_order'] ?? \App\Support\SiteContent\PageSectionCatalog::defaultOrder('carte'))
            @foreach($sectionOrder as $sectionKey)
                @if($sectionKey === 'menu_list')
                    @include('site.partials.carte-menu-list', ['categories' => $categories, 'pageContent' => $pageContent])
                @endif
            @endforeach
        </div>
    </main>
@endsection
