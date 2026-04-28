@php
    $livewire ??= null;
    $renderHookScopes = $livewire?->getRenderHookScopes();
@endphp

<x-filament-panels::layout.base :livewire="$livewire">
    @props([
        'after' => null,
        'heading' => null,
        'subheading' => null,
    ])

    {{-- Styles ici (dans le body) : un @push('styles') dans ce layout arriverait après @stack du <head> et ne serait jamais rendu. --}}
    <style>
        body.fi-body:has(#adobe-login-viewport) {
            background-color: #f3f3f3 !important;
        }

        #adobe-login-viewport {
            flex: 1;
            width: 100%;
            min-height: 100dvh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.25rem;
            background: #f3f3f3;
        }

        #adobe-login-viewport .adobe-login-main {
            width: 100%;
            max-width: none;
            background: transparent;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .adobe-login-page .fi-sc-actions .fi-btn.fi-color-primary,
        .adobe-login-page .fi-sc-actions .fi-btn-color-primary,
        .adobe-login-page .fi-sc-actions button[type='submit'],
        .adobe-login-page .fi-sc-actions a.fi-btn {
            width: 100% !important;
            justify-content: center !important;
            border-radius: 24px !important;
            font-size: 0.9375rem !important;
            font-weight: 600 !important;
            padding: 0.7rem 1rem !important;
            background: #eb1000 !important;
            border-color: #eb1000 !important;
            color: #fff !important;
        }

        .adobe-login-page .fi-sc-actions .fi-btn.fi-color-primary:hover,
        .adobe-login-page .fi-sc-actions button[type='submit']:hover,
        .adobe-login-page .fi-sc-actions a.fi-btn:hover {
            background: #c90e00 !important;
            border-color: #c90e00 !important;
        }
    </style>

    <div id="adobe-login-viewport" class="adobe-login-viewport">
        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::SIMPLE_LAYOUT_START, scopes: $renderHookScopes) }}

        @if (($hasTopbar ?? true) && filament()->auth()->check())
            <div class="fi-simple-layout-header">
                @if (filament()->hasDatabaseNotifications())
                    @livewire(filament()->getDatabaseNotificationsLivewireComponent(), [
                        'lazy' => filament()->hasLazyLoadedDatabaseNotifications(),
                        'position' => \Filament\Enums\DatabaseNotificationsPosition::Topbar,
                    ])
                @endif

                @if (filament()->hasUserMenu())
                    @livewire(Filament\Livewire\SimpleUserMenu::class)
                @endif
            </div>
        @endif

        <main @class(['adobe-login-main mx-auto w-full max-w-none flex-1'])>
            {{ $slot }}
        </main>

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::FOOTER, scopes: $renderHookScopes) }}

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::SIMPLE_LAYOUT_END, scopes: $renderHookScopes) }}
    </div>
</x-filament-panels::layout.base>
