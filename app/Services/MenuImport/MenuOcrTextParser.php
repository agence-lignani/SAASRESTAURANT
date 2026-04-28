<?php

namespace App\Services\MenuImport;

/**
 * Transforme le texte brut OCR en structure « categories / items » (heuristiques FR).
 */
final class MenuOcrTextParser
{
    private const PRICE_TOKEN = '(\d{1,3}[.,]\d{1,2})\s*€?';

    /**
     * @return list<array{name: string, description: null, items: list<array{name: string, price: ?string, description: string}>}>
     */
    public function categoriesFromText(string $text): array
    {
        $normalized = $this->normalizeText($text);
        $lines = preg_split('/\R/u', $normalized) ?: [];

        $categories = [];
        $current = $this->blankCategory('Carte');

        foreach ($lines as $line) {
            $line = trim(preg_replace('/\s+/u', ' ', $line) ?? '');
            if ($line === '') {
                continue;
            }

            if ($this->looksLikeCategoryHeader($line)) {
                if ($this->categoryHasContent($current)) {
                    $categories[] = $current;
                }
                $current = $this->blankCategory($this->normalizeCategoryTitle($line));

                continue;
            }

            // Un seul prix : formats « nom … 12,00 » ou « 12,00 - nom ». Plusieurs prix → découpage par segment.
            $singleLine = $this->countPriceTokens($line) === 1 ? $this->tryParsePricedLine($line) : null;
            if ($singleLine !== null) {
                $current['items'][] = $singleLine;

                continue;
            }

            $segments = $this->extractPricedItemsFromLine($line);
            if ($segments !== []) {
                foreach ($segments as $item) {
                    $current['items'][] = $item;
                }

                continue;
            }

            if (mb_strlen($line) >= 2) {
                $current['items'][] = [
                    'name' => $line,
                    'price' => null,
                    'description' => '',
                ];
            }
        }

        if ($this->categoryHasContent($current) || $categories === []) {
            $categories[] = $current;
        }

        $categories = array_values(array_filter($categories, fn (array $c): bool => $this->categoryHasContent($c)));

        if ($categories === []) {
            return [$this->blankCategory('Extrait OCR')];
        }

        return $categories;
    }

    private function normalizeText(string $text): string
    {
        $text = str_replace(["\xC2\xA0"], ' ', $text);

        return trim($text);
    }

    /**
     * @return array{name: string, description: null, items: list<array{name: string, price: ?string, description: string}>}
     */
    private function blankCategory(string $name): array
    {
        return [
            'name' => $name,
            'description' => null,
            'items' => [],
        ];
    }

    private function categoryHasContent(array $category): bool
    {
        return ($category['items'] ?? []) !== [];
    }

    private function looksLikeCategoryHeader(string $line): bool
    {
        if ($this->countPriceTokens($line) === 1 && $this->tryParsePricedLine($line) !== null) {
            return false;
        }

        if ($this->extractPricedItemsFromLine($line) !== []) {
            return false;
        }

        if (mb_strlen($line) > 55) {
            return false;
        }

        if (preg_match('/^(entrées?|entrees?|entrée|plats?|plat|desserts?|dessert|boissons?|boisson|fromages?|formules?|menus?|carte|hors d[’\']?œuvre|hors d\'oeuvre|spécialités?|specialites?)\b/iu', $line)) {
            return true;
        }

        if (preg_match('/:\s*$/u', $line)) {
            return true;
        }

        $letters = preg_replace('/[^\p{L}]/u', '', $line) ?? '';
        $upper = mb_strtoupper($line, 'UTF-8');
        if ($letters !== '' && mb_strlen($line) <= 36 && $line === $upper && preg_match('/\p{L}/u', $line)) {
            return true;
        }

        return false;
    }

    private function normalizeCategoryTitle(string $line): string
    {
        $t = rtrim($line, " \t.:;—–-");

        return $t !== '' ? $t : 'Section';
    }

    private function countPriceTokens(string $line): int
    {
        return preg_match_all('/\d{1,3}[.,]\d{1,2}/u', $line) ?: 0;
    }

    /**
     * Découpe une ligne sur chaque prix (xx,xx / xx.xx) : plusieurs plats sur une même ligne OCR.
     *
     * @return list<array{name: string, price: ?string, description: string}>
     */
    private function extractPricedItemsFromLine(string $line): array
    {
        if (! preg_match('/\d{1,3}[.,]\d{1,2}/u', $line)) {
            return [];
        }

        $parts = preg_split('/'.self::PRICE_TOKEN.'/u', $line, -1, PREG_SPLIT_DELIM_CAPTURE);
        if ($parts === false) {
            return [];
        }

        $items = [];
        $i = 0;
        $n = count($parts);

        while ($i < $n) {
            if ($i + 1 >= $n) {
                $tail = trim((string) $parts[$i]);
                if ($tail !== '' && $i > 0) {
                    $items[] = $this->makeItem($tail, null);
                }

                break;
            }

            $textBefore = trim((string) $parts[$i]);
            $priceChunk = trim((string) $parts[$i + 1]);

            if (! preg_match('/^(\d{1,3}[.,]\d{1,2})$/u', $priceChunk, $pm)) {
                break;
            }

            $price = $this->normalizePrice($pm[1]);

            if ($textBefore !== '') {
                $items[] = $this->makeItem($textBefore, $price);
                $i += 2;

                continue;
            }

            if ($i + 2 < $n) {
                $textAfter = trim((string) $parts[$i + 2]);
                if ($textAfter !== '' && ! preg_match('/^(\d{1,3}[.,]\d{1,2})$/u', $textAfter)) {
                    $items[] = $this->makeItem($textAfter, $price);
                    $i += 3;

                    continue;
                }
            }

            $i += 2;
        }

        return $items;
    }

    /**
     * @return array{name: string, price: string, description: string}|null
     */
    private function tryParsePricedLine(string $line): ?array
    {
        if (preg_match('/^(.+?)\s+(\d{1,3}[.,]\d{1,2})\s*€?\s*$/u', $line, $m)) {
            return $this->makeItem(trim($m[1]), $this->normalizePrice($m[2]));
        }

        if (preg_match('/^(\d{1,3}[.,]\d{1,2})\s*€?\s*[-–—:]\s*(.+)$/u', $line, $m)) {
            return $this->makeItem(trim($m[2]), $this->normalizePrice($m[1]));
        }

        return null;
    }

    /**
     * @return array{name: string, price: ?string, description: string}
     */
    private function makeItem(string $name, ?string $price): array
    {
        return [
            'name' => $this->cleanupDishName($name),
            'price' => $price,
            'description' => '',
        ];
    }

    private function cleanupDishName(string $name): string
    {
        $name = trim($name);
        $name = preg_replace('/\s*[-–—]\s*$/u', '', $name) ?? $name;

        return trim($name);
    }

    private function normalizePrice(string $p): string
    {
        $n = str_replace(',', '.', $p);

        return is_numeric($n) ? number_format((float) $n, 2, '.', '') : $p;
    }
}
