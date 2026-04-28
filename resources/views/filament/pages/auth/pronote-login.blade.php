@php
    $isDemo = ! app()->environment('production');
    $bannerRight = $isDemo ? 'SITE DE DEMONSTRATION' : 'SITE '.strtoupper(app()->environment());
    $role = $this->data['profile_role'] ?? 'owner';
    $profileEmoji = match ($role) {
        'reservation' => '📋',
        'editor' => '✏️',
        'server' => '🍽️',
        default => '🧭',
    };
@endphp

<div {{ $attributes->class(['fi-simple-page pronote-simple-page']) }}>
    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::SIMPLE_PAGE_START, scopes: $this->getRenderHookScopes()) }}

    <div class="pronote-banner" role="banner">
        <div class="pronote-banner-left">
            <span class="pronote-banner-icon" aria-hidden="true">🔐</span>
            <span class="pronote-banner-title">Connexion administration</span>
        </div>
        <span class="pronote-banner-badge">{{ $bannerRight }}</span>
    </div>

    <div class="pronote-portal-grid">
        <aside class="pronote-portal-aside" aria-label="Informations">
            @include('filament.pages.auth.partials.pronote-aside-portal')
        </aside>

        <div class="pronote-portal-right">
            @if ($this->loginStep === 1)
                <div class="pronote-login-card">
                    <h1 class="pronote-login-card-title">Choisissez votre espace</h1>
                    <p class="pronote-login-card-hint">
                        Sélectionnez un profil ci-dessous, puis saisissez vos identifiants à l’étape suivante.
                    </p>
                    <div class="pronote-login-actions">
                        {{ $this->content }}
                    </div>
                </div>
            @else
                <div class="pronote-login-card">
                    <div class="pronote-login-card-head">
                        <button type="button" wire:click="goToStep1" class="pronote-back-link">
                            ← Retour au choix du profil
                        </button>
                        <h1 class="pronote-login-card-title pronote-login-card-title--center">{{ $this->getProfileSpaceTitle() }}</h1>
                        <div class="pronote-login-icon-ring" aria-hidden="true">
                            <span class="pronote-login-icon-emoji">{{ $profileEmoji }}</span>
                        </div>
                        <p class="pronote-login-required-hint">* champs obligatoires</p>
                    </div>

                    <div class="pronote-login-step2">
                        <div class="pronote-logo-placeholder" aria-hidden="true">
                            <span class="pronote-logo-placeholder-line">LOGO</span>
                            <span class="pronote-logo-placeholder-line">ÉTABLISSEMENT</span>
                        </div>
                        <div class="pronote-login-actions">
                            {{ $this->content }}
                        </div>
                    </div>
                    <button type="button" wire:click="authenticate" class="pronote-manual-submit">
                        Se connecter
                    </button>
                </div>
            @endif
        </div>
    </div>

    <div class="pronote-demo-footer-link">
        <a href="{{ url('/') }}">{{ config('app.name') }} — site public</a>
    </div>

    @if (! $this instanceof \Filament\Tables\Contracts\HasTable)
        <x-filament-actions::modals />
    @endif

    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::SIMPLE_PAGE_END, scopes: $this->getRenderHookScopes()) }}
</div>
