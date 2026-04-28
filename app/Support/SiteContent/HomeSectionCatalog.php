<?php

namespace App\Support\SiteContent;

/**
 * Sections d’accueil disponibles (implémentées en Blade) — ordre éditable, pas de nouveau type côté BO.
 */
final class HomeSectionCatalog
{
    /** @return list<string> */
    public static function keys(): array
    {
        return ['hero', 'manifesto', 'menus', 'reviews_widget', 'espaces', 'faq', 'carte_narrative', 'gallery_highlights', 'spotlight', 'practical'];
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return [
            'hero' => 'Accueil — bandeau principal',
            'manifesto' => 'À propos du restaurant',
            'carte_narrative' => 'Carte signature',
            'menus' => 'Menus & formules',
            'gallery_highlights' => 'Galerie ambiance',
            'reviews_widget' => 'Avis clients',
            'espaces' => 'Le chef',
            'faq' => 'FAQ pratique',
            'spotlight' => 'CTA réservation finale',
            'practical' => 'Contact & accès',
        ];
    }

    /** @return list<string> */
    public static function defaultOrder(): array
    {
        return self::keys();
    }
}
