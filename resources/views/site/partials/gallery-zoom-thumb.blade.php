@php
    /** @var \App\Models\GalleryMedia $m */
    /** @var \App\Models\Restaurant $restaurant */
    $alt = $m->alt_text ?: ($m->caption ?: 'Photo '.$restaurant->name);
    $ariaLabel = filled($m->caption)
        ? 'Agrandir la photo : '.\Illuminate\Support\Str::limit($m->caption, 72)
        : 'Agrandir la photo';
@endphp
<button
    type="button"
    class="gallery-zoom-trigger group relative block w-full cursor-zoom-in border-0 bg-transparent p-0 text-left focus-visible:outline focus-visible:ring-2 focus-visible:ring-[var(--bistro-color-primary)] focus-visible:ring-offset-2 {{ $buttonClass ?? '' }}"
    data-gallery-src="{{ $m->url }}"
    data-gallery-alt="{{ $alt }}"
    data-gallery-caption="{{ $m->caption ?? '' }}"
    aria-label="{{ $ariaLabel }}"
>
    <img
        src="{{ $m->url }}"
        alt="{{ $alt }}"
        class="{{ $imgClass }} pointer-events-none select-none object-cover transition-transform duration-200 ease-out group-hover:scale-[1.02] motion-reduce:transition-none motion-reduce:group-hover:scale-100"
        loading="{{ $loading ?? 'lazy' }}"
        decoding="async"
        @if(! empty($fetchpriority))
            fetchpriority="{{ $fetchpriority }}"
        @endif
    />
</button>
