@extends('layouts.bistro', ['cssVars' => $cssVars, 'bistroFontStylesheet' => $bistroFontStylesheet ?? null])

@section('title', $restaurant->name.' — '.($pageContent['title'] ?? 'Galerie'))

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
            @php($sectionOrder = $pageContent['section_order'] ?? \App\Support\SiteContent\PageSectionCatalog::defaultOrder('galerie'))
            @foreach($sectionOrder as $sectionKey)
                @if($sectionKey === 'header')
                    <section>
                        <h1 class="bistro-title text-3xl text-stone-900 md:text-4xl">{{ $pageContent['title'] ?? 'Galerie' }}</h1>
                        <div class="prose prose-stone mt-3 max-w-2xl text-sm text-stone-600 prose-p:my-1">
                            {!! \App\Support\SiteContent\SiteContentHtml::paragraph($pageContent['intro'] ?? 'Quelques images de notre maison. Cliquez sur une photo pour l’agrandir.') !!}
                        </div>
                    </section>
                @endif

                @if($sectionKey === 'gallery')
                    <section class="mt-10">
                        @if($galleryMedia->isEmpty())
                            <p class="text-stone-500">{{ $pageContent['empty_state'] ?? 'Les photos seront bientôt disponibles.' }}</p>
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
        </div>
    </main>
@endsection
