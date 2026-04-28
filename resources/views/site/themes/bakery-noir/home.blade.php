@php
    $hero = $content['hero'] ?? [];
    $menus = $content['menus'] ?? [];
    $items = $menus['items'] ?? [];
    $gallery = $content['gallery_highlights'] ?? [];
    $galleryItems = $gallery['items'] ?? [];
    $about = $content['manifesto'] ?? [];
    $featured = $content['espaces'] ?? [];
    $spotlight = $content['spotlight'] ?? [];
    $practical = $content['practical'] ?? [];

    $heroImage = $hero['image_url'] ?? 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=1600&q=80&auto=format&fit=crop';
    $heroTitle = $hero['title'] ?? ($restaurant->name ?? 'Bakery');
    $heroSubtitle = $hero['subtitle'] ?? 'Sweet treats perfect eats';
    $heroButtons = $hero['cta_buttons'] ?? [];
@endphp

<div class="theme-noir">
    <header class="theme-noir-header">
        <div class="bistro-container flex items-center justify-between">
            <a href="{{ route('site.home') }}" class="theme-noir-brand">{{ $restaurant->name }}</a>
            <nav class="theme-noir-nav">
                <a href="{{ route('site.home') }}">Home</a>
                <a href="{{ route('site.carte') }}">Shop</a>
                <a href="{{ route('site.contact') }}">Help</a>
            </nav>
        </div>
    </header>

    <section class="theme-noir-hero" style="background-image: url('{{ $heroImage }}');">
        <div class="theme-noir-hero-overlay"></div>
        <div class="bistro-container relative z-10 grid gap-10 py-16 md:grid-cols-12 md:py-24">
            <div class="md:col-span-8">
                <p class="text-xs uppercase tracking-[0.22em] text-white/70">{{ $hero['eyebrow'] ?? 'Bakery Edition' }}</p>
                <h1 class="mt-3 max-w-2xl font-[family-name:var(--bistro-font-heading)] text-5xl leading-[0.95] text-white md:text-7xl">
                    {{ $heroTitle }}
                </h1>
                <p class="mt-4 max-w-lg text-sm leading-relaxed text-white/82">{{ is_string($heroSubtitle) ? $heroSubtitle : '' }}</p>
                <div class="mt-7 flex flex-wrap gap-3">
                    @foreach($heroButtons as $index => $button)
                        @if(filled($button['href'] ?? null))
                            <a href="{{ $button['href'] }}" class="{{ $index === 0 ? 'theme-noir-btn-primary' : 'theme-noir-btn-secondary' }}">
                                {{ $button['label'] ?? 'Learn more' }}
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
            <aside class="md:col-span-4">
                <div class="border border-white/25 bg-black/45 p-4 backdrop-blur-sm">
                    <p class="text-[0.62rem] uppercase tracking-[0.2em] text-white/70">Quick Basket</p>
                    <p class="mt-3 text-sm text-white/90">3 items selected</p>
                    <p class="mt-2 text-2xl font-semibold text-white">$24.00</p>
                    <a href="{{ route('site.carte') }}" class="theme-noir-btn-primary mt-4 inline-flex w-full justify-center">Checkout</a>
                </div>
            </aside>
        </div>
    </section>

    <section class="bg-[#181b23]">
        <div class="bistro-container py-10">
            <div class="flex items-end justify-between gap-4">
                <h2 class="font-[family-name:var(--bistro-font-heading)] text-3xl text-white md:text-4xl">{{ $menus['heading'] ?? 'Top Products' }}</h2>
                <a href="{{ route('site.carte') }}" class="text-xs uppercase tracking-[0.18em] text-white/70">View all</a>
            </div>
            <div class="mt-6 flex gap-4 overflow-x-auto pb-2">
                @foreach($items as $item)
                    @if(filled($item['title'] ?? null))
                        <article class="min-w-[250px] flex-1 border border-stone-700 bg-[#11131a] p-3">
                            <div class="theme-noir-product-image">
                                @if(filled($item['image_url'] ?? null))
                                    <img src="{{ $item['image_url'] }}" alt="{{ $item['image_alt'] ?? $item['title'] }}" loading="lazy">
                                @endif
                            </div>
                            <div class="mt-3 flex items-start justify-between gap-2">
                                <div>
                                    <p class="text-sm font-semibold text-white">{{ $item['price'] ?? '$10' }}</p>
                                    <p class="mt-1 text-sm text-white/85">{{ $item['title'] }}</p>
                                </div>
                                <span class="theme-noir-add">Add</span>
                            </div>
                        </article>
                    @endif
                @endforeach
            </div>
        </div>
    </section>

    <section class="theme-noir-promo">
        <div class="bistro-container py-10">
            <div class="flex flex-col items-center justify-between gap-4 md:flex-row">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-[var(--bistro-color-secondary)]">{{ $spotlight['heading'] ?? 'Special Offer' }}</p>
                    <h3 class="mt-1 font-[family-name:var(--bistro-font-heading)] text-3xl text-[color:var(--bistro-color-text)]">20% Off Your First Order</h3>
                </div>
                @foreach(($spotlight['buttons'] ?? []) as $button)
                    @if(filled($button['href'] ?? null))
                        <a href="{{ $button['href'] }}" class="theme-noir-btn-primary">{{ $button['label'] ?? 'Learn More' }}</a>
                    @endif
                @endforeach
            </div>
        </div>
    </section>

    <section id="about-band" class="theme-noir-about-band">
        <div class="bistro-container py-12 md:py-16">
            <div class="grid gap-6 md:grid-cols-12 md:items-center">
                <div class="md:col-span-7">
                    <p class="text-xs uppercase tracking-[0.2em] text-white/75">{{ $about['eyebrow'] ?? 'About us' }}</p>
                    <h2 class="mt-2 font-[family-name:var(--bistro-font-heading)] text-4xl text-white md:text-5xl">{{ $about['heading'] ?? 'About us' }}</h2>
                    <p class="mt-4 max-w-2xl text-sm leading-relaxed text-white/85">
                        {{ strip_tags((string) (($about['paragraphs'][0] ?? '') ?: ($about['body'] ?? ''))) }}
                    </p>
                </div>
                <div class="grid grid-cols-2 gap-3 md:col-span-5">
                    @foreach(array_slice($galleryItems, 0, 2) as $photo)
                        @if(filled($photo['image_url'] ?? null))
                            <figure class="theme-noir-tile"><img src="{{ $photo['image_url'] }}" alt="{{ $photo['image_alt'] ?? '' }}" loading="lazy"></figure>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="theme-noir-section bg-[#f8f8f8]">
        <div class="bistro-container py-14">
            <h2 class="theme-noir-title-center">{{ $gallery['heading'] ?? 'Explore More' }}</h2>
            <div class="mt-6 grid grid-cols-2 gap-3 md:grid-cols-4">
                @foreach($galleryItems as $photo)
                    @if(filled($photo['image_url'] ?? null))
                        <figure class="theme-noir-tile">
                            <img src="{{ $photo['image_url'] }}" alt="{{ $photo['image_alt'] ?? '' }}" loading="lazy">
                        </figure>
                    @endif
                @endforeach
            </div>
        </div>
    </section>
</div>
