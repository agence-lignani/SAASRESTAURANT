<?php

namespace App\Support\SiteContent;

/**
 * Harmonise les structures de contenu (anciens champs vs repeaters, etc.).
 *
 * Les clés de premier niveau et les sections de pages sont restreintes aux schémas connus :
 * seule une évolution en code peut ajouter une nouvelle page ou une nouvelle section.
 *
 * @param  array<string, mixed>  $content
 * @return array<string, mixed>
 */
final class SiteContentNormalizer
{
    /** @var list<string> */
    private const ROOT_PAGE_KEYS = ['home', 'carte', 'galerie', 'contact', 'reservation', 'reservation_manage'];

    public static function normalize(array $content): array
    {
        $content = self::filterKeysInOrder($content, self::ROOT_PAGE_KEYS);

        foreach (PageSectionCatalog::pages() as $page) {
            if (! isset($content[$page]) || ! is_array($content[$page])) {
                continue;
            }

            $content[$page] = self::normalizePage($page, $content[$page]);
        }

        return $content;
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  list<string>  $allowed
     * @return array<string, mixed>
     */
    private static function filterKeysInOrder(array $data, array $allowed): array
    {
        $out = [];

        foreach ($allowed as $key) {
            if (array_key_exists($key, $data)) {
                $out[$key] = $data[$key];
            }
        }

        return $out;
    }

    /**
     * @param  array<string, mixed>  $pageContent
     * @return array<string, mixed>
     */
    private static function normalizePage(string $page, array $pageContent): array
    {
        if ($page === 'home') {
            if (isset($pageContent['hero']) && is_array($pageContent['hero'])) {
                $pageContent['hero'] = self::normalizeHero($pageContent['hero']);
            }
            if (isset($pageContent['manifesto']) && is_array($pageContent['manifesto'])) {
                $pageContent['manifesto'] = self::normalizeManifesto($pageContent['manifesto']);
            }
            if (isset($pageContent['carte_narrative']) && is_array($pageContent['carte_narrative'])) {
                $pageContent['carte_narrative'] = self::normalizeCarteNarrative($pageContent['carte_narrative']);
            }
            if (isset($pageContent['reviews_widget']) && is_array($pageContent['reviews_widget'])) {
                $pageContent['reviews_widget'] = self::normalizeReviewsWidget($pageContent['reviews_widget']);
            }
        }

        $pageContent['section_order'] = self::normalizeSectionOrder($page, $pageContent['section_order'] ?? null);

        if ($page === 'home') {
            $blocks = self::filterKeysInOrder($pageContent, PageSectionCatalog::keys($page));

            return array_merge(
                ['section_order' => $pageContent['section_order']],
                $blocks,
            );
        }

        return $pageContent;
    }

    /**
     * @return list<string>
     */
    private static function normalizeSectionOrder(string $page, mixed $order): array
    {
        $canonical = PageSectionCatalog::keys($page);
        $default = PageSectionCatalog::defaultOrder($page);

        if (! is_array($order)) {
            return $default;
        }

        $order = self::flattenSectionOrderRows($order);

        $strings = [];
        foreach ($order as $k) {
            if (is_string($k) && in_array($k, $canonical, true)) {
                $strings[] = $k;
            }
        }

        if (count($strings) !== count($canonical)) {
            return $default;
        }

        if (count(array_unique($strings)) !== count($canonical)) {
            return $default;
        }

        $a = $strings;
        $b = $canonical;
        sort($a);
        sort($b);
        if ($a !== $b) {
            return $default;
        }

        return $strings;
    }

    /**
     * Le formulaire Filament stocke parfois l’ordre comme
     * [ ['section' => 'hero'], ['section' => 'manifesto'], … ] au lieu de ['hero', 'manifesto', …].
     *
     * @param  array<int, mixed>  $order
     * @return list<string>
     */
    private static function flattenSectionOrderRows(array $order): array
    {
        $out = [];
        foreach ($order as $item) {
            if (is_string($item) && $item !== '') {
                $out[] = $item;

                continue;
            }
            if (is_array($item) && isset($item['section']) && is_string($item['section']) && $item['section'] !== '') {
                $out[] = $item['section'];
            }
        }

        return $out;
    }

    /**
     * @param  array<string, mixed>  $hero
     * @return array<string, mixed>
     */
    private static function normalizeHero(array $hero): array
    {
        if (! empty($hero['cta_primary']) || ! empty($hero['cta_secondary'])) {
            $hero['cta_buttons'] = self::legacyCtaPair(
                is_array($hero['cta_primary'] ?? null) ? $hero['cta_primary'] : null,
                is_array($hero['cta_secondary'] ?? null) ? $hero['cta_secondary'] : null
            );
        }

        unset($hero['cta_primary'], $hero['cta_secondary']);

        if (! isset($hero['cta_buttons']) || ! is_array($hero['cta_buttons'])) {
            $hero['cta_buttons'] = [];
        }

        return $hero;
    }

    /**
     * @param  array<string, mixed>  $block
     * @return array<string, mixed>
     */
    private static function normalizeCarteNarrative(array $block): array
    {
        if (! empty($block['cta_primary']) || ! empty($block['cta_secondary'])) {
            $block['cta_buttons'] = self::legacyCtaPair(
                is_array($block['cta_primary'] ?? null) ? $block['cta_primary'] : null,
                is_array($block['cta_secondary'] ?? null) ? $block['cta_secondary'] : null
            );
        }

        unset($block['cta_primary'], $block['cta_secondary']);

        if (! isset($block['cta_buttons']) || ! is_array($block['cta_buttons'])) {
            $block['cta_buttons'] = [];
        }

        return $block;
    }

    /**
     * @param  array<string, mixed>|null  $primary
     * @param  array<string, mixed>|null  $secondary
     * @return list<array{label: string, href: string, variant: string}>
     */
    private static function legacyCtaPair(?array $primary, ?array $secondary): array
    {
        $out = [];

        if (is_array($primary)
            && filled($primary['label'] ?? null)
            && filled($primary['href'] ?? null)) {
            $out[] = [
                'label' => (string) $primary['label'],
                'href' => (string) $primary['href'],
                'variant' => 'primary',
            ];
        }

        if (is_array($secondary)
            && filled($secondary['label'] ?? null)
            && filled($secondary['href'] ?? null)) {
            $out[] = [
                'label' => (string) $secondary['label'],
                'href' => (string) $secondary['href'],
                'variant' => 'secondary',
            ];
        }

        return $out;
    }

    /**
     * @param  array<string, mixed>  $manifesto
     * @return array<string, mixed>
     */
    private static function normalizeManifesto(array $manifesto): array
    {
        if (filled($manifesto['more_href'] ?? null)) {
            $manifesto['more_links'] = [[
                'label' => (string) ($manifesto['more_label'] ?? ''),
                'href' => (string) $manifesto['more_href'],
            ]];
        }

        unset($manifesto['more_label'], $manifesto['more_href']);

        if (! isset($manifesto['more_links']) || ! is_array($manifesto['more_links'])) {
            $manifesto['more_links'] = [];
        }

        return $manifesto;
    }

    /**
     * @param  array<string, mixed>  $reviews
     * @return array<string, mixed>
     */
    private static function normalizeReviewsWidget(array $reviews): array
    {
        if (filled($reviews['cta_label'] ?? null) && filled($reviews['url'] ?? null)) {
            $reviews['cta_buttons'] = [[
                'label' => (string) $reviews['cta_label'],
                'href' => (string) $reviews['url'],
            ]];
        }

        unset($reviews['cta_label']);

        if (! isset($reviews['cta_buttons']) || ! is_array($reviews['cta_buttons'])) {
            $reviews['cta_buttons'] = [];
        }

        return $reviews;
    }
}
