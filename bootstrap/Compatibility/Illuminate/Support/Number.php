<?php

namespace Illuminate\Support;

use Illuminate\Support\Traits\Macroable;

/**
 * Remplacement minimal de Illuminate\Support\Number lorsque ext-intl est absent.
 * Les montants et nombres sont formatés de façon stable (fr : virgule, espace fine insécable).
 */
class Number
{
    use Macroable;

    protected static string $locale = 'en';

    protected static string $currency = 'USD';

    public static function format(int|float $number, ?int $precision = null, ?int $maxPrecision = null, ?string $locale = null): string|false
    {
        unset($locale);

        $n = (float) $number;

        if ($maxPrecision !== null) {
            $decimals = $maxPrecision;
        } elseif ($precision !== null) {
            $decimals = $precision;
        } else {
            $decimals = abs($n - round($n)) < 0.0000001 ? 0 : 2;
        }

        return number_format($n, (int) $decimals, ',', "\u{202f}");
    }

    public static function parse(string $string, ?int $type = null, ?string $locale = null): int|float|false
    {
        unset($type, $locale);

        $normalized = str_replace([' ', "\u{202f}", "\u{00a0}"], '', $string);
        $normalized = str_replace(',', '.', $normalized);

        if (! is_numeric($normalized)) {
            return false;
        }

        return (float) $normalized;
    }

    public static function parseInt(string $string, ?string $locale = null): int|false
    {
        $v = self::parse($string, null, $locale);

        return $v === false ? false : (int) $v;
    }

    public static function parseFloat(string $string, ?string $locale = null): float|false
    {
        $v = self::parse($string, null, $locale);

        return $v === false ? false : (float) $v;
    }

    public static function spell(int|float $number, ?string $locale = null, ?int $after = null, ?int $until = null)
    {
        if (! is_null($after) && $number <= $after) {
            return static::format($number, locale: $locale);
        }

        if (! is_null($until) && $number >= $until) {
            return static::format($number, locale: $locale);
        }

        return (string) $number;
    }

    public static function ordinal(int|float $number, ?string $locale = null)
    {
        unset($locale);

        return (string) (int) $number;
    }

    public static function spellOrdinal(int|float $number, ?string $locale = null)
    {
        unset($locale);

        return (string) (int) $number;
    }

    public static function percentage(int|float $number, int $precision = 0, ?int $maxPrecision = null, ?string $locale = null)
    {
        return static::format($number, $precision, $maxPrecision, $locale).' %';
    }

    public static function currency(int|float $number, string $in = '', ?string $locale = null, ?int $precision = null)
    {
        unset($locale);

        $code = ! empty($in) ? $in : static::$currency;
        $decimals = $precision ?? 2;
        $formatted = number_format((float) $number, (int) $decimals, ',', "\u{202f}");

        $symbol = match (strtoupper($code)) {
            'EUR' => '€',
            'USD' => '$',
            'GBP' => '£',
            'CHF' => 'CHF',
            default => $code,
        };

        if (in_array(strtoupper($code), ['EUR', 'CHF'], true)) {
            return $formatted."\u{00a0}".$symbol;
        }

        return $symbol.$formatted;
    }

    public static function fileSize(int|float $bytes, int $precision = 0, ?int $maxPrecision = null)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

        $unitCount = count($units);

        for ($i = 0; ($bytes / 1024) > 0.9 && ($i < $unitCount - 1); $i++) {
            $bytes /= 1024;
        }

        return sprintf('%s %s', static::format($bytes, $precision, $maxPrecision), $units[$i]);
    }

    public static function abbreviate(int|float $number, int $precision = 0, ?int $maxPrecision = null)
    {
        return static::forHumans($number, $precision, $maxPrecision, abbreviate: true);
    }

    public static function forHumans(int|float $number, int $precision = 0, ?int $maxPrecision = null, bool $abbreviate = false)
    {
        return static::summarize($number, $precision, $maxPrecision, $abbreviate ? [
            3 => 'K',
            6 => 'M',
            9 => 'B',
            12 => 'T',
            15 => 'Q',
        ] : [
            3 => ' thousand',
            6 => ' million',
            9 => ' billion',
            12 => ' trillion',
            15 => ' quadrillion',
        ]);
    }

    protected static function summarize(int|float $number, int $precision = 0, ?int $maxPrecision = null, array $units = [])
    {
        if (empty($units)) {
            $units = [
                3 => 'K',
                6 => 'M',
                9 => 'B',
                12 => 'T',
                15 => 'Q',
            ];
        }

        switch (true) {
            case (float) $number === 0.0:
                return $precision > 0 ? static::format(0, $precision, $maxPrecision) : '0';
            case $number < 0:
                return sprintf('-%s', static::summarize(abs($number), $precision, $maxPrecision, $units));
            case $number >= 1e15:
                return sprintf('%s'.end($units), static::summarize($number / 1e15, $precision, $maxPrecision, $units));
        }

        $numberExponent = floor(log10($number));
        $displayExponent = $numberExponent - ($numberExponent % 3);
        $number /= pow(10, $displayExponent);

        return trim(sprintf('%s%s', static::format($number, $precision, $maxPrecision), $units[$displayExponent] ?? ''));
    }

    public static function clamp(int|float $number, int|float $min, int|float $max)
    {
        return min(max($number, $min), $max);
    }

    public static function pairs(int|float $to, int|float $by, int|float $start = 0, int|float $offset = 1)
    {
        $output = [];

        for ($lower = $start; $lower < $to; $lower += $by) {
            $upper = $lower + $by - $offset;

            if ($upper > $to) {
                $upper = $to;
            }

            $output[] = [$lower, $upper];
        }

        return $output;
    }

    public static function trim(int|float $number)
    {
        return json_decode(json_encode($number));
    }

    public static function withLocale(string $locale, callable $callback)
    {
        $previousLocale = static::$locale;

        static::useLocale($locale);

        try {
            return $callback();
        } finally {
            static::useLocale($previousLocale);
        }
    }

    public static function withCurrency(string $currency, callable $callback)
    {
        $previousCurrency = static::$currency;

        static::useCurrency($currency);

        try {
            return $callback();
        } finally {
            static::useCurrency($previousCurrency);
        }
    }

    public static function useLocale(string $locale)
    {
        static::$locale = $locale;
    }

    public static function useCurrency(string $currency)
    {
        static::$currency = $currency;
    }

    public static function defaultLocale()
    {
        return static::$locale;
    }

    public static function defaultCurrency()
    {
        return static::$currency;
    }
}
