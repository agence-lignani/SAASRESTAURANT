<?php

namespace App\Support\SiteContent;

final class PageSectionCatalog
{
    /**
     * @return array<string, array<string, string>>
     */
    public static function definitions(): array
    {
        return [
            'home' => [
                'hero' => 'Accueil — bandeau principal',
                'manifesto' => 'À propos du restaurant',
                'menus' => 'Menus & formules',
                'values' => 'Bloc valeurs / engagements',
                'gallery_highlights' => 'Galerie ambiance',
                'practical' => 'Contact & accès',
            ],
            'carte' => [
                'header' => 'En-tête de page',
                'menu_list' => 'Liste des catégories et plats',
            ],
            'galerie' => [
                'header' => 'En-tête',
                'gallery' => 'Grille galerie / état vide',
            ],
            'contact' => [
                'header' => 'En-tête',
                'feedback' => 'Messages de succès / erreurs',
                'form' => 'Formulaire de contact',
            ],
            'reservation' => [
                'feedback' => 'Messages de retour',
                'booking_form' => 'Formulaire de réservation',
            ],
            'reservation_manage' => [
                'header' => 'En-tête & messages',
                'summary' => 'Récapitulatif réservation',
                'actions' => 'Annulation / reprogrammation',
            ],
        ];
    }

    /** @return list<string> */
    public static function pages(): array
    {
        return array_keys(self::definitions());
    }

    /** @return list<string> */
    public static function keys(string $page): array
    {
        return array_keys(self::definitions()[$page] ?? []);
    }

    /** @return array<string, string> */
    public static function options(string $page): array
    {
        return self::definitions()[$page] ?? [];
    }

    /** @return list<string> */
    public static function defaultOrder(string $page): array
    {
        return self::keys($page);
    }
}
