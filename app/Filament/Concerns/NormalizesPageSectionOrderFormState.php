<?php

namespace App\Filament\Concerns;

use App\Support\SiteContent\PageSectionCatalog;

trait NormalizesPageSectionOrderFormState
{
    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function inflatePageSectionOrderRepeaters(array $data): array
    {
        foreach (PageSectionCatalog::pages() as $page) {
            $order = $data['content'][$page]['section_order'] ?? null;
            if (! is_array($order) || $order === []) {
                $order = PageSectionCatalog::defaultOrder($page);
            }

            $order = $this->normalizeSectionOrderForForm($page, $order);

            $first = reset($order);
            if (is_array($first) && array_key_exists('section', $first)) {
                $data['content'][$page]['section_order'] = $order;

                continue;
            }

            if (is_string($first)) {
                $data['content'][$page]['section_order'] = array_map(
                    fn (string $key): array => ['section' => $key],
                    array_values($order),
                );

                continue;
            }

            $data['content'][$page]['section_order'] = array_map(
                fn (string $key): array => ['section' => $key],
                PageSectionCatalog::defaultOrder($page),
            );
        }

        return $data;
    }

    /**
     * Garantit un ordre compatible avec le schéma courant (clés supprimées/ajoutées).
     *
     * @param  array<int, mixed>  $order
     * @return array<int, mixed>
     */
    private function normalizeSectionOrderForForm(string $page, array $order): array
    {
        $canonical = PageSectionCatalog::keys($page);
        $default = PageSectionCatalog::defaultOrder($page);

        $rows = [];
        foreach ($order as $item) {
            if (is_string($item) && in_array($item, $canonical, true) && ! in_array($item, $rows, true)) {
                $rows[] = $item;

                continue;
            }

            if (is_array($item)
                && isset($item['section'])
                && is_string($item['section'])
                && in_array($item['section'], $canonical, true)
                && ! in_array($item['section'], $rows, true)) {
                $rows[] = $item['section'];
            }
        }

        if ($rows === []) {
            return $default;
        }

        foreach ($default as $section) {
            if (! in_array($section, $rows, true)) {
                $rows[] = $section;
            }
        }

        return $rows;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function flattenPageSectionOrderRepeaters(array $data): array
    {
        foreach (PageSectionCatalog::pages() as $page) {
            $order = $data['content'][$page]['section_order'] ?? null;
            if (! is_array($order) || $order === []) {
                continue;
            }

            $first = reset($order);
            if (! (is_array($first) && isset($first['section']))) {
                continue;
            }

            $flat = [];
            foreach ($order as $row) {
                if (is_array($row) && isset($row['section']) && is_string($row['section'])) {
                    $flat[] = $row['section'];
                }
            }

            if (count($flat) === count(PageSectionCatalog::keys($page))) {
                $data['content'][$page]['section_order'] = $flat;
            }
        }

        return $data;
    }
}
