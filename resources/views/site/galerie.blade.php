@extends('layouts.bistro', ['cssVars' => $cssVars, 'bistroFontStylesheet' => $bistroFontStylesheet ?? null])

@section('title', $restaurant->name.' — '.($pageContent['title'] ?? 'Galerie'))

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
            <h1 class="bistro-page-hero__title">{{ $pageContent['title'] ?? 'Galerie' }}</h1>
            @if(filled($pageContent['intro'] ?? null))
                <p class="bistro-page-hero__intro">
                    {!! strip_tags(\App\Support\SiteContent\SiteContentHtml::paragraph($pageContent['intro'])) !!}
                </p>
            @else
                <p class="bistro-page-hero__intro">
                    Quelques images de notre maison. Cliquez sur une photo pour l'agrandir.
                </p>
            @endif
        </div>
    </section>

    <main id="contenu-principal" class="bistro-page-content">
        <div class="palazzo-shell">
            @php($sectionOrder = $pageContent['section_order'] ?? \App\Support\SiteContent\PageSectionCatalog::defaultOrder('galerie'))
            @foreach($sectionOrder as $sectionKey)
                @if($sectionKey === 'gallery')
                    @if($galleryMedia->isEmpty())
                        <p style="color:color-mix(in srgb, #105a41 60%, #fff);font-size:0.95rem;">
                            {{ $pageContent['empty_state'] ?? 'Les photos seront bientôt disponibles.' }}
                        </p>
                    @else
                        <div data-bistro-gallery>
                            @include('site.partials.gallery-grid', ['galleryMedia' => $galleryMedia, 'restaurant' => $restaurant])
                        </div>

                        <dialog id="bistro-gallery-lightbox" class="bistro-gallery-lightbox" aria-modal="true" aria-labelledby="bistro-gallery-lightbox-title">
                            <div class="bistro-gallery-lightbox__shell">
                                <p id="bistro-gallery-lightbox-title" class="sr-only">{{ $pageContent['lightbox_title'] ?? 'Visionneuse de la galerie' }}</p>
                                <button type="button" class="bistro-gallery-lightbox__close" aria-label="Fermer la visionneuse">
                                    <span aria-hidden="true">×</span>
                                </button>
                                <button type="button" class="bistro-gallery-lightbox__prev" aria-label="Photo précédente" hidden>
                                    <span aria-hidden="true">‹</span>
                                </button>
                                <button type="button" class="bistro-gallery-lightbox__next" aria-label="Photo suivante" hidden>
                                    <span aria-hidden="true">›</span>
                                </button>
                                <div class="bistro-gallery-lightbox__stage">
                                    <img class="bistro-gallery-lightbox__img" src="" alt="" decoding="async" />
                                </div>
                                <p class="bistro-gallery-lightbox__caption hidden"></p>
                            </div>
                        </dialog>
                    @endif
                @endif
            @endforeach
        </div>
    </main>
@endsection
