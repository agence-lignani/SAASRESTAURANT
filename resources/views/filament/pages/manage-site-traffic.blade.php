@php
    $r = $this->report;
    $total = (int) ($r['total'] ?? 0);
    $prev = (int) ($r['previous_total'] ?? 0);
    $delta = $r['delta_pct'];
    $trend = $r['trend'] ?? 'flat';
    $daily = $r['daily'] ?? [];
    $topPaths = $r['top_paths'] ?? [];
    $byRoute = $r['by_route'] ?? [];
    $peak = $r['peak'] ?? null;
    $avg = $r['avg_per_day'] ?? 0;
    $charts = $r['charts'] ?? null;
@endphp

<x-filament-panels::page>
    <div class="space-y-8">
        {{-- Période --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="inline-flex rounded-xl bg-gray-950/5 p-1 ring-1 ring-gray-950/10 dark:bg-white/5 dark:ring-white/10">
                @foreach ([7 => '7 jours', 30 => '30 jours', 90 => '90 jours'] as $d => $label)
                    <button
                        type="button"
                        wire:click="setDays({{ $d }})"
                        wire:loading.attr="disabled"
                        @class([
                            'rounded-lg px-4 py-2 text-sm font-semibold transition',
                            'bg-white text-amber-950 shadow-sm ring-1 ring-gray-950/10 dark:bg-amber-500 dark:text-amber-950 dark:ring-0' => $this->days === $d,
                            'text-gray-600 hover:text-gray-950 dark:text-gray-400 dark:hover:text-white' => $this->days !== $d,
                        ])
                    >
                        {{ $label }}
                    </button>
                @endforeach
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ $r['period_label'] ?? '' }}
                @if ($prev > 0 && $delta !== null)
                    <span class="ml-1 font-medium text-gray-700 dark:text-gray-300">· période précédente : {{ number_format($prev, 0, ',', ' ') }} vues</span>
                @endif
            </p>
        </div>

        {{-- KPIs --}}
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-500 to-amber-700 p-5 text-white shadow-lg ring-1 ring-amber-600/30">
                <p class="text-xs font-semibold uppercase tracking-wider text-amber-100/90">Vues totales</p>
                <p class="mt-2 text-3xl font-bold tabular-nums tracking-tight">{{ number_format($total, 0, ',', ' ') }}</p>
                <p class="mt-1 text-sm text-amber-100">sur la période sélectionnée</p>
                <div class="pointer-events-none absolute -right-6 -top-6 h-24 w-24 rounded-full bg-white/10" aria-hidden="true"></div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Moyenne / jour</p>
                <p class="mt-2 text-3xl font-bold tabular-nums text-gray-950 dark:text-white">{{ number_format($avg, 1, ',', ' ') }}</p>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">répartition calendaire</p>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Pic d’affluence</p>
                @if ($peak)
                    <p class="mt-2 text-2xl font-bold capitalize text-gray-950 dark:text-white">{{ $peak['label'] }}</p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ number_format($peak['count'], 0, ',', ' ') }} vues ce jour-là</p>
                @else
                    <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">Pas encore assez de données.</p>
                @endif
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">vs période précédente</p>
                @if ($delta === null && $prev === 0 && $total > 0)
                    <p class="mt-2 text-2xl font-bold text-emerald-600 dark:text-emerald-400">Nouveau</p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">première activité sur cette fenêtre</p>
                @elseif ($delta === null)
                    <p class="mt-2 text-2xl font-bold text-gray-400">—</p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">pas de comparaison</p>
                @else
                    <p @class([
                        'mt-2 text-3xl font-bold tabular-nums',
                        'text-emerald-600 dark:text-emerald-400' => $trend === 'up',
                        'text-rose-600 dark:text-rose-400' => $trend === 'down',
                        'text-gray-600 dark:text-gray-300' => $trend === 'flat',
                        'text-sky-600 dark:text-sky-400' => $trend === 'new',
                    ])>
                        {{ $delta > 0 ? '+' : '' }}{{ number_format($delta, 1, ',', ' ') }} %
                    </p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">évolution du volume de pages vues</p>
                @endif
            </div>
        </div>

        @if ($total === 0)
            <x-filament::section>
                <x-slot name="heading">Activité par jour</x-slot>
                <p class="text-sm text-gray-500 dark:text-gray-400">Aucune visite enregistrée sur cette période. Parcourez le site public pour alimenter les statistiques.</p>
            </x-filament::section>
        @else
            <div wire:key="traffic-charts-{{ $this->days }}-{{ $total }}" class="space-y-8">
                <script type="application/json" id="traffic-charts-json">@json($charts)</script>

                <x-filament::section>
                    <x-slot name="heading">Activité par jour</x-slot>
                    <x-slot name="description">
                        Deux courbes : période affichée et période précédente (même nombre de jours). Interactions Chart.js :
                        <strong class="font-medium text-gray-800 dark:text-gray-200">molette</strong> zoom horizontal,
                        <strong class="font-medium text-gray-800 dark:text-gray-200">Maj + glisser</strong> panoramique,
                        <strong class="font-medium text-gray-800 dark:text-gray-200">Ctrl + glisser</strong> zoom par sélection.
                    </x-slot>
                    <div class="relative h-80 w-full rounded-xl bg-gray-950/[0.02] p-2 ring-1 ring-gray-950/5 dark:bg-white/[0.02] dark:ring-white/10 sm:p-4">
                        <canvas id="site-traffic-daily-canvas" class="max-h-full" aria-label="Graphique des vues par jour"></canvas>
                    </div>
                </x-filament::section>

                <div class="grid gap-6 lg:grid-cols-2">
                    <x-filament::section>
                        <x-slot name="heading">Répartition par page</x-slot>
                        <x-slot name="description">Donut interactif (infobulles avec part du total).</x-slot>

                        @if ($charts && ! empty($charts['routes']))
                            <div class="relative mx-auto h-72 max-w-md">
                                <canvas id="site-traffic-routes-canvas" aria-label="Répartition par type de page"></canvas>
                            </div>
                        @else
                            <p class="text-sm text-gray-500 dark:text-gray-400">Aucune donnée.</p>
                        @endif
                    </x-filament::section>

                    <x-filament::section>
                        <x-slot name="heading">Chemins les plus consultés</x-slot>
                        <x-slot name="description">Barres horizontales — molette ou pincement pour zoom vertical, Maj + glisser pour panoramique vertical.</x-slot>

                        @if ($charts && ! empty($charts['paths']))
                            <div class="relative h-80 w-full">
                                <canvas id="site-traffic-paths-canvas" aria-label="Top des chemins URL"></canvas>
                            </div>
                        @else
                            <p class="text-sm text-gray-500 dark:text-gray-400">Aucune donnée.</p>
                        @endif
                    </x-filament::section>
                </div>
            </div>
        @endif

        @if ($total > 0 && count($daily) > 0)
            <x-filament::section>
                <x-slot name="heading">Tableau des vues par jour</x-slot>
                <x-slot name="description">Valeurs brutes correspondant au graphique.</x-slot>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-white/10">
                                <th class="py-2 pr-4 font-medium text-gray-950 dark:text-white">Jour</th>
                                <th class="py-2 font-medium text-gray-950 dark:text-white">Vues</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($daily as $row)
                                <tr class="border-b border-gray-100 dark:border-white/5">
                                    <td class="py-2 pr-4 text-gray-700 dark:text-gray-300">{{ $row['label'] }}</td>
                                    <td class="py-2 tabular-nums text-gray-950 dark:text-white">{{ $row['count'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-filament::section>
        @endif

        <x-filament::section>
            <x-slot name="heading">Méthode de collecte</x-slot>
            <x-slot name="description">
                Une ligne est créée à chaque affichage réussi (HTTP 200) des pages marquées pour le suivi. Aucune donnée personnelle ni cookie publicitaire n’est stockée dans cette table. Graphiques : <a href="https://www.chartjs.org/" target="_blank" rel="noopener noreferrer" class="text-primary-600 underline dark:text-primary-400">Chart.js</a> + plugin zoom.
            </x-slot>
        </x-filament::section>
    </div>

    @vite(['resources/js/filament/site-traffic-charts.js'])
</x-filament-panels::page>
