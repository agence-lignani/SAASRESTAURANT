@extends('layouts.bistro', ['cssVars' => $cssVars, 'bistroFontStylesheet' => $bistroFontStylesheet ?? null, 'metaDescription' => $metaDescription ?? null])

@section('title', $restaurant->name.' — '.($legalPage->title ?? ($slug === 'mentions-legales' ? 'Mentions légales' : 'Politique de confidentialité')))

@section('content')
    <div class="theme-lumen">
        @include('site.partials.lumen-header', ['restaurant' => $restaurant])

        <main id="contenu-principal">
            @include('site.partials.lumen-page-hero', [
                'eyebrow' => 'Informations',
                'title' => $legalPage->title ?? ($slug === 'mentions-legales' ? 'Mentions légales' : 'Politique de confidentialité'),
                'intro' => 'Retrouvez les informations utiles et les engagements de la maison.',
            ])
            <section class="theme-lumen-page-section">
                <div class="bistro-container max-w-3xl">
                    <article class="theme-lumen-prose">
                @if($legalPage)
                    {!! nl2br(e($legalPage->body)) !!}
                @else
                    <p class="text-stone-500">Ce texte sera renseigné depuis l’administration (Contenus légaux).</p>
                @endif
                    </article>
                </div>
            </section>
        </main>
    </div>
@endsection
