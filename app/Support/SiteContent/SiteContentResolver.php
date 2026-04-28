<?php

namespace App\Support\SiteContent;

use App\Models\Restaurant;

class SiteContentResolver
{
    /**
     * @return array<string, mixed>
     */
    public static function forRestaurant(Restaurant $restaurant): array
    {
        $defaults = SiteContentDefaults::forRestaurant($restaurant);
        $custom = $restaurant->pageContent?->content;

        if (! is_array($custom) || $custom === []) {
            return SiteContentNormalizer::normalize($defaults);
        }

        return SiteContentNormalizer::normalize(self::merge($defaults, $custom));
    }

    /**
     * @param  array<string, mixed>  $base
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private static function merge(array $base, array $overrides): array
    {
        foreach ($overrides as $key => $value) {
            if (is_array($value) && isset($base[$key]) && is_array($base[$key])) {
                $base[$key] = self::merge($base[$key], $value);

                continue;
            }

            if ($value !== null) {
                $base[$key] = $value;
            }
        }

        return $base;
    }
}
