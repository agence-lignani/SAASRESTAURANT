@include('site.partials.top-contact-bar', ['restaurant' => $restaurant])

<header class="theme-lumen-header sticky top-0 z-50">
    <div class="bistro-container flex flex-wrap items-center justify-between gap-4 py-4 md:py-5">
        <a href="{{ route('site.home') }}" class="theme-lumen-brand">{{ $restaurant->name }}</a>
        @include('site.partials.main-nav')
    </div>
</header>
