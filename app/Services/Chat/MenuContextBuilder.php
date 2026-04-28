<?php

namespace App\Services\Chat;

use App\Models\Restaurant;

/**
 * Construit un contexte texte à partir du menu MySQL du tenant courant uniquement.
 */
final class MenuContextBuilder
{
    public function build(Restaurant $restaurant): string
    {
        $restaurant->load([
            'menuCategories' => fn ($q) => $q->orderBy('sort_order'),
            'menuCategories.menuItems' => fn ($q) => $q->orderBy('sort_order'),
        ]);

        $lines = [];

        foreach ($restaurant->menuCategories as $category) {
            $lines[] = '## '.$category->name;
            if (filled($category->description)) {
                $lines[] = $category->description;
            }
            foreach ($category->menuItems as $item) {
                if (! $item->is_available) {
                    continue;
                }
                $price = $item->price !== null
                    ? number_format((float) $item->price, 2, ',', ' ').' '.$item->currency
                    : 'prix sur place';
                $lines[] = '- '.$item->name.' — '.$price;
                if (filled($item->description)) {
                    $lines[] = '  Description : '.$item->description;
                }
                $allergens = is_array($item->allergens) && $item->allergens !== []
                    ? implode(', ', $item->allergens)
                    : null;
                $diets = is_array($item->dietary_flags) && $item->dietary_flags !== []
                    ? implode(', ', $item->dietary_flags)
                    : null;
                if ($allergens) {
                    $lines[] = '  Allergènes (indicatifs) : '.$allergens;
                }
                if ($diets) {
                    $lines[] = '  Régimes / repères : '.$diets;
                }
            }
            $lines[] = '';
        }

        $text = trim(implode("\n", $lines));

        return $text === '' ? '(Aucun plat disponible dans la carte pour le moment.)' : $text;
    }
}
