<?php

namespace App\Theme;

/**
 * Polices proposées au back-office : toutes servies par Google Fonts sous licence
 * SIL Open Font License ou Apache 2.0 — usage commercial et modification autorisés.
 *
 * @see https://fonts.google.com/attribution
 */
final class BistroFonts
{
    public const DEFAULT_KEY = 'plus-jakarta-sans';

    /**
     * @return array<string, array{label: string, stack: string, google?: string, wght?: string}>
     */
    public static function definitions(): array
    {
        return [
            /* Sans-serif — UI & lecture */
            'plus-jakarta-sans' => [
                'label' => 'Plus Jakarta Sans — sans géométrique (UI)',
                'stack' => '"Plus Jakarta Sans", system-ui, sans-serif',
                'google' => 'Plus+Jakarta+Sans',
            ],
            'instrument-sans' => [
                'label' => 'Instrument Sans — sans humaniste',
                'stack' => '"Instrument Sans", system-ui, sans-serif',
                'google' => 'Instrument+Sans',
            ],
            'inter' => [
                'label' => 'Inter — sans neutre (écrans)',
                'stack' => 'Inter, system-ui, sans-serif',
                'google' => 'Inter',
            ],
            'source-sans-3' => [
                'label' => 'Source Sans 3 — sans Adobe / lecture',
                'stack' => '"Source Sans 3", system-ui, sans-serif',
                'google' => 'Source+Sans+3',
            ],
            'dm-sans' => [
                'label' => 'DM Sans — sans géométrique doux',
                'stack' => '"DM Sans", system-ui, sans-serif',
                'google' => 'DM+Sans',
            ],
            'nunito-sans' => [
                'label' => 'Nunito Sans — sans arrondi',
                'stack' => '"Nunito Sans", system-ui, sans-serif',
                'google' => 'Nunito+Sans',
            ],
            'work-sans' => [
                'label' => 'Work Sans — sans sobre',
                'stack' => '"Work Sans", system-ui, sans-serif',
                'google' => 'Work+Sans',
            ],
            'manrope' => [
                'label' => 'Manrope — sans moderne',
                'stack' => 'Manrope, system-ui, sans-serif',
                'google' => 'Manrope',
            ],
            'outfit' => [
                'label' => 'Outfit — sans contemporain',
                'stack' => 'Outfit, system-ui, sans-serif',
                'google' => 'Outfit',
            ],
            'lexend' => [
                'label' => 'Lexend — sans lisibilité renforcée',
                'stack' => 'Lexend, system-ui, sans-serif',
                'google' => 'Lexend',
            ],
            'figtree' => [
                'label' => 'Figtree — sans convivial',
                'stack' => 'Figtree, system-ui, sans-serif',
                'google' => 'Figtree',
            ],
            'rubik' => [
                'label' => 'Rubik — sans légèrement arrondie',
                'stack' => 'Rubik, system-ui, sans-serif',
                'google' => 'Rubik',
            ],
            'montserrat' => [
                'label' => 'Montserrat — sans géométrique classique',
                'stack' => 'Montserrat, system-ui, sans-serif',
                'google' => 'Montserrat',
            ],
            'lato' => [
                'label' => 'Lato — sans humaniste populaire',
                'stack' => 'Lato, system-ui, sans-serif',
                'google' => 'Lato',
            ],
            'open-sans' => [
                'label' => 'Open Sans — sans neutre web',
                'stack' => '"Open Sans", system-ui, sans-serif',
                'google' => 'Open+Sans',
            ],
            'roboto' => [
                'label' => 'Roboto — sans Material / Android',
                'stack' => 'Roboto, system-ui, sans-serif',
                'google' => 'Roboto',
            ],
            'raleway' => [
                'label' => 'Raleway — sans élégant étroit',
                'stack' => 'Raleway, system-ui, sans-serif',
                'google' => 'Raleway',
            ],
            'ubuntu' => [
                'label' => 'Ubuntu — sans humaniste',
                'stack' => 'Ubuntu, system-ui, sans-serif',
                'google' => 'Ubuntu',
            ],
            'josefin-sans' => [
                'label' => 'Josefin Sans — sans géométrique vintage',
                'stack' => '"Josefin Sans", system-ui, sans-serif',
                'google' => 'Josefin+Sans',
            ],
            'quicksand' => [
                'label' => 'Quicksand — sans arrondie douce',
                'stack' => 'Quicksand, system-ui, sans-serif',
                'google' => 'Quicksand',
            ],
            'oswald' => [
                'label' => 'Oswald — sans condensed (affiches)',
                'stack' => 'Oswald, system-ui, sans-serif',
                'google' => 'Oswald',
            ],
            /* Serif — titres & éditorial */
            'playfair-display' => [
                'label' => 'Playfair Display — serif display élégant',
                'stack' => '"Playfair Display", Georgia, serif',
                'google' => 'Playfair+Display',
            ],
            'cormorant-garamond' => [
                'label' => 'Cormorant Garamond — serif classique fin',
                'stack' => '"Cormorant Garamond", Georgia, serif',
                'google' => 'Cormorant+Garamond',
            ],
            'lora' => [
                'label' => 'Lora — serif de lecture chaleureux',
                'stack' => 'Lora, Georgia, serif',
                'google' => 'Lora',
            ],
            'merriweather' => [
                'label' => 'Merriweather — serif robuste écran',
                'stack' => 'Merriweather, Georgia, serif',
                'google' => 'Merriweather',
            ],
            'libre-baskerville' => [
                'label' => 'Libre Baskerville — serif traditionnel',
                'stack' => '"Libre Baskerville", Georgia, serif',
                'google' => 'Libre+Baskerville',
            ],
            'eb-garamond' => [
                'label' => 'EB Garamond — serif Renaissance',
                'stack' => '"EB Garamond", Georgia, serif',
                'google' => 'EB+Garamond',
            ],
            'fraunces' => [
                'label' => 'Fraunces — serif organique / display',
                'stack' => 'Fraunces, Georgia, serif',
                'google' => 'Fraunces',
                'wght' => '400;500;600;700',
            ],
            'crimson-pro' => [
                'label' => 'Crimson Pro — serif éditorial',
                'stack' => '"Crimson Pro", Georgia, serif',
                'google' => 'Crimson+Pro',
            ],
            'spectral' => [
                'label' => 'Spectral — serif scientifique / sérieux',
                'stack' => 'Spectral, Georgia, serif',
                'google' => 'Spectral',
            ],
            /* Système (aucune requête réseau) */
            'system' => [
                'label' => 'Système — police native (sans téléchargement)',
                'stack' => 'system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
            ],
            'georgia' => [
                'label' => 'Georgia / Times — serif système',
                'stack' => 'Georgia, "Times New Roman", Times, serif',
            ],
        ];
    }

    /**
     * @return array<string, string> key => label (pour Filament)
     */
    public static function options(): array
    {
        return collect(self::definitions())
            ->map(fn (array $d): string => $d['label'])
            ->all();
    }

    public static function fontFamily(?string $key): string
    {
        $definitions = self::definitions();
        $k = $key && isset($definitions[$key]) ? $key : self::DEFAULT_KEY;

        return $definitions[$k]['stack'];
    }

    /**
     * URL Google Fonts (css2) pour les familles nécessaires (titres + corps), sans doublon.
     */
    public static function stylesheetHref(?string $headingKey, ?string $bodyKey): ?string
    {
        $definitions = self::definitions();
        $h = $headingKey && isset($definitions[$headingKey]) ? $headingKey : self::DEFAULT_KEY;
        $b = $bodyKey && isset($definitions[$bodyKey]) ? $bodyKey : self::DEFAULT_KEY;

        $seen = [];
        $parts = [];
        foreach ([$h, $b] as $key) {
            $def = $definitions[$key];
            if (! isset($def['google'])) {
                continue;
            }
            $g = $def['google'];
            if (isset($seen[$g])) {
                continue;
            }
            $seen[$g] = true;
            $wght = $def['wght'] ?? '400;500;600;700';
            $parts[] = 'family='.$g.':wght@'.$wght;
        }

        if ($parts === []) {
            return null;
        }

        return 'https://fonts.googleapis.com/css2?'.implode('&', $parts).'&display=swap';
    }
}
