<?php

namespace App\Filament\Concerns;

use App\Support\SiteContent\HomeSectionCatalog;

trait NormalizesHomeSectionOrderFormState
{
    /**
     * Le Repeater attend une liste de [ ['section' => 'hero'], ... ] ; en base on stocke ['hero', 'manifesto', ...].
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function inflateHomeSectionOrderRepeater(array $data): array
    {
        if (! isset($data['content']['home']['section_order'])) {
            return $data;
        }

        $order = $data['content']['home']['section_order'];
        if (! is_array($order) || $order === []) {
            return $data;
        }

        $first = reset($order);
        if (is_array($first) && array_key_exists('section', $first)) {
            return $data;
        }

        if (is_string($first)) {
            $data['content']['home']['section_order'] = array_map(
                fn (string $k): array => ['section' => $k],
                array_values($order)
            );
        }

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function flattenHomeSectionOrderRepeater(array $data): array
    {
        if (! isset($data['content']['home']['section_order']) || ! is_array($data['content']['home']['section_order'])) {
            return $data;
        }

        $order = $data['content']['home']['section_order'];
        $first = reset($order);

        if (is_array($first) && isset($first['section'])) {
            $flat = [];
            foreach ($order as $row) {
                if (is_array($row) && isset($row['section']) && is_string($row['section'])) {
                    $flat[] = $row['section'];
                }
            }
            if (count($flat) === count(HomeSectionCatalog::keys())) {
                $data['content']['home']['section_order'] = $flat;
            }
        }

        return $data;
    }
}
