@extends('layouts.bistro', ['cssVars' => $cssVars, 'bistroFontStylesheet' => $bistroFontStylesheet ?? null, 'metaDescription' => $metaDescription ?? null])

@section('title', $restaurant->name.' — Actualités')

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
        <div class="bistro-container">
            <h1 class="bistro-title text-3xl text-stone-900 md:text-4xl">Actualités</h1>
            <ul class="mt-8 space-y-6">
                @forelse($posts as $post)
                    <li class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                        <p class="text-xs text-stone-500">{{ $post->published_at?->translatedFormat('d F Y') }}</p>
                        <h2 class="mt-1 text-xl font-semibold text-stone-900">
                            <a href="{{ route('site.posts.show', ['slug' => $post->slug]) }}" class="hover:underline">{{ $post->title }}</a>
                        </h2>
                        @if(filled($post->excerpt))
                            <p class="mt-2 text-sm text-stone-600">{{ $post->excerpt }}</p>
                        @endif
                    </li>
                @empty
                    <li class="text-sm text-stone-500">Aucune publication pour le moment.</li>
                @endforelse
            </ul>
            <div class="mt-8">
                {{ $posts->links() }}
            </div>
        </div>
    </main>
@endsection
