@extends('layouts.bistro', ['cssVars' => $cssVars, 'bistroFontStylesheet' => $bistroFontStylesheet ?? null])

@section('title', ($pageContent['title'] ?? 'Réservation').' — '.$restaurant->name)

@section('content')
    @include('site.partials.top-contact-bar', ['restaurant' => $restaurant])

    <header class="bistro-inner-header">
        <div class="bistro-inner-header__inner">
            <a href="{{ route('site.home') }}" class="bistro-inner-brand">
                {{ $restaurant->name }}
            </a>
            @include('site.partials.main-nav')
        </div>
    </header>

    <section class="bistro-page-hero" aria-hidden="true">
        <div class="palazzo-shell palazzo-center" style="position:relative;">
            <p class="bistro-page-hero__eyebrow">{{ $restaurant->name }}</p>
            <h1 class="bistro-page-hero__title">{{ $pageContent['title'] ?? 'Réserver une table' }}</h1>
            @if(filled($pageContent['intro'] ?? null))
                <p class="bistro-page-hero__intro">{!! strip_tags(\App\Support\SiteContent\SiteContentHtml::paragraph($pageContent['intro'])) !!}</p>
            @endif
        </div>
    </section>

    <main id="contenu-principal" class="bistro-page-content">
        <div class="palazzo-shell" style="max-width:780px;margin:0 auto;">
            @php
                $minDate = now()->addHours((int) ($bookingSettings?->min_notice_hours ?? 2))->toDateString();
                $maxDate = now()->addDays((int) ($bookingSettings?->max_days_ahead ?? 30))->toDateString();
                $sectionOrder = $pageContent['section_order'] ?? \App\Support\SiteContent\PageSectionCatalog::defaultOrder('reservation');
            @endphp
            <style>
                .reservation-picker {
                    background: var(--bistro-color-primary);
                    color: #fff;
                    border-radius: 24px;
                    padding: 1rem;
                }
                .reservation-picker-row {
                    width: 100%;
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    padding: .95rem 0;
                    border-top: 1px solid rgb(255 255 255 / .35);
                    background: transparent;
                    color: inherit;
                    font-weight: 700;
                    font-size: 1.05rem;
                    cursor: pointer;
                }
                .reservation-picker-row:first-of-type { border-top: 0; }
                .reservation-picker-label { display: inline-flex; align-items: center; gap: .65rem; }
                .reservation-picker-panel { display: none; padding: 0 0 .95rem; }
                .reservation-picker-panel.is-open { display: block; }
                .reservation-picker-input {
                    width: 100%;
                    border-radius: 12px;
                    border: 1px solid rgb(255 255 255 / .45);
                    background: rgb(255 255 255 / .12);
                    color: #fff;
                    padding: .7rem .8rem;
                }
                .reservation-picker-input option { color: #111827; }
                .reservation-picker-date-wrap {
                    border: 1px solid rgb(255 255 255 / .35);
                    border-radius: 14px;
                    padding: .65rem;
                    background: rgb(255 255 255 / .08);
                }
                .reservation-picker-date-label {
                    display: block;
                    font-size: .78rem;
                    font-weight: 700;
                    margin-bottom: .45rem;
                    color: rgb(255 255 255 / .9);
                    text-transform: uppercase;
                    letter-spacing: .04em;
                }
                input[type='date'].reservation-picker-input::-webkit-calendar-picker-indicator {
                    filter: brightness(0) invert(1);
                    cursor: pointer;
                }
                .reservation-picker-slots {
                    display: grid;
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                    gap: .5rem;
                }
                .reservation-picker-covers {
                    display: grid;
                    grid-template-columns: repeat(4, minmax(0, 1fr));
                    gap: .5rem;
                }
                .reservation-picker-slot {
                    border-radius: 12px;
                    border: 1px solid rgb(255 255 255 / .45);
                    background: rgb(255 255 255 / .12);
                    color: #fff;
                    padding: .55rem .45rem;
                    font-size: .8rem;
                    font-weight: 700;
                }
                .reservation-picker-slot.is-active { background: #fff; color: var(--bistro-color-primary); }
                .reservation-picker-submit {
                    width: 100%;
                    margin-top: .75rem;
                    border-radius: 14px;
                    border: 0;
                    background: #fff;
                    color: var(--bistro-color-primary);
                    font-size: 1.35rem;
                    font-weight: 800;
                    line-height: 1;
                    padding: 1rem;
                    cursor: pointer;
                }
            </style>

            @foreach($sectionOrder as $sectionKey)
                @if($sectionKey === 'feedback')
                    @if (session('reservation_ok'))
                        <div class="bistro-alert bistro-alert--success mb-6" role="status">
                            {!! \App\Support\SiteContent\SiteContentHtml::safe($pageContent['success_message'] ?? 'Merci ! Votre demande de réservation a bien été enregistrée.') !!}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="bistro-alert bistro-alert--error mb-6" role="alert">
                            <strong>{{ $pageContent['error_form_title'] ?? 'Le formulaire contient des erreurs :' }}</strong>
                            <ul style="margin-top:0.5rem;padding-left:1.2rem;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                @endif

                @if($sectionKey === 'booking_form')
                    <form method="POST" action="{{ route('site.reservation.store') }}" class="bistro-card mt-8 space-y-7 p-6 md:p-8">
                @csrf

                <div class="reservation-picker">
                    <p class="mb-1 text-center text-sm text-white/85">{{ $pageContent['eyebrow'] ?? 'Choisissez vos préférences' }}</p>

                    <button type="button" class="reservation-picker-row" data-toggle-target="panel-service">
                        <span class="reservation-picker-label">🍽️ <span id="summary-service">{{ $pageContent['service_label'] ?? 'Service' }}</span></span>
                        <span>⌄</span>
                    </button>
                    <div id="panel-service" class="reservation-picker-panel">
                        <select id="booking-service" name="booking_service_id" required class="reservation-picker-input">
                            <option value="">Choisir un service</option>
                            @foreach($services as $service)
                                <option value="{{ $service->id }}" @selected(old('booking_service_id') == $service->id)>
                                    {{ $service->name }} ({{ substr($service->starts_at, 0, 5) }}–{{ substr($service->ends_at, 0, 5) }})
                                </option>
                            @endforeach
                        </select>
                        @error('booking_service_id')<span class="mt-1 block text-xs text-red-200">{{ $message }}</span>@enderror
                    </div>

                    <button type="button" class="reservation-picker-row" data-toggle-target="panel-date">
                        <span class="reservation-picker-label">📅 <span id="summary-date">{{ $pageContent['date_time_label'] ?? 'Date et horaire' }}</span></span>
                        <span>⌄</span>
                    </button>
                    <div id="panel-date" class="reservation-picker-panel">
                        <div class="reservation-picker-date-wrap">
                            <label class="reservation-picker-date-label" for="reservation-date">{{ $pageContent['date_field_label'] ?? 'Date' }}</label>
                            <input
                                id="reservation-date"
                                type="date"
                                name="reservation_date"
                                value="{{ old('reservation_date', $minDate) }}"
                                min="{{ $minDate }}"
                                max="{{ $maxDate }}"
                                required
                                class="reservation-picker-input"
                            />
                        </div>
                        @error('reservation_date')<span class="mt-1 block text-xs text-red-200">{{ $message }}</span>@enderror

                        <div class="reservation-picker-date-wrap mt-2">
                            <p class="reservation-picker-date-label">Horaires disponibles</p>
                        <select id="reservation-time" name="reservation_time" required class="sr-only">
                            <option value="">{{ $pageContent['time_select_placeholder_initial'] ?? 'Choisir d’abord une date et un service' }}</option>
                        </select>
                        <div id="reservation-slot-grid" class="reservation-picker-slots"></div>
                        <div id="reservation-availability-help" class="reservation-availability-help prose prose-invert mt-2 max-w-none text-xs text-white/80 prose-p:my-0">
                            {!! \App\Support\SiteContent\SiteContentHtml::safe($pageContent['availability_help'] ?? 'Les horaires proposés sont synchronisés avec les réservations enregistrées.') !!}
                        </div>
                        @error('reservation_time')<span class="mt-1 block text-xs text-red-200">{{ $message }}</span>@enderror
                        </div>

                        <div class="reservation-picker-date-wrap mt-2">
                            <p class="reservation-picker-date-label">{{ $pageContent['covers_label'] ?? 'Couverts' }}</p>
                            <input id="covers-input" type="number" name="covers" min="1" max="20" value="{{ old('covers', 2) }}" required class="sr-only" />
                            <div id="covers-grid" class="reservation-picker-covers"></div>
                            @error('covers')<span class="mt-1 block text-xs text-red-200">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <button type="button" class="reservation-picker-row" data-toggle-target="panel-contact">
                        <span class="reservation-picker-label">👤 <span id="summary-contact">{{ $pageContent['contact_label'] ?? 'Informations client' }}</span></span>
                        <span>⌄</span>
                    </button>
                    <div id="panel-contact" class="reservation-picker-panel">
                        <div class="grid gap-2 md:grid-cols-2">
                            <input id="first-name-input" type="text" name="customer_first_name" value="{{ old('customer_first_name') }}" required autocomplete="given-name" class="reservation-picker-input" placeholder="{{ $pageContent['placeholder_first_name'] ?? 'Prénom' }}" />
                            <input id="last-name-input" type="text" name="customer_last_name" value="{{ old('customer_last_name') }}" required autocomplete="family-name" class="reservation-picker-input" placeholder="{{ $pageContent['placeholder_last_name'] ?? 'Nom' }}" />
                        </div>
                        @error('customer_first_name')<span class="mt-1 block text-xs text-red-200">{{ $message }}</span>@enderror
                        @error('customer_last_name')<span class="mt-1 block text-xs text-red-200">{{ $message }}</span>@enderror

                        <div class="mt-2 grid gap-2 md:grid-cols-2">
                            <input id="email-input" type="email" name="customer_email" value="{{ old('customer_email') }}" required autocomplete="email" class="reservation-picker-input" placeholder="{{ $pageContent['placeholder_email'] ?? 'E-mail' }}" />
                            <input id="phone-input" type="tel" name="customer_phone" value="{{ old('customer_phone') }}" required autocomplete="tel" class="reservation-picker-input" placeholder="{{ $pageContent['placeholder_phone'] ?? 'Téléphone' }}" />
                        </div>
                        @error('customer_email')<span class="mt-1 block text-xs text-red-200">{{ $message }}</span>@enderror
                        @error('customer_phone')<span class="mt-1 block text-xs text-red-200">{{ $message }}</span>@enderror

                        <textarea name="notes" rows="2" class="reservation-picker-input mt-2" placeholder="{{ $pageContent['placeholder_notes'] ?? 'Notes (optionnel)' }}">{{ old('notes') }}</textarea>
                        @error('notes')<span class="mt-1 block text-xs text-red-200">{{ $message }}</span>@enderror
                    </div>

                    <button type="submit" class="reservation-picker-submit">{{ $pageContent['submit_label'] ?? 'Réserver' }}</button>
                </div>
                    </form>
                @endif
            @endforeach
        </div>
    </main>

    <script>
        (() => {
            // Toggle robuste des sections: indépendant du reste du script.
            document.querySelectorAll('[data-toggle-target]').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const panelId = btn.dataset.toggleTarget;
                    const target = panelId ? document.getElementById(panelId) : null;
                    if (target) {
                        target.classList.toggle('is-open');
                    }
                });
            });
        })();

        (() => {
            const pc = @json($pageContent ?? []);
            const serviceInput = document.getElementById('booking-service');
            const dateInput = document.getElementById('reservation-date');
            const coversInput = document.getElementById('covers-input');
            const timeSelect = document.getElementById('reservation-time');
            const slotGrid = document.getElementById('reservation-slot-grid');
            const helpText = document.getElementById('reservation-availability-help');
            const coversGrid = document.getElementById('covers-grid');
            const serviceSummary = document.getElementById('summary-service');
            const dateSummary = document.getElementById('summary-date');
            const contactSummary = document.getElementById('summary-contact');
            const firstNameInput = document.getElementById('first-name-input');
            const lastNameInput = document.getElementById('last-name-input');
            const emailInput = document.getElementById('email-input');
            const phoneInput = document.getElementById('phone-input');
            const endpoint = @json(route('site.reservation.availability'));
            const oldTime = @json(old('reservation_time'));
            const serviceLabelFallback = pc.service_label ?? 'Service';
            const dateLabelFallback = pc.date_time_label ?? 'Date et horaire';
            const contactLabelFallback = pc.contact_label ?? 'Informations client';

            if (!serviceInput || !dateInput || !coversInput || !timeSelect || !slotGrid || !helpText || !coversGrid || !contactSummary || !firstNameInput || !lastNameInput || !emailInput || !phoneInput) {
                return;
            }

            let currentRequest = 0;
            let selectedTimeValue = oldTime || timeSelect.value || '';

            const formatDateFr = (value) => {
                if (!value) return dateLabelFallback;
                const parsed = new Date(`${value}T00:00:00`);
                return parsed.toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long' });
            };

            const refreshDateSummary = () => {
                const base = formatDateFr(dateInput.value);
                dateSummary.textContent = selectedTimeValue ? `${base} · ${selectedTimeValue}` : base;
            };

            const renderCovers = () => {
                coversGrid.innerHTML = '';
                const selected = Number.parseInt(coversInput.value || '2', 10);

                for (let covers = 1; covers <= 20; covers++) {
                    const coverButton = document.createElement('button');
                    coverButton.type = 'button';
                    coverButton.className = 'reservation-picker-slot';
                    if (covers === selected) {
                        coverButton.classList.add('is-active');
                    }
                    coverButton.textContent = `${covers}`;
                    coverButton.addEventListener('click', () => {
                        coversInput.value = String(covers);
                        renderCovers();
                        refreshAvailability();
                    });
                    coversGrid.appendChild(coverButton);
                }
            };

            const setOptions = (slots, selectedTime = '') => {
                timeSelect.innerHTML = '';
                slotGrid.innerHTML = '';
                const placeholder = document.createElement('option');
                placeholder.value = '';
                placeholder.textContent = slots.length > 0 ? (pc.js_slot_pick ?? 'Choisir un horaire') : (pc.js_slot_none ?? 'Aucun horaire disponible');
                timeSelect.appendChild(placeholder);

                for (const slot of slots) {
                    const option = document.createElement('option');
                    option.value = slot.time;
                    option.textContent = `${slot.time} (${slot.remaining_covers} ${pc.js_slot_remaining ?? 'place(s) restantes'})`;
                    if (selectedTime && selectedTime === slot.time) {
                        option.selected = true;
                    }
                    timeSelect.appendChild(option);

                    const slotButton = document.createElement('button');
                    slotButton.type = 'button';
                    slotButton.dataset.time = slot.time;
                    slotButton.className = 'reservation-picker-slot';
                    if (selectedTime && selectedTime === slot.time) {
                        slotButton.classList.add('is-active');
                    }
                    slotButton.textContent = `${slot.time} · ${slot.remaining_covers} ${pc.js_slot_short ?? 'pl.'}`;
                    slotButton.addEventListener('click', () => {
                        selectedTimeValue = slot.time;
                        timeSelect.value = slot.time;
                        refreshDateSummary();
                        setOptions(slots, slot.time);
                    });
                    slotGrid.appendChild(slotButton);
                }
            };

            const refreshAvailability = async () => {
                const serviceId = serviceInput.value;
                const date = dateInput.value;
                const covers = coversInput.value || '1';

                if (!serviceId || !date) {
                    setOptions([]);
                    helpText.textContent = pc.js_choose_service_then_date ?? 'Choisissez un service puis une date.';
                    return;
                }

                const requestId = ++currentRequest;
                helpText.textContent = pc.js_loading ?? 'Chargement des disponibilités...';

                try {
                    const url = new URL(endpoint, window.location.origin);
                    url.searchParams.set('booking_service_id', serviceId);
                    url.searchParams.set('reservation_date', date);
                    url.searchParams.set('covers', covers);

                    const response = await fetch(url, {
                        headers: {
                            'Accept': 'application/json',
                        },
                    });

                    if (requestId !== currentRequest) {
                        return;
                    }

                    if (!response.ok) {
                        setOptions([]);
                        helpText.textContent = pc.js_error_availability ?? 'Impossible de charger les disponibilités pour le moment.';
                        return;
                    }

                    const payload = await response.json();
                    const slots = Array.isArray(payload.time_slots) ? payload.time_slots : [];
                    const selectedTime = selectedTimeValue || timeSelect.value || oldTime || '';
                    setOptions(slots, selectedTime);
                    if (!slots.some((slot) => slot.time === selectedTime)) {
                        selectedTimeValue = '';
                        timeSelect.value = '';
                        refreshDateSummary();
                    }
                    const prefix = pc.js_slots_count_prefix ?? 'Créneaux disponibles :';
                    helpText.textContent = payload.message ?? `${prefix} ${slots.length}`;
                } catch (error) {
                    setOptions([]);
                    helpText.textContent = pc.js_error_network ?? 'Erreur réseau lors du chargement des disponibilités.';
                }
            };

            const refreshContactSummary = () => {
                const fullName = `${firstNameInput.value || ''} ${lastNameInput.value || ''}`.trim();
                const parts = [fullName, emailInput.value || '', phoneInput.value || ''].filter(Boolean);
                contactSummary.textContent = parts.length ? parts.join(' · ') : contactLabelFallback;
            };

            serviceInput.addEventListener('change', refreshAvailability);
            serviceInput.addEventListener('change', () => {
                serviceSummary.textContent = serviceInput.options[serviceInput.selectedIndex]?.textContent || serviceLabelFallback;
            });
            dateInput.addEventListener('change', refreshAvailability);
            dateInput.addEventListener('change', refreshDateSummary);
            coversInput.addEventListener('change', refreshAvailability);
            coversInput.addEventListener('blur', refreshAvailability);
            coversInput.addEventListener('change', renderCovers);

            [firstNameInput, lastNameInput, emailInput, phoneInput].forEach((input) => {
                input.addEventListener('input', refreshContactSummary);
                input.addEventListener('change', refreshContactSummary);
            });

            serviceSummary.textContent = serviceInput.options[serviceInput.selectedIndex]?.textContent || serviceLabelFallback;
            refreshDateSummary();
            renderCovers();
            refreshContactSummary();
            refreshAvailability();
        })();
    </script>
@endsection

