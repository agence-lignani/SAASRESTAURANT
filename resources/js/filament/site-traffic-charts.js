import { Chart, registerables } from 'chart.js';
import zoomPlugin from 'chartjs-plugin-zoom';

Chart.register(...registerables, zoomPlugin);

/** @type {{ daily: Chart|null, routes: Chart|null, paths: Chart|null }} */
const instances = { daily: null, routes: null, paths: null };

function isDark() {
    return document.documentElement.classList.contains('dark');
}

function chartColors() {
    const dark = isDark();

    return {
        text: dark ? 'rgb(212 212 216)' : 'rgb(63 63 70)',
        grid: dark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.06)',
        border: dark ? 'rgba(255,255,255,0.12)' : 'rgba(0,0,0,0.08)',
        primary: '#d97706',
        primaryMuted: dark ? 'rgba(245, 158, 11, 0.35)' : 'rgba(217, 119, 6, 0.2)',
        secondary: dark ? 'rgba(161, 161, 170, 0.9)' : 'rgba(82, 82, 91, 0.85)',
    };
}

function destroyCharts() {
    Object.keys(instances).forEach((key) => {
        const c = instances[key];
        if (c) {
            try {
                c.destroy();
            } catch {
                //
            }
            instances[key] = null;
        }
    });
}

function readPayload() {
    const el = document.getElementById('traffic-charts-json');
    if (!el?.textContent?.trim()) {
        return null;
    }
    try {
        return JSON.parse(el.textContent);
    } catch {
        return null;
    }
}

function initSiteTrafficCharts() {
    const payload = readPayload();
    destroyCharts();

    if (!payload) {
        return;
    }

    const colors = chartColors();

    if (payload.daily?.labels?.length && document.getElementById('site-traffic-daily-canvas')) {
        instances.daily = new Chart(document.getElementById('site-traffic-daily-canvas'), {
            type: 'line',
            data: {
                labels: payload.daily.labels,
                datasets: payload.daily.datasets,
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { color: colors.text, usePointStyle: true, boxWidth: 8 },
                    },
                    tooltip: {
                        callbacks: {
                            label(ctx) {
                                const v = ctx.parsed.y;

                                return `${ctx.dataset.label ?? ''}: ${v == null ? '—' : v} vues`;
                            },
                        },
                    },
                    zoom: {
                        limits: { x: { minRange: 2 } },
                        pan: {
                            enabled: true,
                            mode: 'x',
                            modifierKey: 'shift',
                        },
                        zoom: {
                            wheel: { enabled: true },
                            pinch: { enabled: true },
                            drag: { enabled: true, modifierKey: 'ctrl' },
                            mode: 'x',
                        },
                    },
                },
                scales: {
                    x: {
                        ticks: { color: colors.text, maxRotation: 45, minRotation: 0 },
                        grid: { color: colors.grid },
                        border: { color: colors.border },
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { color: colors.text, precision: 0, stepSize: 1 },
                        grid: { color: colors.grid },
                        border: { color: colors.border },
                    },
                },
            },
        });
    }

    if (payload.routes?.labels?.length && document.getElementById('site-traffic-routes-canvas')) {
        instances.routes = new Chart(document.getElementById('site-traffic-routes-canvas'), {
            type: 'doughnut',
            data: {
                labels: payload.routes.labels,
                datasets: [
                    {
                        data: payload.routes.data,
                        backgroundColor: payload.routes.colors,
                        borderWidth: 1,
                        borderColor: isDark() ? 'rgb(24 24 27)' : 'rgb(255 255 255)',
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: { color: colors.text, usePointStyle: true, boxWidth: 8 },
                    },
                    tooltip: {
                        callbacks: {
                            label(ctx) {
                                const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                const v = ctx.parsed;
                                const pct = total ? Math.round((v / total) * 1000) / 10 : 0;

                                return `${ctx.label}: ${v} (${pct} %)`;
                            },
                        },
                    },
                },
            },
        });
    }

    if (payload.paths?.labels?.length && document.getElementById('site-traffic-paths-canvas')) {
        instances.paths = new Chart(document.getElementById('site-traffic-paths-canvas'), {
            type: 'bar',
            data: {
                labels: payload.paths.labels,
                datasets: [
                    {
                        label: 'Vues',
                        data: payload.paths.data,
                        backgroundColor: colors.primaryMuted,
                        borderColor: colors.primary,
                        borderWidth: 1,
                        borderRadius: 6,
                    },
                ],
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            title(items) {
                                const i = items[0]?.dataIndex;

                                return payload.paths.fullLabels[i] ?? '';
                            },
                        },
                    },
                    zoom: {
                        limits: { y: { minRange: 2 } },
                        pan: { enabled: true, mode: 'y', modifierKey: 'shift' },
                        zoom: {
                            wheel: { enabled: true },
                            pinch: { enabled: true },
                            mode: 'y',
                        },
                    },
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { color: colors.text, precision: 0 },
                        grid: { color: colors.grid },
                        border: { color: colors.border },
                    },
                    y: {
                        ticks: { color: colors.text },
                        grid: { display: false },
                        border: { color: colors.border },
                    },
                },
            },
        });
    }
}

function scheduleInit() {
    queueMicrotask(() => initSiteTrafficCharts());
}

document.addEventListener('DOMContentLoaded', scheduleInit);
document.addEventListener('livewire:navigated', scheduleInit);

document.addEventListener('livewire:init', () => {
    const lw = window.Livewire;
    if (!lw?.hook) {
        return;
    }
    lw.hook('morph.updated', () => {
        if (document.getElementById('traffic-charts-json')) {
            scheduleInit();
        }
    });
});
