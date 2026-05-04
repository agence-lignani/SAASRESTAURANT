@extends('layouts.bistro', ['cssVars' => $cssVars, 'bistroFontStylesheet' => $bistroFontStylesheet ?? null])

@section('title', $restaurant->name.' — '.($pageContent['title'] ?? 'Contact'))

@section('content')
    @include('site.partials.top-contact-bar', ['restaurant' => $restaurant])

    <header class="bistro-inner-header">
        <div class="bistro-inner-header__inner">
            <a href="{{ route('site.home') }}" class="bistro-inner-brand">
                {{ $restaurant->name }}
            </a>
            @include('site.partials.main-nav')
        </div>
    </header>

    <section class="bistro-page-hero" aria-hidden="true">
        <div class="palazzo-shell palazzo-center" style="position:relative;">
            <p class="bistro-page-hero__eyebrow">{{ $restaurant->name }}</p>
            <h1 class="bistro-page-hero__title">{{ $pageContent['title'] ?? 'Nous écrire' }}</h1>
            @if(filled($pageContent['intro'] ?? null))
                <p class="bistro-page-hero__intro">
                    {!! strip_tags(\App\Support\SiteContent\SiteContentHtml::paragraph($pageContent['intro'])) !!}
                </p>
            @else
                <p class="bistro-page-hero__intro">
                    Réservation, privatisation ou simple message&nbsp;: nous vous répondrons dès que possible.
                </p>
            @endif
        </div>
    </section>

    <main id="contenu-principal" class="bistro-page-content" aria-labelledby="contact-heading">
        <h1 id="contact-heading" class="sr-only">{{ $pageContent['title'] ?? 'Nous écrire' }}</h1>
        <div class="palazzo-shell">
            @php($sectionOrder = $pageContent['section_order'] ?? \App\Support\SiteContent\PageSectionCatalog::defaultOrder('contact'))

            @foreach($sectionOrder as $sectionKey)
                @if($sectionKey === 'feedback')
                    @if(session('contact_ok'))
                        <div class="bistro-alert bistro-alert--success mb-8" role="status">
                            {!! \App\Support\SiteContent\SiteContentHtml::safe($pageContent['success_message'] ?? 'Merci, votre message a bien été envoyé.') !!}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="bistro-alert bistro-alert--error mb-8" role="alert">
                            <strong>{{ $pageContent['error_title'] ?? 'Veuillez corriger les champs ci-dessous.' }}</strong>
                            <ul style="margin-top:0.5rem;padding-left:1.2rem;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                @endif

                @if($sectionKey === 'form')
                    <div class="bistro-contact-wrap">
                        {{-- Sidebar info --}}
                        <aside class="bistro-contact-aside">
                            <h2 class="bistro-contact-aside__title">{{ $restaurant->name }}</h2>
                            @if(filled($restaurant->address_line1))
                                <p class="bistro-contact-aside__item">
                                    <span aria-hidden="true">📍</span>
                                    <span>{{ $restaurant->address_line1 }}@if(filled($restaurant->city))<br>{{ $restaurant->postal_code }} {{ $restaurant->city }}@endif</span>
                                </p>
                            @endif
                            @if(filled($restaurant->contact_phone))
                                <p class="bistro-contact-aside__item">
                                    <span aria-hidden="true">📞</span>
                                    <a href="tel:{{ preg_replace('/\s+/', '', $restaurant->contact_phone) }}" style="color:inherit;text-decoration:none;font-weight:600;">{{ $restaurant->contact_phone }}</a>
                                </p>
                            @endif
                            @if(filled($restaurant->contact_email))
                                <p class="bistro-contact-aside__item">
                                    <span aria-hidden="true">✉</span>
                                    <a href="mailto:{{ $restaurant->contact_email }}" style="color:inherit;text-decoration:none;font-weight:600;">{{ $restaurant->contact_email }}</a>
                                </p>
                            @endif
                        </aside>

                        {{-- Form card --}}
                        <div class="bistro-form-card">
                            <form method="post" action="{{ route('site.contact.store') }}" novalidate>
                                @csrf
                                {{-- Honeypot --}}
                                <div class="hidden" aria-hidden="true">
                                    <label for="website">Ne pas remplir ce champ</label>
                                    <input type="text" name="website" id="website" value="" tabindex="-1" autocomplete="off" style="width:100%;"/>
                                </div>

                                <div class="bistro-form-field">
                                    <label for="name" class="bistro-form-label">Nom</label>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}" required autocomplete="name" class="bistro-form-input"/>
                                </div>

                                <div class="bistro-form-field">
                                    <label for="email" class="bistro-form-label">{{ $pageContent['label_email'] ?? 'E-mail' }}</label>
                                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="email" class="bistro-form-input"/>
                                </div>

                                <div class="bistro-form-field">
                                    <label for="phone" class="bistro-form-label">
                                        {{ $pageContent['label_phone'] ?? 'Téléphone' }}
                                        <span style="font-weight:400;opacity:0.6;"> {{ $pageContent['label_phone_optional'] ?? '(optionnel)' }}</span>
                                    </label>
                                    <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" autocomplete="tel" class="bistro-form-input"/>
                                </div>

                                <div class="bistro-form-field">
                                    <label for="subject" class="bistro-form-label">{{ $pageContent['label_subject'] ?? 'Sujet' }}</label>
                                    <div class="bistro-form-select-wrap">
                                        <select name="subject" id="subject" required class="bistro-form-select">
                                            <option value="" disabled @selected(old('subject') === null)>{{ $pageContent['subject_placeholder'] ?? 'Choisir…' }}</option>
                                            @foreach($subjectOptions as $value => $label)
                                                <option value="{{ $value }}" @selected(old('subject') === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="bistro-form-field">
                                    <label for="body" class="bistro-form-label">{{ $pageContent['label_message'] ?? 'Message' }}</label>
                                    <textarea name="body" id="body" rows="6" required class="bistro-form-textarea">{{ old('body') }}</textarea>
                                </div>

                                <button type="submit" class="bistro-form-submit">{{ $pageContent['submit_label'] ?? 'Envoyer le message' }}</button>
                            </form>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </main>
@endsection
