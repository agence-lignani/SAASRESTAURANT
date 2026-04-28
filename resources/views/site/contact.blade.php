@extends('layouts.bistro', ['cssVars' => $cssVars, 'bistroFontStylesheet' => $bistroFontStylesheet ?? null])

@section('title', $restaurant->name.' — '.($pageContent['title'] ?? 'Contact'))

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
        <div class="bistro-container max-w-2xl">
            @php($sectionOrder = $pageContent['section_order'] ?? \App\Support\SiteContent\PageSectionCatalog::defaultOrder('contact'))
            @foreach($sectionOrder as $sectionKey)
                @if($sectionKey === 'header')
                    <section>
                        <h1 class="bistro-title text-3xl text-stone-900 md:text-4xl">{{ $pageContent['title'] ?? 'Nous écrire' }}</h1>
                        <div class="prose prose-stone mt-3 max-w-none text-sm text-stone-600 prose-p:my-1">
                            {!! \App\Support\SiteContent\SiteContentHtml::paragraph($pageContent['intro'] ?? 'Réservation, privatisation ou simple message : nous vous répondrons dès que possible.') !!}
                        </div>
                    </section>
                @endif

                @if($sectionKey === 'feedback')
                    <section class="mt-8 space-y-4">
                        @if(session('contact_ok'))
                            <div class="prose prose-stone prose-emerald max-w-none rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-900 prose-p:my-0" role="status">
                                {!! \App\Support\SiteContent\SiteContentHtml::safe($pageContent['success_message'] ?? 'Merci, votre message a bien été envoyé.') !!}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-900" role="alert">
                                <p class="font-semibold">{{ $pageContent['error_title'] ?? 'Veuillez corriger les champs ci-dessous.' }}</p>
                                <ul class="mt-2 list-disc pl-5">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </section>
                @endif

                @if($sectionKey === 'form')
                    <form method="post" action="{{ route('site.contact.store') }}" class="mt-10 space-y-6">
                        @csrf
                        {{-- Honeypot F11 : laisser vide --}}
                        <div class="hidden" aria-hidden="true">
                            <label for="website">Ne pas remplir ce champ</label>
                            <input type="text" name="website" id="website" value="" tabindex="-1" autocomplete="off" class="w-full rounded-lg border border-stone-200 px-3 py-2 text-sm"/>
                        </div>

                        <div>
                            <label for="name" class="block text-sm font-medium text-stone-800">Nom</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required class="mt-1 w-full rounded-xl border border-stone-200 px-4 py-2.5 text-sm shadow-sm focus:border-[var(--bistro-color-primary)] focus:outline-none focus:ring-2 focus:ring-[var(--bistro-color-primary)]/20"/>
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-stone-800">{{ $pageContent['label_email'] ?? 'E-mail' }}</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required class="mt-1 w-full rounded-xl border border-stone-200 px-4 py-2.5 text-sm shadow-sm focus:border-[var(--bistro-color-primary)] focus:outline-none focus:ring-2 focus:ring-[var(--bistro-color-primary)]/20"/>
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-stone-800">{{ $pageContent['label_phone'] ?? 'Téléphone' }} <span class="font-normal text-stone-500">{{ $pageContent['label_phone_optional'] ?? '(optionnel)' }}</span></label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="mt-1 w-full rounded-xl border border-stone-200 px-4 py-2.5 text-sm shadow-sm focus:border-[var(--bistro-color-primary)] focus:outline-none focus:ring-2 focus:ring-[var(--bistro-color-primary)]/20"/>
                        </div>
                        <div>
                            <label for="subject" class="block text-sm font-medium text-stone-800">{{ $pageContent['label_subject'] ?? 'Sujet' }}</label>
                            <select name="subject" id="subject" required class="mt-1 w-full rounded-xl border border-stone-200 bg-white px-4 py-2.5 text-sm shadow-sm focus:border-[var(--bistro-color-primary)] focus:outline-none focus:ring-2 focus:ring-[var(--bistro-color-primary)]/20">
                                <option value="" disabled @selected(old('subject') === null)>{{ $pageContent['subject_placeholder'] ?? 'Choisir…' }}</option>
                                @foreach($subjectOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(old('subject') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="body" class="block text-sm font-medium text-stone-800">{{ $pageContent['label_message'] ?? 'Message' }}</label>
                            <textarea name="body" id="body" rows="6" required class="mt-1 w-full rounded-xl border border-stone-200 px-4 py-2.5 text-sm shadow-sm focus:border-[var(--bistro-color-primary)] focus:outline-none focus:ring-2 focus:ring-[var(--bistro-color-primary)]/20">{{ old('body') }}</textarea>
                        </div>
                        <button type="submit" class="bistro-btn-primary w-full sm:w-auto">{{ $pageContent['submit_label'] ?? 'Envoyer' }}</button>
                    </form>
                @endif
            @endforeach
        </div>
    </main>
@endsection
