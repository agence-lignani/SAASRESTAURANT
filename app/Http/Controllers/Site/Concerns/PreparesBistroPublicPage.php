<?php

namespace App\Http\Controllers\Site\Concerns;

use App\Models\Restaurant;
use App\Theme\BistroFonts;
use App\Theme\BistroManifest;

trait PreparesBistroPublicPage
{
    /**
     * @return array{cssVars: array<string, string>, bistroFontStylesheet: ?string}
     */
    protected function bistroThemePayload(Restaurant $restaurant): array
    {
        $restaurant->loadMissing(['themeSetting']);
        $theme = $restaurant->themeSetting;

        return [
            'cssVars' => BistroManifest::cssVariablesForRestaurant($restaurant),
            'bistroFontStylesheet' => BistroFonts::stylesheetHref(
                $theme?->font_heading_key,
                $theme?->font_body_key,
            ),
        ];
    }
}
