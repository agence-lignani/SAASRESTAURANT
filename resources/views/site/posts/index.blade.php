@extends('layouts.bistro', ['cssVars' => $cssVars, 'bistroFontStylesheet' => $bistroFontStylesheet ?? null, 'metaDescription' => $metaDescription ?? null])

@section('title', $restaurant->name.' — Actualités')

@section('content')
    @include('site.partials.lumen-header', ['restaurant' => $restaurant])

    <main id="contenu-principal" class="theme-lumen min-h-screen">
        @include('site.partials.lumen-page-hero', [
            'eyebrow' => 'Journal',
            'title' => 'Actualités',
            'intro' => 'Notes de saison, nouveautés et temps forts de la maison.',
        ])

        <section class="theme-lumen-block">
            <div class="bistro-container">
            <ul class="space-y-5">
                @forelse($posts as $post)
                    <li class="theme-lumen-card p-6 md:p-7">
                        <p class="theme-lumen-kicker">{{ $post->published_at?->translatedFormat('d F Y') }}</p>
                        <h2 class="theme-lumen-heading-md mt-2">
                            <a href="{{ route('site.posts.show', ['slug' => $post->slug]) }}" class="hover:underline">{{ $post->title }}</a>
                        </h2>
                        @if(filled($post->excerpt))
                            <p class="theme-lumen-copy mt-3 max-w-3xl text-sm">{{ $post->excerpt }}</p>
                        @endif
                    </li>
                @empty
                    <li class="theme-lumen-card p-6 text-sm text-[#6a5d52]">Aucune publication pour le moment.</li>
                @endforelse
            </ul>
            <div class="mt-8">
                {{ $posts->links() }}
            </div>
            </div>
        </section>
        </div>
    </main>
@endsection
