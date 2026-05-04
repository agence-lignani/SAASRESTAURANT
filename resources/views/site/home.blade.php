@extends('layouts.bistro', ['cssVars' => $cssVars, 'bistroFontStylesheet' => $bistroFontStylesheet ?? null])

@section('title', $restaurant->name)

@section('content')
    @php
        $theme = request()->query('theme', env('SITE_THEME_PRESET', 'lumen-atelier'));
        $theme = in_array($theme, ['bakery-noir', 'lumen-atelier'], true) ? $theme : 'lumen-atelier';
    @endphp

    <main id="contenu-principal">
        @include('site.themes.'.$theme.'.home', [
            'content' => $content,
            'restaurant' => $restaurant,
        ])
    </main>
@endsection

@section('footer')
    {{-- Les thèmes gèrent leur propre footer visuel dans leurs sections --}}
@endsection
