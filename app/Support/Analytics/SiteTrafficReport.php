<?php

namespace App\Support\Analytics;

use App\Models\Restaurant;
use App\Models\SitePageView;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class SiteTrafficReport
{
    /** @var array<string, string> */
    public const ROUTE_LABELS = [
        'site.home' => 'Accueil',
        'site.carte' => 'Carte',
        'site.galerie' => 'Galerie',
        'site.contact' => 'Contact',
        'site.reservation' => 'Réservation',
        'site.posts.index' => 'Actualités (liste)',
        'site.posts.show' => 'Article',
        'site.legal' => 'Page légale',
    ];

    /**
     * @return array{
     *     days: int,
     *     period_label: string,
     *     total: int,
     *     previous_total: int,
     *     delta_pct: float|null,
     *     trend: 'up'|'down'|'flat'|'new',
     *     daily: list<array{date: string, label: string, label_short: string, count: int}>,
     *     top_paths: list<array{path: string, count: int, pct: float}>,
     *     by_route: list<array{route_name: string, label: string, count: int, pct: float, color: string}>,
     *     avg_per_day: float,
     *     peak: array{label: string, count: int}|null,
     *     charts: array<string, mixed>|null
     * }
     */
    public static function build(Restaurant $restaurant, int $days): array
    {
        $days = in_array($days, [7, 30, 90], true) ? $days : 7;

        $newest = CarbonImmutable::now();
        $oldest = $newest->startOfDay()->subDays($days - 1);

        $prevNewest = $oldest->subDay()->endOfDay();
        $prevOldest = $prevNewest->startOfDay()->subDays($days - 1);

        $base = SitePageView::query()->where('restaurant_id', $restaurant->id);

        $total = (int) (clone $base)
            ->where('viewed_at', '>=', $oldest)
            ->where('viewed_at', '<=', $newest)
            ->count();

        $previousTotal = (int) (clone $base)
            ->where('viewed_at', '>=', $prevOldest)
            ->where('viewed_at', '<=', $prevNewest)
            ->count();

        $deltaPct = null;
        $trend = 'flat';
        if ($previousTotal > 0) {
            $deltaPct = round((($total - $previousTotal) / $previousTotal) * 100, 1);
            if ($deltaPct > 0.5) {
                $trend = 'up';
            } elseif ($deltaPct < -0.5) {
                $trend = 'down';
            }
        } elseif ($total > 0) {
            $trend = 'new';
        }

        $dailyCounts = self::countsGroupedByDay(
            $restaurant->id,
            $oldest,
            $newest,
        );

        $prevDailyCounts = self::countsGroupedByDay(
            $restaurant->id,
            $prevOldest,
            $prevNewest,
        );

        $daily = [];
        $previousDailyData = [];
        for ($i = 0; $i < $days; $i++) {
            $day = $oldest->addDays($i);
            $key = $day->toDateString();
            $c = (int) ($dailyCounts->get($key, 0));
            $daily[] = [
                'date' => $key,
                'label' => $day->translatedFormat('l j F'),
                'label_short' => $day->translatedFormat('D j'),
                'count' => $c,
            ];

            $prevDay = $prevOldest->startOfDay()->addDays($i);
            $previousDailyData[] = (int) ($prevDailyCounts->get($prevDay->toDateString(), 0));
        }

        $topPathsRows = (clone $base)
            ->where('viewed_at', '>=', $oldest)
            ->where('viewed_at', '<=', $newest)
            ->selectRaw('path, COUNT(*) as c')
            ->groupBy('path')
            ->orderByDesc('c')
            ->limit(12)
            ->get();

        $topPaths = [];
        foreach ($topPathsRows as $row) {
            $topPaths[] = [
                'path' => (string) $row->path,
                'count' => (int) $row->c,
                'pct' => $total > 0 ? round(((int) $row->c / $total) * 100, 1) : 0.0,
            ];
        }

        $routeRows = (clone $base)
            ->where('viewed_at', '>=', $oldest)
            ->where('viewed_at', '<=', $newest)
            ->whereNotNull('route_name')
            ->selectRaw('route_name, COUNT(*) as c')
            ->groupBy('route_name')
            ->orderByDesc('c')
            ->get();

        $palette = ['#d97706', '#92400e', '#b45309', '#78350f', '#f59e0b', '#a16207', '#ca8a04', '#eab308', '#713f12', '#451a03'];
        $byRoute = [];
        $i = 0;
        foreach ($routeRows as $row) {
            $name = (string) $row->route_name;
            $byRoute[] = [
                'route_name' => $name,
                'label' => self::ROUTE_LABELS[$name] ?? $name,
                'count' => (int) $row->c,
                'pct' => $total > 0 ? round(((int) $row->c / $total) * 100, 1) : 0.0,
                'color' => $palette[$i % count($palette)],
            ];
            $i++;
        }

        $peakRow = collect($daily)->sortByDesc('count')->first();
        $peak = $peakRow && $peakRow['count'] > 0
            ? ['label' => $peakRow['label_short'], 'count' => $peakRow['count']]
            : null;

        $avgPerDay = $days > 0 ? round($total / $days, 1) : 0.0;

        $charts = null;
        if ($total > 0) {
            $labels = array_column($daily, 'label_short');
            $currentData = array_column($daily, 'count');

            $pathLabels = [];
            $pathFull = [];
            foreach ($topPaths as $p) {
                $path = $p['path'];
                $pathFull[] = $path;
                $pathLabels[] = mb_strlen($path) > 36 ? mb_substr($path, 0, 33).'…' : $path;
            }

            $charts = [
                'daily' => [
                    'labels' => $labels,
                    'datasets' => [
                        [
                            'label' => 'Période affichée',
                            'data' => $currentData,
                            'borderColor' => '#d97706',
                            'backgroundColor' => 'rgba(217, 119, 6, 0.18)',
                            'fill' => true,
                            'tension' => 0.35,
                            'pointRadius' => 3,
                            'pointHoverRadius' => 6,
                            'pointBackgroundColor' => '#d97706',
                        ],
                        [
                            'label' => 'Période précédente',
                            'data' => $previousDailyData,
                            'borderColor' => 'rgba(113, 113, 122, 0.9)',
                            'backgroundColor' => 'transparent',
                            'fill' => false,
                            'tension' => 0.35,
                            'borderDash' => [6, 4],
                            'pointRadius' => 2,
                            'pointHoverRadius' => 5,
                        ],
                    ],
                ],
                'routes' => $byRoute !== [] ? [
                    'labels' => array_column($byRoute, 'label'),
                    'data' => array_column($byRoute, 'count'),
                    'colors' => array_column($byRoute, 'color'),
                ] : null,
                'paths' => $topPaths !== [] ? [
                    'labels' => $pathLabels,
                    'fullLabels' => $pathFull,
                    'data' => array_column($topPaths, 'count'),
                ] : null,
            ];
        }

        return [
            'days' => $days,
            'period_label' => sprintf('%d jours (jusqu’à aujourd’hui)', $days),
            'total' => $total,
            'previous_total' => $previousTotal,
            'delta_pct' => $deltaPct,
            'trend' => $trend,
            'daily' => $daily,
            'top_paths' => $topPaths,
            'by_route' => $byRoute,
            'avg_per_day' => $avgPerDay,
            'peak' => $peak,
            'charts' => $charts,
        ];
    }

    /**
     * @return Collection<string, int>
     */
    private static function countsGroupedByDay(int $restaurantId, CarbonImmutable $from, CarbonImmutable $to): Collection
    {
        $expr = match (DB::connection()->getDriverName()) {
            'sqlite' => "strftime('%Y-%m-%d', viewed_at)",
            default => 'DATE(viewed_at)',
        };

        $rows = SitePageView::query()
            ->where('restaurant_id', $restaurantId)
            ->where('viewed_at', '>=', $from)
            ->where('viewed_at', '<=', $to)
            ->selectRaw("{$expr} as day, COUNT(*) as c")
            ->groupByRaw($expr)
            ->orderBy('day')
            ->get();

        return $rows->mapWithKeys(fn ($r): array => [(string) $r->day => (int) $r->c]);
    }
}
