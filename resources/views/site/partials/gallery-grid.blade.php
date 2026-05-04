{{-- Mise en page selon le nombre de photos : colonne unique, grille, mosaïque (colonnes CSS). Clic = zoom (lightbox). --}}
@php
    $count = $galleryMedia->count();
@endphp

@if($count === 1)
    @php
        $m = $galleryMedia->first();
    @endphp
    <ul class="mt-10 list-none p-0">
        <li class="theme-lumen-card mx-auto max-w-4xl overflow-hidden p-0">
            <figure class="m-0">
                @include('site.partials.gallery-zoom-thumb', [
                    'm' => $m,
                    'restaurant' => $restaurant,
                    'imgClass' => 'max-h-[min(70vh,720px)] w-full object-cover object-center',
                    'loading' => 'eager',
                    'fetchpriority' => 'high',
                ])
                @if(filled($m->caption))
                    <figcaption class="border-t border-stone-100 px-5 py-4 text-sm text-stone-600">
                        {{ $m->caption }}
                    </figcaption>
                @endif
            </figure>
        </li>
    </ul>
@elseif($count === 2)
    <ul class="mt-10 grid list-none grid-cols-1 gap-5 p-0 sm:grid-cols-2 lg:gap-7">
        @foreach($galleryMedia as $m)
            <li class="theme-lumen-card overflow-hidden p-0">
                <figure class="m-0">
                    @include('site.partials.gallery-zoom-thumb', [
                        'm' => $m,
                        'restaurant' => $restaurant,
                        'imgClass' => 'aspect-[4/3] w-full object-cover',
                        'loading' => $loop->first ? 'eager' : 'lazy',
                        'fetchpriority' => $loop->first ? 'high' : null,
                    ])
                    @if(filled($m->caption))
                        <figcaption class="border-t border-stone-100 px-4 py-3 text-sm text-stone-600">
                            {{ $m->caption }}
                        </figcaption>
                    @endif
                </figure>
            </li>
        @endforeach
    </ul>
@elseif($count === 3)
    {{--
        Mosaïque responsive : 2×2 (grande vignette à gauche, deux à droite) dès la grille 2 colonnes.
        Très petits écrans : 2 colonnes — ligne 1 pleine largeur, ligne 2 deux vignettes côte à côte.
        Grands écrans : colonne gauche plus large (bento).
    --}}
    <ul
        class="mt-10 grid list-none grid-cols-2 gap-3 p-0 sm:grid-rows-2 sm:gap-6 sm:min-h-[min(72vh,540px)] lg:min-h-[min(68vh,620px)] lg:grid-cols-[1.15fr_1fr] xl:grid-cols-3 xl:grid-rows-1 xl:gap-8 xl:min-h-0"
    >
        @foreach($galleryMedia as $m)
            <li
                @class([
                    'theme-lumen-card min-h-0 overflow-hidden p-0 col-span-2 sm:col-span-1 sm:row-span-2 xl:col-span-1 xl:row-span-1',
                    'col-span-1' => ! $loop->first,
                ])
            >
                <figure @class(['m-0 flex h-full flex-col', 'min-h-[200px] sm:min-h-0' => $loop->first])>
                    @include('site.partials.gallery-zoom-thumb', [
                        'm' => $m,
                        'restaurant' => $restaurant,
                        'imgClass' => $loop->first
                            ? 'aspect-[16/10] max-h-[260px] min-h-0 w-full flex-1 object-cover sm:max-h-none sm:min-h-[240px] sm:aspect-auto xl:aspect-[4/3] xl:max-h-[min(42vh,360px)] xl:min-h-[220px]'
                            : 'aspect-[4/3] min-h-0 w-full flex-1 object-cover sm:aspect-auto sm:min-h-[min(28vw,220px)] lg:min-h-[200px] xl:aspect-[4/3] xl:max-h-[min(42vh,360px)] xl:min-h-[220px]',
                        'buttonClass' => 'flex h-full min-h-0 w-full flex-1 flex-col',
                        'loading' => $loop->first ? 'eager' : 'lazy',
                        'fetchpriority' => $loop->first ? 'high' : null,
                    ])
                    @if(filled($m->caption))
                        <figcaption class="shrink-0 border-t border-stone-100 px-4 py-3 text-sm text-stone-600">
                            {{ $m->caption }}
                        </figcaption>
                    @endif
                </figure>
            </li>
        @endforeach
    </ul>
@elseif($count === 4)
    <ul class="mt-10 grid list-none grid-cols-1 gap-5 p-0 sm:grid-cols-2 lg:gap-7">
        @foreach($galleryMedia as $m)
            <li class="theme-lumen-card overflow-hidden p-0">
                <figure class="m-0">
                    @include('site.partials.gallery-zoom-thumb', [
                        'm' => $m,
                        'restaurant' => $restaurant,
                        'imgClass' => 'aspect-[4/3] w-full object-cover',
                        'loading' => 'lazy',
                    ])
                    @if(filled($m->caption))
                        <figcaption class="border-t border-stone-100 px-4 py-3 text-sm text-stone-600">
                            {{ $m->caption }}
                        </figcaption>
                    @endif
                </figure>
            </li>
        @endforeach
    </ul>
@else
    {{-- 5+ : mosaïque fluide (colonnes), hauteurs variées pour un rendu naturel --}}
    <ul class="mt-10 columns-1 gap-5 p-0 sm:columns-2 sm:gap-6 lg:columns-3 lg:gap-7 [&>li]:break-inside-avoid">
        @foreach($galleryMedia as $m)
            @php
                $mod = $loop->iteration % 5;
                $aspectClass = match ($mod) {
                    1, 4 => 'aspect-[4/3]',
                    2 => 'aspect-square',
                    3 => 'aspect-[3/4]',
                    default => 'aspect-[5/4]',
                };
            @endphp
            <li class="theme-lumen-card mb-5 overflow-hidden p-0 last:mb-0 sm:mb-6">
                <figure class="m-0">
                    @include('site.partials.gallery-zoom-thumb', [
                        'm' => $m,
                        'restaurant' => $restaurant,
                        'imgClass' => $aspectClass.' w-full object-cover',
                        'loading' => 'lazy',
                    ])
                    @if(filled($m->caption))
                        <figcaption class="border-t border-stone-100 px-4 py-3 text-sm text-stone-600">
                            {{ $m->caption }}
                        </figcaption>
                    @endif
                </figure>
            </li>
        @endforeach
    </ul>
@endif
