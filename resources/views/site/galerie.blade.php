@extends('layouts.bistro', ['cssVars' => $cssVars, 'bistroFontStylesheet' => $bistroFontStylesheet ?? null])

@section('title', $restaurant->name.' — '.($pageContent['title'] ?? 'Galerie'))

@section('content')
    <div class="theme-lumen min-h-screen">
        @include('site.partials.lumen-header', ['restaurant' => $restaurant])

        <main id="contenu-principal" class="theme-lumen-page">
            @php($sectionOrder = $pageContent['section_order'] ?? \App\Support\SiteContent\PageSectionCatalog::defaultOrder('galerie'))
            @foreach($sectionOrder as $sectionKey)
                @if($sectionKey === 'header')
                    @include('site.partials.lumen-page-hero', [
                        'eyebrow' => 'Galerie',
                        'title' => $pageContent['title'] ?? 'Galerie',
                        'intro' => $pageContent['intro'] ?? 'Quelques images de notre maison. Cliquez sur une photo pour l’agrandir.',
                    ])
                @endif

                @if($sectionKey === 'gallery')
                    <section class="bistro-container mt-10 md:mt-14">
                        @if($galleryMedia->isEmpty())
                            <p class="theme-lumen-empty">{{ $pageContent['empty_state'] ?? 'Les photos seront bientôt disponibles.' }}</p>
                        @else
                            <div data-bistro-gallery>
                                @include('site.partials.gallery-grid', ['galleryMedia' => $galleryMedia, 'restaurant' => $restaurant])
                            </div>

                            <dialog id="bistro-gallery-lightbox" class="bistro-gallery-lightbox" aria-modal="true" aria-labelledby="bistro-gallery-lightbox-title">
                                <div class="bistro-gallery-lightbox__shell">
                                    <p id="bistro-gallery-lightbox-title" class="sr-only">{{ $pageContent['lightbox_title'] ?? 'Visionneuse de la galerie' }}</p>
                                    <button type="button" class="bistro-gallery-lightbox__close" aria-label="Fermer la visionneuse">
                                        <span aria-hidden="true" class="text-2xl leading-none">×</span>
                                    </button>
                                    <button type="button" class="bistro-gallery-lightbox__prev" aria-label="Photo précédente" hidden>
                                        <span aria-hidden="true" class="text-3xl font-light leading-none">‹</span>
                                    </button>
                                    <button type="button" class="bistro-gallery-lightbox__next" aria-label="Photo suivante" hidden>
                                        <span aria-hidden="true" class="text-3xl font-light leading-none">›</span>
                                    </button>
                                    <div class="bistro-gallery-lightbox__stage">
                                        <img class="bistro-gallery-lightbox__img" src="" alt="" decoding="async" />
                                    </div>
                                    <p class="bistro-gallery-lightbox__caption hidden max-w-3xl px-4 text-center text-sm text-white/95"></p>
                                </div>
                            </dialog>
                        @endif
                    </section>
                @endif
            @endforeach
        </main>
    </div>
@endsection
