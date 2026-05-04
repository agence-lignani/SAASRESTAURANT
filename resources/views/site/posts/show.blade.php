@extends('layouts.bistro', ['cssVars' => $cssVars, 'bistroFontStylesheet' => $bistroFontStylesheet ?? null, 'metaDescription' => $metaDescription ?? null])

@section('title', $restaurant->name.' — '.$post->title)

@section('content')
    @include('site.partials.top-contact-bar', ['restaurant' => $restaurant])
    @include('site.partials.lumen-header', ['restaurant' => $restaurant])

    <main id="contenu-principal" class="theme-lumen-page">
        <div class="bistro-container max-w-4xl">
            <a href="{{ route('site.posts.index') }}" class="theme-lumen-backlink">← Actualités</a>
            @include('site.partials.lumen-page-hero', [
                'kicker' => $post->published_at?->translatedFormat('d F Y') ?? 'Actualité',
                'title' => $post->title,
                'intro' => null,
            ])
            <article class="theme-lumen-panel theme-lumen-rich-text">
                {!! nl2br(e($post->body)) !!}
            </article>
        </div>
    </main>
@endsection
