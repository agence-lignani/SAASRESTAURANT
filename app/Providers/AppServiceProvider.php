<?php

namespace App\Providers;

use App\Contracts\MenuImport\MenuImportExtractor;
use App\Http\Middleware\BindFilamentRestaurant;
use App\Models\Reservation;
use App\Models\Restaurant;
use App\Observers\ReservationObserver;
use App\Services\MenuImport\StubMenuImportExtractor;
use App\Services\MenuImport\TesseractMenuImportExtractor;
use App\Support\Filament\SiteContentRichEditorStateNormalizer;
use Filament\Forms\Components\RichEditor;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(MenuImportExtractor::class, function ($app): MenuImportExtractor {
            return match (config('menu_import.driver', 'tesseract')) {
                'stub' => $app->make(StubMenuImportExtractor::class),
                default => $app->make(TesseractMenuImportExtractor::class),
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        FilamentAsset::register([
            Js::make(
                'site-content-sections-dnd',
                asset('js/site-content-sections-dnd.js').'?v='.filemtime(public_path('js/site-content-sections-dnd.js'))
            ),
        ]);

        // TipTap PHP plante sur setContent([]) ou { type: doc } sans clé content (Schema::apply).
        // On normalise toutes les instances RichEditor (hydratation + état Livewire entre requêtes).
        RichEditor::configureUsing(function (RichEditor $component): void {
            $component->formatStateUsing(fn ($state) => SiteContentRichEditorStateNormalizer::normalizeRichEditorLeafValue($state));

            $component->afterStateUpdated(function (RichEditor $component): void {
                $raw = $component->getRawState();
                if (! SiteContentRichEditorStateNormalizer::richEditorLeafNeedsNormalization($raw)) {
                    return;
                }

                $component->state(SiteContentRichEditorStateNormalizer::normalizeRichEditorLeafValue($raw));
            });
        });

        // Les POST Livewire ne passent pas par la pile Filament : réappliquer ce middleware
        // comme les routes d’origine (memo path), pour lier `filament.restaurant`.
        Livewire::addPersistentMiddleware([
            BindFilamentRestaurant::class,
        ]);

        Reservation::observe(ReservationObserver::class);

        RateLimiter::for('chat', function (Request $request): Limit {
            $perMinute = max(6, (int) config('llm.rate_limit_per_minute', 24));

            return Limit::perMinute($perMinute)->by($request->ip());
        });

        View::composer('layouts.bistro', function (\Illuminate\View\View $view): void {
            $restaurant = view()->shared('restaurant');

            if (! $restaurant instanceof Restaurant) {
                $view->with('bistroChat', [
                    'enabled' => false,
                    'position' => 'bottom-end',
                    'endpoint' => '#',
                ]);

                return;
            }

            $setting = $restaurant->chatSetting;

            $view->with('bistroChat', [
                'enabled' => (bool) ($setting?->is_enabled),
                'position' => in_array($setting?->widget_position, ['bottom-end', 'bottom-start'], true)
                    ? $setting->widget_position
                    : 'bottom-end',
                'endpoint' => route('site.chat.store'),
            ]);
        });
    }
}
