<section class="theme-lumen-page-hero" aria-labelledby="{{ $headingId ?? 'page-heading' }}">
    <div class="bistro-container">
        @isset($kicker)
            <p class="theme-lumen-kicker">{{ $kicker }}</p>
        @endisset
        <h1 id="{{ $headingId ?? 'page-heading' }}" class="theme-lumen-title mt-3">
            {{ $title }}
        </h1>
        @if(filled($intro ?? null))
            <div class="theme-lumen-copy mt-5 max-w-3xl text-lg">
                {!! \App\Support\SiteContent\SiteContentHtml::paragraph($intro) !!}
            </div>
        @endif
    </div>
</section>
