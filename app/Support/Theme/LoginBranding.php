<?php

namespace App\Support\Theme;

use App\Models\Restaurant;

/**
 * Couleurs page de connexion alignées sur Apparence (RestaurantThemeSetting),
 * avec ajustements pour le contraste (texte blanc sur fond, texte sur blanc).
 */
final class LoginBranding
{
    private const FALLBACK_PRIMARY = '#007d69';

    private const FALLBACK_SECONDARY = '#2C1810';

    private const FALLBACK_TEXT = '#1c1917';

    /** Ratio WCAG AA texte normal sur fond. */
    private const MIN_CONTRAST_WHITE_BG = 4.5;

    /**
     * @return array{
     *     wrapper_style: string,
     *     portal_bg: string,
     *     portal_fg: string,
     *     portal_fg_muted: string,
     *     accent: string,
     *     accent_hover: string,
     *     on_white: string,
     *     text: string,
     *     text_muted: string,
     *     card_muted: string,
     *     selection_bg: string,
     *     ring: string,
     * }
     */
    public static function resolve(): array
    {
        $restaurant = Restaurant::query()->with('themeSetting')->orderBy('id')->first();
        $theme = $restaurant?->themeSetting;

        $primary = self::normalizeHex($theme?->color_primary) ?? self::FALLBACK_PRIMARY;
        $secondary = self::normalizeHex($theme?->color_secondary) ?? self::FALLBACK_SECONDARY;
        $text = self::normalizeHex($theme?->color_text) ?? self::FALLBACK_TEXT;

        $portalBg = self::ensureContrastWithWhite($primary, self::MIN_CONTRAST_WHITE_BG);
        $accent = self::ensureContrastWithWhite($primary, self::MIN_CONTRAST_WHITE_BG);
        $accentHover = self::darkenHex($accent, 0.12);
        $onWhite = self::ensureContrastOnWhite($primary, self::MIN_CONTRAST_WHITE_BG);
        $textMuted = self::mixHex($text, '#78716c', 0.35);
        $cardMuted = self::ensureContrastOnWhite(self::mixHex($text, '#52525b', 0.45), self::MIN_CONTRAST_WHITE_BG);

        $ring = self::ensureContrastOnWhite($secondary, 3.0);
        $selectionBg = self::mixHex($primary, '#ffffff', 0.88);

        $portalFg = self::ensureLightTextOnDarkBackground($portalBg, self::MIN_CONTRAST_WHITE_BG);
        $portalFgMuted = self::ensureContrastWithBackground(
            self::mixHex('#ffffff', $portalBg, 0.22),
            $portalBg,
            self::MIN_CONTRAST_WHITE_BG
        );

        $wrapperStyle = sprintf(
            '--login-portal-bg:%s;--login-portal-fg:%s;--login-portal-fg-muted:%s;--login-accent:%s;--login-accent-hover:%s;--login-on-white:%s;--login-text:%s;--login-text-muted:%s;--login-card-muted:%s;--login-selection-bg:%s;--login-ring:%s;',
            e($portalBg),
            e($portalFg),
            e($portalFgMuted),
            e($accent),
            e($accentHover),
            e($onWhite),
            e($text),
            e($textMuted),
            e($cardMuted),
            e($selectionBg),
            e($ring)
        );

        return [
            'wrapper_style' => $wrapperStyle,
            'portal_bg' => $portalBg,
            'portal_fg' => $portalFg,
            'portal_fg_muted' => $portalFgMuted,
            'accent' => $accent,
            'accent_hover' => $accentHover,
            'on_white' => $onWhite,
            'text' => $text,
            'text_muted' => $textMuted,
            'card_muted' => $cardMuted,
            'selection_bg' => $selectionBg,
            'ring' => $ring,
        ];
    }

    private static function normalizeHex(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        $v = ltrim(trim($value), '#');
        if (strlen($v) === 3) {
            $v = $v[0].$v[0].$v[1].$v[1].$v[2].$v[2];
        }

        if (strlen($v) !== 6 || ! ctype_xdigit($v)) {
            return null;
        }

        return '#'.strtolower($v);
    }

    /**
     * @return array{0: int, 1: int, 2: int}
     */
    private static function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');

        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }

    private static function rgbToHex(int $r, int $g, int $b): string
    {
        return sprintf('#%02x%02x%02x', max(0, min(255, $r)), max(0, min(255, $g)), max(0, min(255, $b)));
    }

    private static function relativeLuminance(string $hex): float
    {
        $rgb = self::hexToRgb($hex);
        $linear = [];
        foreach ($rgb as $c) {
            $c = $c / 255;
            $linear[] = $c <= 0.03928 ? $c / 12.92 : (($c + 0.055) / 1.055) ** 2.4;
        }

        return 0.2126 * $linear[0] + 0.7152 * $linear[1] + 0.0722 * $linear[2];
    }

    private static function contrastRatio(string $hex1, string $hex2): float
    {
        $l1 = self::relativeLuminance($hex1);
        $l2 = self::relativeLuminance($hex2);
        $L1 = max($l1, $l2);
        $L2 = min($l1, $l2);

        return ($L1 + 0.05) / ($L2 + 0.05);
    }

    /** Texte sur fond portail : blanc si le fond est assez sombre, sinon texte foncé (fond clair). */
    private static function ensureLightTextOnDarkBackground(string $bgHex, float $minRatio): string
    {
        if (self::contrastRatio('#ffffff', $bgHex) >= $minRatio) {
            return '#ffffff';
        }

        return self::ensureContrastOnWhite($bgHex, $minRatio);
    }

    /**
     * Ajuste une couleur de premier plan jusqu’à contraste suffisant avec le fond (texte sur portail).
     */
    private static function ensureContrastWithBackground(string $fgHex, string $bgHex, float $minRatio): string
    {
        $fg = $fgHex;
        for ($i = 0; $i < 36; $i++) {
            if (self::contrastRatio($fg, $bgHex) >= $minRatio) {
                return $fg;
            }
            $fg = self::mixHex($fg, '#ffffff', 0.14);
        }

        return '#ffffff';
    }

    /** Assombrit une couleur jusqu’à contraste suffisant avec le blanc (texte clair sur fond coloré). */
    private static function ensureContrastWithWhite(string $hex, float $minRatio): string
    {
        $rgb = self::hexToRgb($hex);
        for ($i = 0; $i < 28; $i++) {
            $current = self::rgbToHex($rgb[0], $rgb[1], $rgb[2]);
            if (self::contrastRatio('#ffffff', $current) >= $minRatio) {
                return $current;
            }
            $rgb = [
                (int) round($rgb[0] * 0.88),
                (int) round($rgb[1] * 0.88),
                (int) round($rgb[2] * 0.88),
            ];
        }

        return '#1a1a1a';
    }

    /** Assombrit jusqu’à contraste suffisant sur fond blanc (texte foncé). */
    private static function ensureContrastOnWhite(string $hex, float $minRatio): string
    {
        $rgb = self::hexToRgb($hex);
        for ($i = 0; $i < 28; $i++) {
            $current = self::rgbToHex($rgb[0], $rgb[1], $rgb[2]);
            if (self::contrastRatio($current, '#ffffff') >= $minRatio) {
                return $current;
            }
            $rgb = [
                (int) round($rgb[0] * 0.85),
                (int) round($rgb[1] * 0.85),
                (int) round($rgb[2] * 0.85),
            ];
        }

        return '#1c1917';
    }

    private static function darkenHex(string $hex, float $amount): string
    {
        $rgb = self::hexToRgb($hex);
        $f = 1 - $amount;

        return self::rgbToHex(
            (int) round($rgb[0] * $f),
            (int) round($rgb[1] * $f),
            (int) round($rgb[2] * $f)
        );
    }

    /** @param  string  $a  hex @param  string  $b  hex @param  float  $t  0 = a, 1 = b */
    private static function mixHex(string $a, string $b, float $t): string
    {
        $ra = self::hexToRgb($a);
        $rb = self::hexToRgb($b);

        return self::rgbToHex(
            (int) round($ra[0] * (1 - $t) + $rb[0] * $t),
            (int) round($ra[1] * (1 - $t) + $rb[1] * $t),
            (int) round($ra[2] * (1 - $t) + $rb[2] * $t)
        );
    }
}
