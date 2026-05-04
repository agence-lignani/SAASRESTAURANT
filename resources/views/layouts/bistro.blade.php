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
    <a href="#contenu-principal" class="bistro-skip-link">Aller au contenu principal</a>

    @yield('content')

    @include('site.partials.chat-widget')

    @hasSection('footer')
        @yield('footer')
    @else
        <footer class="bistro-footer" role="contentinfo">
            <div class="bistro-container" style="text-align:center;">
                <span class="bistro-footer-brand">{{ $restaurant->name ?? '' }}</span>
                <nav class="bistro-footer-links" aria-label="Liens du pied de page">
                    <a href="{{ route('site.legal', ['slug' => 'mentions-legales']) }}">Mentions légales</a>
                    <span class="separator" aria-hidden="true">·</span>
                    <a href="{{ route('site.legal', ['slug' => 'politique-de-confidentialite']) }}">Confidentialité</a>
                    <span class="separator" aria-hidden="true">·</span>
                    <a href="{{ route('site.posts.index') }}">Actualités</a>
                </nav>
                <p class="bistro-footer-meta">
                    &copy; {{ date('Y') }} {{ $restaurant->name ?? '' }}
                    &nbsp;—&nbsp;<a href="{{ url('/admin') }}">Espace admin</a>
                </p>
            </div>
        </footer>
    @endif
</body>
</html>
