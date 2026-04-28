<?php

namespace App\Support;

/**
 * Allergènes / mentions réglementaires (base EU — F18).
 *
 * @see https://food.ec.europa.eu/safety/labelling-and-nutrition/food-information-consumers/legislation_en
 */
final class AllergenCatalog
{
    /**
     * @return array<string, string> clé => libellé FR
     */
    public static function options(): array
    {
        return [
            'gluten' => 'Gluten',
            'crustaceans' => 'Crustacés',
            'eggs' => 'Œufs',
            'fish' => 'Poisson',
            'peanuts' => 'Arachides',
            'soy' => 'Soja',
            'milk' => 'Lait',
            'nuts' => 'Fruits à coque',
            'celery' => 'Céleri',
            'mustard' => 'Moutarde',
            'sesame' => 'Sésame',
            'sulphites' => 'Anhydride sulfureux / sulfites',
            'lupin' => 'Lupin',
            'molluscs' => 'Mollusques',
        ];
    }
}
