@php
    $hero = $content['hero'] ?? [];
    $menus = $content['menus'] ?? [];
    $items = $menus['items'] ?? [];
    $gallery = $content['gallery_highlights'] ?? [];
    $galleryItems = $gallery['items'] ?? [];
    $about = $content['manifesto'] ?? [];
    $faq = $content['faq'] ?? [];
    $spotlight = $content['spotlight'] ?? [];
@endphp

<div class="theme-lumen">
    <header class="theme-lumen-header">
        <div class="bistro-container flex items-center justify-between py-6">
            <a href="{{ route('site.home') }}" class="theme-lumen-brand">{{ $restaurant->name }}</a>
            <nav class="theme-lumen-nav">
                <a href="{{ route('site.home') }}">Journal</a>
                <a href="{{ route('site.carte') }}">Collection</a>
                <a href="{{ route('site.contact') }}">Studio</a>
                <a href="{{ route('site.reservation') }}" class="theme-lumen-cta">Book</a>
            </nav>
        </div>
    </header>

    <section class="theme-lumen-hero">
        <div class="bistro-container py-14 md:py-20">
            <p class="theme-lumen-kicker">{{ $hero['eyebrow'] ?? 'Lumen Journal' }}</p>
            <h1 class="theme-lumen-heading-xl mt-4 max-w-5xl md:text-8xl">
                {{ $hero['title'] ?? $restaurant->name }}
            </h1>
            <div class="mt-8 grid gap-6 md:grid-cols-12 md:items-end">
                <p class="theme-lumen-copy md:col-span-5 text-base">{{ is_string($hero['subtitle'] ?? null) ? $hero['subtitle'] : '' }}</p>
                <div class="md:col-span-7">
                    <figure class="theme-lumen-hero-media">
                        @if(filled($hero['image_url'] ?? null))
                            <img src="{{ $hero['image_url'] }}" alt="{{ $hero['image_alt'] ?? '' }}" loading="eager">
                        @endif
                    </figure>
                </div>
            </div>
        </div>
    </section>

    <section class="theme-lumen-block">
        <div class="bistro-container py-14 md:py-20">
            <div class="grid gap-12 md:grid-cols-12">
                <aside class="md:col-span-3">
                    <p class="theme-lumen-kicker">Chapter 01</p>
                    <h2 class="theme-lumen-heading-lg mt-3">{{ $about['heading'] ?? 'Manifesto' }}</h2>
                </aside>
                <article class="theme-lumen-copy md:col-span-9 space-y-5 text-lg">
                    @foreach(($about['paragraphs'] ?? []) as $paragraph)
                        <div>{!! \App\Support\SiteContent\SiteContentHtml::paragraph($paragraph) !!}</div>
                    @endforeach
                </article>
            </div>
        </div>
    </section>

    <section class="theme-lumen-block theme-lumen-cream">
        <div class="bistro-container py-14 md:py-20">
            <p class="theme-lumen-kicker">Chapter 02</p>
            <h2 class="theme-lumen-title mt-3">{{ $menus['heading'] ?? 'Selection' }}</h2>
            <div class="mt-8 divide-y divide-[#ddcec0]">
                @foreach($items as $item)
                    @if(filled($item['title'] ?? null))
                        <article class="grid gap-5 py-5 md:grid-cols-12 md:items-center">
                            <div class="md:col-span-3">
                                <span class="theme-lumen-price">{{ $item['price'] ?? '' }}</span>
                            </div>
                            <div class="md:col-span-6">
                                <h3 class="theme-lumen-heading-md">{{ $item['title'] }}</h3>
                                <p class="theme-lumen-copy mt-2 text-sm">{{ $item['description'] ?? '' }}</p>
                            </div>
                            <div class="md:col-span-3">
                                <a href="{{ route('site.carte') }}" class="theme-lumen-btn-secondary w-full justify-center">View</a>
                            </div>
                        </article>
                    @endif
                @endforeach
            </div>
        </div>
    </section>

    <section class="theme-lumen-block">
        <div class="bistro-container py-14 md:py-20">
            <p class="theme-lumen-kicker">Chapter 03</p>
            <h2 class="theme-lumen-title mt-3">{{ $gallery['heading'] ?? 'Visual Notes' }}</h2>
            <div class="mt-8 grid gap-3 md:grid-cols-12 md:auto-rows-[210px]">
                @foreach($galleryItems as $index => $photo)
                    @if(filled($photo['image_url'] ?? null))
                        <figure class="theme-lumen-tile {{ $index === 0 ? 'md:col-span-7 md:row-span-2' : ($index === 1 ? 'md:col-span-5' : 'md:col-span-4') }}">
                            <img src="{{ $photo['image_url'] }}" alt="{{ $photo['image_alt'] ?? '' }}" loading="lazy">
                        </figure>
                    @endif
                @endforeach
            </div>
        </div>
    </section>

    <section class="theme-lumen-promo">
        <div class="bistro-container py-14 md:py-20">
            <div class="grid gap-8 md:grid-cols-12 md:items-end">
                <h2 class="theme-lumen-title text-white md:col-span-8">{{ $spotlight['heading'] ?? 'Réservez votre table' }}</h2>
                <div class="md:col-span-4 md:text-right">
                    @foreach(($spotlight['buttons'] ?? []) as $button)
                        @if(filled($button['href'] ?? null))
                            <a href="{{ $button['href'] }}" class="theme-lumen-btn-primary">{{ $button['label'] ?? 'Réserver' }}</a>
                        @endif
                    @endforeach
                </div>
            </div>
            <p class="mt-4 max-w-2xl text-white/82">{{ strip_tags((string) ($spotlight['body'] ?? '')) }}</p>
        </div>
    </section>

    <section class="theme-lumen-block">
        <div class="bistro-container py-14 md:py-20">
            <p class="theme-lumen-kicker">Chapter 04</p>
            <h2 class="theme-lumen-title mt-3">{{ $faq['heading'] ?? 'FAQ' }}</h2>
            <div class="mx-auto mt-8 max-w-5xl">
                @foreach(($faq['items'] ?? []) as $item)
                    @if(filled($item['question'] ?? null))
                        <article class="grid gap-4 border-b border-[#e8ddd2] py-5 md:grid-cols-12">
                            <h3 class="theme-lumen-heading-md md:col-span-5">{{ $item['question'] }}</h3>
                            <div class="theme-lumen-copy text-sm md:col-span-7">{!! \App\Support\SiteContent\SiteContentHtml::safe($item['answer'] ?? '') !!}</div>
                        </article>
                    @endif
                @endforeach
            </div>
        </div>
    </section>
</div>
