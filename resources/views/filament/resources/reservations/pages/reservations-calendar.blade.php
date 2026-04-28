<x-filament-panels::page>
    <style>
        .rsv-calendar { display: grid; gap: 1rem; }
        .rsv-toolbar, .rsv-card { background: #fff; border: 1px solid #d7dde6; border-radius: 12px; }
        .rsv-toolbar { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; padding: .75rem 1rem; gap: .75rem; }
        .rsv-nav-btn { border: 1px solid #c7d0dc; border-radius: 10px; background: #fff; color: #1f2937; font-weight: 600; padding: .5rem .75rem; min-height: 44px; cursor: pointer; }
        .rsv-nav-btn:hover { background: #f8fafc; }
        .rsv-month-label { margin: 0; text-transform: capitalize; font-size: 1rem; font-weight: 700; color: #0f172a; }
        .rsv-layout { display: grid; gap: 1rem; grid-template-columns: 2fr 1fr; }
        .rsv-grid-days, .rsv-grid-cells { display: grid; grid-template-columns: repeat(7, minmax(0, 1fr)); }
        .rsv-grid-days { border-bottom: 1px solid #d7dde6; }
        .rsv-grid-days div { text-align: center; font-size: .75rem; font-weight: 700; letter-spacing: .03em; text-transform: uppercase; color: #64748b; padding: .5rem .25rem; }
        .rsv-day-cell { min-height: 105px; border-right: 1px solid #d7dde6; border-bottom: 1px solid #d7dde6; padding: .5rem; text-align: left; background: #fff; cursor: pointer; }
        .rsv-day-cell:hover { background: #f8fafc; }
        .rsv-day-cell.is-other-month { opacity: .48; }
        .rsv-day-cell.is-selected { background: #ecfeff; box-shadow: inset 0 0 0 1px #22d3ee; }
        .rsv-day-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: .4rem; }
        .rsv-day-num { font-size: .9rem; font-weight: 700; color: #111827; }
        .rsv-total-pill { display: inline-block; font-size: .7rem; font-weight: 700; border-radius: 999px; padding: .1rem .45rem; background: #111827; color: #fff; }
        .rsv-stats { font-size: .72rem; line-height: 1.35; }
        .rsv-confirmed { color: #047857; font-weight: 600; }
        .rsv-pending { color: #b45309; font-weight: 600; }
        .rsv-card { padding: .9rem; }
        .rsv-title { margin: 0; font-size: .95rem; font-weight: 700; color: #0f172a; text-transform: capitalize; }
        .rsv-subtitle { margin: .3rem 0 .75rem; font-size: .8rem; color: #64748b; }
        .rsv-items { display: grid; gap: .6rem; }
        .rsv-item { border: 1px solid #dbe2ea; border-radius: 10px; padding: .65rem; }
        .rsv-item-top { display: flex; justify-content: space-between; align-items: flex-start; gap: .5rem; }
        .rsv-item-time { margin: 0; font-size: .88rem; font-weight: 700; color: #111827; }
        .rsv-item-meta { margin: .2rem 0 0; font-size: .76rem; color: #475569; }
        .rsv-badge { display: inline-block; border-radius: 999px; padding: .1rem .45rem; font-size: .68rem; font-weight: 700; }
        .rsv-badge-confirmed { background: #d1fae5; color: #065f46; }
        .rsv-badge-pending { background: #fef3c7; color: #92400e; }
        .rsv-badge-other { background: #ffe4e6; color: #9f1239; }
        .rsv-empty { border: 1px dashed #cbd5e1; border-radius: 10px; padding: 1rem; text-align: center; color: #64748b; font-size: .88rem; }
        @media (max-width: 1200px) { .rsv-layout { grid-template-columns: 1fr; } }
        @media (max-width: 640px) {
            .rsv-toolbar { justify-content: center; }
            .rsv-month-label { order: -1; width: 100%; text-align: center; }
            .rsv-nav-btn { flex: 1 1 calc(50% - 0.5rem); }
        }
    </style>

    <div class="rsv-calendar">
        <div class="rsv-toolbar">
            <button type="button" wire:click="previousMonth" class="rsv-nav-btn">← Mois précédent</button>
            <h2 class="rsv-month-label">{{ $this->getMonthLabel() }}</h2>
            <button type="button" wire:click="nextMonth" class="rsv-nav-btn">Mois suivant →</button>
        </div>

        <div class="rsv-layout">
            <section class="rsv-card" style="padding: 0; overflow: hidden;">
                <div class="rsv-grid-days">
                    @foreach (['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'] as $dayLabel)
                        <div>{{ $dayLabel }}</div>
                    @endforeach
                </div>

                <div class="rsv-grid-cells">
                    @foreach ($this->getCalendarCells() as $cell)
                        @php
                            $isSelected = $cell['date'] === $selectedDate;
                        @endphp
                        <button
                            type="button"
                            wire:click="selectDate('{{ $cell['date'] }}')"
                            @class([
                                'rsv-day-cell',
                                'is-selected' => $isSelected,
                                'is-other-month' => ! $cell['inMonth'],
                            ])
                        >
                            <div class="rsv-day-top">
                                <span class="rsv-day-num">{{ $cell['day'] }}</span>
                                @if ($cell['total'] > 0)
                                    <span class="rsv-total-pill">{{ $cell['total'] }}</span>
                                @endif
                            </div>
                            @if ($cell['total'] > 0)
                                <div class="rsv-stats">
                                    <div class="rsv-confirmed">Confirmées: {{ $cell['confirmed'] }}</div>
                                    <div class="rsv-pending">En attente: {{ $cell['pending'] }}</div>
                                </div>
                            @endif
                        </button>
                    @endforeach
                </div>
            </section>

            <section class="rsv-card">
                <h3 class="rsv-title">Agenda du {{ \Carbon\CarbonImmutable::parse($selectedDate)->locale('fr')->isoFormat('dddd D MMMM YYYY') }}</h3>
                <p class="rsv-subtitle">{{ count($this->getSelectedDayReservations()) }} réservation(s)</p>

                <div class="rsv-items">
                    @forelse ($this->getSelectedDayReservations() as $reservation)
                        <article class="rsv-item">
                            <div class="rsv-item-top">
                                <div>
                                    <p class="rsv-item-time">
                                        {{ \Carbon\CarbonImmutable::parse($reservation->reservation_at)->format('H:i') }}
                                        — {{ $reservation->customer_name }}
                                    </p>
                                    <p class="rsv-item-meta">
                                        {{ $reservation->covers }} pers.
                                        @if ($reservation->bookingService)
                                            • {{ $reservation->bookingService->name }}
                                        @endif
                                    </p>
                                </div>

                                <span @class([
                                    'rsv-badge',
                                    'rsv-badge-confirmed' => in_array($reservation->status, ['confirmed', 'attended'], true),
                                    'rsv-badge-pending' => $reservation->status === 'pending',
                                    'rsv-badge-other' => in_array($reservation->status, ['refused', 'cancelled', 'delayed', 'no_show'], true),
                                ])>
                                    {{ ucfirst($reservation->status) }}
                                </span>
                            </div>
                            @if ($reservation->notes)
                                <p class="rsv-item-meta" style="margin-top: .45rem;">{{ $reservation->notes }}</p>
                            @endif
                        </article>
                    @empty
                        <p class="rsv-empty">Aucune réservation sur cette date.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-filament-panels::page>

