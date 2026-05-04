<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Support\SiteContent\HomeSectionCatalog;
use App\Support\SiteContent\SiteContentDefaults;
use App\Support\SiteContent\SiteContentResolver;
use App\Theme\BistroManifest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(Request $request): View
    {
        /** @var Restaurant $restaurant */
        $restaurant = $request->attributes->get('restaurant');

        $restaurant->load(['openingHours', 'pageContent']);

        $manifest = BistroManifest::all();
        $cssVars = BistroManifest::cssVariablesForRestaurant($restaurant);

        $siteContent = SiteContentResolver::forRestaurant($restaurant);
        $content = $siteContent['home'];
        $defaultsHome = SiteContentDefaults::forRestaurant($restaurant)['home'];
        foreach ($defaultsHome as $sectionKey => $defaultBlock) {
            if (! array_key_exists($sectionKey, $content) || $content[$sectionKey] === null) {
                $content[$sectionKey] = $defaultBlock;
            }
        }
        $order = $content['section_order'] ?? [];
        if (! is_array($order) || $order === []) {
            $content['section_order'] = HomeSectionCatalog::defaultOrder();
        }
        $content['hero']['image_url'] = $this->resolveHeroImageUrl($content['hero']['image_url'] ?? null);
        if (isset($content['carte_narrative']) && is_array($content['carte_narrative'])) {
            $content['carte_narrative']['image_url'] = $this->resolveHeroImageUrl($content['carte_narrative']['image_url'] ?? null);
        }
        if (isset($content['manifesto']) && is_array($content['manifesto'])) {
            $content['manifesto']['image_url'] = $this->resolveHeroImageUrl($content['manifesto']['image_url'] ?? null);
        }
        if (isset($content['espaces']) && is_array($content['espaces'])) {
            $content['espaces']['image_url'] = $this->resolveHeroImageUrl($content['espaces']['image_url'] ?? null);
        }
        if (isset($content['spotlight']) && is_array($content['spotlight'])) {
            $content['spotlight']['image_url'] = $this->resolveHeroImageUrl($content['spotlight']['image_url'] ?? null);
        }
        if (isset($content['menus']['items']) && is_array($content['menus']['items'])) {
            $content['menus']['items'] = array_map(function (mixed $item): mixed {
                if (! is_array($item)) {
                    return $item;
                }

                $item['image_url'] = $this->resolveHeroImageUrl($item['image_url'] ?? null);

                return $item;
            }, $content['menus']['items']);
        }
        if (isset($content['gallery_highlights']['items']) && is_array($content['gallery_highlights']['items'])) {
            $content['gallery_highlights']['items'] = array_map(function (mixed $item): mixed {
                if (! is_array($item)) {
                    return $item;
                }

                $item['image_url'] = $this->resolveHeroImageUrl($item['image_url'] ?? null);

                return $item;
            }, $content['gallery_highlights']['items']);
        }

        if (isset($content['practical']['opening_lines']) && is_array($content['practical']['opening_lines'])) {
            $content['practical']['opening_lines'] = array_values(array_filter(array_map(
                fn (mixed $line): ?string => is_array($line)
                    ? (filled($line['line'] ?? null) ? (string) $line['line'] : null)
                    : (filled($line) ? (string) $line : null),
                $content['practical']['opening_lines']
            )));
        }

        if (isset($content['practical']['footer_meta_lines']) && is_array($content['practical']['footer_meta_lines'])) {
            $content['practical']['footer_meta_lines'] = array_values(array_filter(array_map(
                fn (mixed $line): ?string => is_array($line)
                    ? (filled($line['line'] ?? null) ? (string) $line['line'] : null)
                    : (filled($line) ? (string) $line : null),
                $content['practical']['footer_meta_lines']
            )));
        }

        $content['practical'] = [
            'heading' => $content['practical']['heading'] ?? 'Venir au restaurant',
            'contact_title' => $content['practical']['contact_title'] ?? 'Nous joindre',
            'opening_title' => $content['practical']['opening_title'] ?? 'Horaires d’ouverture',
            'footer_map_label' => $content['practical']['footer_map_label'] ?? 'Site map',
            'footer_find_label' => $content['practical']['footer_find_label'] ?? 'Find us',
            'footer_hours_label' => $content['practical']['footer_hours_label'] ?? 'Hours',
            'footer_meta_lines' => $content['practical']['footer_meta_lines'] ?? [],
            'contact_lines' => array_values(array_filter([
                $restaurant->contact_phone,
                $restaurant->contact_email,
                $restaurant->address_line1
                    ? trim(implode(', ', array_filter([
                        $restaurant->address_line1,
                        $restaurant->address_line2,
                        trim(($restaurant->postal_code ?? '').' '.($restaurant->city ?? '')),
                        $restaurant->country,
                    ])))
                    : null,
            ])),
            'opening_lines' => $content['practical']['opening_lines'] ?? $this->formatOpeningHours($restaurant),
        ];

        return view('site.home', [
            'manifest' => $manifest,
            'cssVars' => $cssVars,
            'content' => $content,
            'bistroFontStylesheet' => null,
        ]);
    }

    private function resolveHeroImageUrl(?string $value): ?string
    {
        if (! filled($value)) {
            return null;
        }

        if (Str::startsWith($value, ['http://', 'https://', '//', '/'])) {
            return $value;
        }

        return Storage::disk('public')->url($value);
    }

    /**
     * @return list<string>
     */
    private function formatOpeningHours(Restaurant $restaurant): array
    {
        $labels = ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'];
        $lines = [];

        foreach ($restaurant->openingHours as $hour) {
            $label = $labels[$hour->day_of_week] ?? (string) $hour->day_of_week;

            if ($hour->is_closed) {
                $lines[] = $label.' · Fermé';

                continue;
            }

            $open = $hour->opens_at;
            $close = $hour->closes_at;
            $openStr = $open ? (string) $open : '—';
            $closeStr = $close ? (string) $close : '—';
            $lines[] = $label.' · '.$openStr.' – '.$closeStr;
        }

        return $lines;
    }
}
