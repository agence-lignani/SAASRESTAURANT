<?php

namespace App\Theme;

use App\Models\Restaurant;

/**
 * Charge le manifeste thème Bistro (cf. Cahiers des charges §2.6.3).
 */
final class BistroManifest
{
    public static function path(): string
    {
        return base_path('themes/bistro/manifest.php');
    }

    /**
     * @return array<string, mixed>
     */
    public static function all(): array
    {
        $path = self::path();

        if (! is_file($path)) {
            return [];
        }

        /** @var array<string, mixed> */
        return require $path;
    }

    /**
     * @return array<string, string>
     */
    public static function cssVariables(): array
    {
        $map = self::cssVariablesFromManifest(self::all());
        $map['--bistro-font-heading'] = BistroFonts::fontFamily(null);
        $map['--bistro-font-body'] = BistroFonts::fontFamily(null);

        return $map;
    }

    /**
     * Variables CSS manifeste + surcharge `restaurant_theme_settings` (J2).
     *
     * @return array<string, string>
     */
    public static function cssVariablesForRestaurant(?Restaurant $restaurant): array
    {
        $map = self::cssVariables();

        if ($restaurant === null || $restaurant->themeSetting === null) {
            return $map;
        }

        $ts = $restaurant->themeSetting;

        if (filled($ts->color_primary)) {
            $map['--bistro-color-primary'] = $ts->color_primary;
        }
        if (filled($ts->color_secondary)) {
            $map['--bistro-color-secondary'] = $ts->color_secondary;
        }
        if (filled($ts->color_text)) {
            $map['--bistro-color-text'] = $ts->color_text;
        }
        if (filled($ts->radius_md)) {
            $map['--bistro-radius-md'] = $ts->radius_md.'rem';
        }
        if (filled($ts->radius_sm)) {
            $map['--bistro-radius-sm'] = $ts->radius_sm.'rem';
        }
        if (filled($ts->radius_lg)) {
            $map['--bistro-radius-lg'] = $ts->radius_lg.'rem';
        }

        $map['--bistro-font-heading'] = BistroFonts::fontFamily($ts->font_heading_key);
        $map['--bistro-font-body'] = BistroFonts::fontFamily($ts->font_body_key);

        return $map;
    }

    /**
     * @param  array<string, mixed>  $manifest
     * @return array<string, string>
     */
    private static function cssVariablesFromManifest(array $manifest): array
    {
        $tokens = $manifest['tokens'] ?? [];
        $colors = $tokens['colors'] ?? [];

        return [
            '--bistro-color-primary' => $colors['primary'] ?? '#8B4513',
            '--bistro-color-secondary' => $colors['secondary'] ?? '#2C1810',
            '--bistro-color-text' => $colors['text'] ?? '#1a1a1a',
            '--bistro-radius-sm' => ($tokens['radius']['sm'] ?? '0.25').'rem',
            '--bistro-radius-md' => ($tokens['radius']['md'] ?? '0.5').'rem',
            '--bistro-radius-lg' => ($tokens['radius']['lg'] ?? '1').'rem',
            '--bistro-shadow-sm' => $tokens['shadows']['sm'] ?? '0 1px 2px rgb(0 0 0 / 0.06)',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function fontOptions(): array
    {
        return BistroFonts::options();
    }
}
