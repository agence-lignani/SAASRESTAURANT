<div {{ $attributes->class(['fi-simple-page adobe-login-page']) }}>
    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::SIMPLE_PAGE_START, scopes: $this->getRenderHookScopes()) }}

    <div class="adobe-login-shell">
        <div class="adobe-login-card">
            @if (session('status'))
                <div class="adobe-login-flash adobe-login-flash--success" role="status">
                    {{ session('status') }}
                </div>
            @endif
            @if (session('error'))
                <div class="adobe-login-flash adobe-login-flash--danger" role="alert">
                    {{ session('error') }}
                </div>
            @endif
            <div class="adobe-login-brand">{{ config('app.name') }}</div>
            <h1 class="adobe-login-title">Se connecter</h1>
            <p class="adobe-login-sub">
                Saisissez le <strong>nom</strong> (nom de famille enregistré sur votre compte) et votre <strong>code</strong> à six chiffres.
            </p>
            <div class="adobe-login-form">
                {{ $this->content }}
            </div>
            <p class="adobe-login-footer">
                <a href="{{ url('/') }}">Retour au site public</a>
            </p>
        </div>
    </div>

    @if (! $this instanceof \Filament\Tables\Contracts\HasTable)
        <x-filament-actions::modals />
    @endif

    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::SIMPLE_PAGE_END, scopes: $this->getRenderHookScopes()) }}
</div>
