@extends('layouts.bistro', ['cssVars' => $cssVars, 'bistroFontStylesheet' => $bistroFontStylesheet ?? null])

@section('title', $restaurant->name)

@section('content')
    <main id="contenu-principal">
        @include('site.themes.lumen-atelier.home', [
            'content' => $content,
            'restaurant' => $restaurant,
        ])
    </main>
@endsection

@section('footer')
    {{-- Les thèmes gèrent leur propre footer visuel dans leurs sections --}}
@endsection
