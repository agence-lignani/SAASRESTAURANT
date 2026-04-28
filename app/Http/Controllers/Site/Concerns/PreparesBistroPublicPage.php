<?php

namespace App\Http\Controllers\Site\Concerns;

use App\Models\Restaurant;
use App\Theme\BistroManifest;

trait PreparesBistroPublicPage
{
    /**
     * @return array{cssVars: array<string, string>, bistroFontStylesheet: null}
     */
    protected function bistroThemePayload(Restaurant $restaurant): array
    {
        return [
            'cssVars' => BistroManifest::cssVariablesForRestaurant($restaurant),
            'bistroFontStylesheet' => null,
        ];
    }
}
