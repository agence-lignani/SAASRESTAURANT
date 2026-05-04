<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ $metaDescription ?? $restaurant->tagline ?? $restaurant->name }}">
    <title>@yield('title', $restaurant->name ?? 'Restaurant')</title>
    @php($jsonLd = $jsonLd ?? \App\Support\Seo\RestaurantJsonLd::forRestaurant($restaurant))
    <script type="application/ld+json">@json($jsonLd)</script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Martian+Mono:wght@400;500;700;800&family=Oi&display=swap">
    @vite(['resources/css/bistro.css', 'resources/js/app.js'])
    <style>
        :root {
            @foreach($cssVars ?? [] as $k => $v)
            {{ $k }}: {{ $v }};
            @endforeach
        }
    </style>
</head>
<body class="bistro-body min-h-screen antialiased">
    @yield('content')
    @include('site.partials.chat-widget')
    @hasSection('footer')
        @yield('footer')
    @else
        <footer class="bistro-footer border-t border-stone-200/90 bg-white py-10" role="contentinfo">
            <div class="bistro-container text-center text-sm text-stone-500">
                <p class="font-semibold text-stone-800">{{ $restaurant->name ?? '' }}</p>
                <p class="mt-2 flex flex-wrap justify-center gap-x-3 gap-y-1">
                    <a href="{{ route('site.legal', ['slug' => 'mentions-legales']) }}" class="hover:underline">Mentions légales</a>
                    <span aria-hidden="true">·</span>
                    <a href="{{ route('site.legal', ['slug' => 'politique-de-confidentialite']) }}" class="hover:underline">Confidentialité</a>
                    <span aria-hidden="true">·</span>
                    <a href="{{ route('site.posts.index') }}" class="hover:underline">Actualités</a>
                </p>
                <p class="mt-2">&copy; {{ date('Y') }}
                    — <a href="{{ url('/admin') }}" class="font-medium text-[var(--bistro-color-primary)] hover:underline">Admin</a>
                </p>
            </div>
        </footer>
    @endif
</body>
</html>
