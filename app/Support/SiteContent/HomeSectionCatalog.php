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
        return ['hero', 'manifesto', 'menus', 'values', 'gallery_highlights', 'practical'];
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return [
            'hero' => 'Accueil — bandeau principal',
            'manifesto' => 'À propos du restaurant',
            'menus' => 'Menus & formules',
            'values' => 'Valeurs / engagements',
            'gallery_highlights' => 'Galerie ambiance',
            'practical' => 'Contact & accès',
        ];
    }

    /** @return list<string> */
    public static function defaultOrder(): array
    {
        return self::keys();
    }
}
