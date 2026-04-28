@extends('layouts.bistro', ['cssVars' => $cssVars, 'bistroFontStylesheet' => $bistroFontStylesheet ?? null, 'metaDescription' => $metaDescription ?? null])

@section('title', $restaurant->name.' — '.$post->title)

@section('content')
    @include('site.partials.top-contact-bar', ['restaurant' => $restaurant])

    <header class="bistro-main-header sticky top-0 z-50 border-b border-stone-200/80 bg-white/75 backdrop-blur-md supports-[backdrop-filter]:bg-white/60">
        <div class="bistro-container flex flex-wrap items-center justify-between gap-4 py-4">
            <a href="{{ route('site.home') }}" class="bistro-title text-lg text-stone-900 md:text-xl">
                {{ $restaurant->name }}
            </a>
            @include('site.partials.main-nav')
        </div>
    </header>

    <main id="contenu-principal" class="pb-16 pt-8 md:pb-24 md:pt-12">
        <div class="bistro-container max-w-3xl">
            <p class="text-xs text-stone-500">
                <a href="{{ route('site.posts.index') }}" class="font-medium text-[var(--bistro-color-primary)] hover:underline">← Actualités</a>
                · {{ $post->published_at?->translatedFormat('d F Y') }}
            </p>
            <h1 class="bistro-title mt-3 text-3xl text-stone-900 md:text-4xl">{{ $post->title }}</h1>
            <article class="prose prose-stone mt-6 max-w-none text-sm leading-relaxed text-stone-700">
                {!! nl2br(e($post->body)) !!}
            </article>
        </div>
    </main>
@endsection
