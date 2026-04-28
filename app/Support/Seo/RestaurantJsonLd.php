<?php

namespace App\Support\Seo;

use App\Models\OpeningHour;
use App\Models\Restaurant;

/**
 * JSON-LD Restaurant (schema.org) — F15.
 *
 * @return array<string, mixed>
 */
final class RestaurantJsonLd
{
    public static function forRestaurant(Restaurant $restaurant): array
    {
        $restaurant->loadMissing(['openingHours']);

        $openingHoursSpecification = [];
        foreach ($restaurant->openingHours ?? [] as $hour) {
            if (! $hour instanceof OpeningHour) {
                continue;
            }
            if ($hour->is_closed || blank($hour->opens_at) || blank($hour->closes_at)) {
                continue;
            }
            $openingHoursSpecification[] = [
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => self::schemaDay($hour->day_of_week),
                'opens' => substr((string) $hour->opens_at, 0, 5),
                'closes' => substr((string) $hour->closes_at, 0, 5),
            ];
        }

        $street = trim(implode(', ', array_filter([
            $restaurant->address_line1,
            $restaurant->address_line2,
        ])));

        $address = array_filter([
            '@type' => 'PostalAddress',
            'streetAddress' => $street !== '' ? $street : null,
            'addressLocality' => $restaurant->city,
            'postalCode' => $restaurant->postal_code,
            'addressCountry' => $restaurant->country,
        ]);

        $payload = [
            '@context' => 'https://schema.org',
            '@type' => 'Restaurant',
            'name' => $restaurant->name,
            'url' => url('/'),
        ];

        if (filled($restaurant->tagline)) {
            $payload['description'] = $restaurant->tagline;
        }
        if (filled($restaurant->contact_email)) {
            $payload['email'] = $restaurant->contact_email;
        }
        if (filled($restaurant->contact_phone)) {
            $payload['telephone'] = $restaurant->contact_phone;
        }
        if ($address !== []) {
            $payload['address'] = $address;
        }
        if ($openingHoursSpecification !== []) {
            $payload['openingHoursSpecification'] = $openingHoursSpecification;
        }

        return $payload;
    }

    /** @param  int  $dow  0 = dimanche (PHP date('w')) … 6 = samedi */
    private static function schemaDay(int $dow): string
    {
        return match ($dow) {
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            default => 'Monday',
        };
    }
}
