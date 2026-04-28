@extends('layouts.bistro', ['cssVars' => $cssVars, 'bistroFontStylesheet' => $bistroFontStylesheet ?? null])

@section('title', ($pageContent['browser_title'] ?? 'Gérer ma réservation').' — '.$restaurant->name)

@section('content')
    @include('site.partials.top-contact-bar', ['restaurant' => $restaurant])

    <main id="contenu-principal" class="pb-16 pt-8 md:pb-24 md:pt-12">
        <div class="bistro-container max-w-3xl space-y-6">
            @php($sectionOrder = $pageContent['section_order'] ?? \App\Support\SiteContent\PageSectionCatalog::defaultOrder('reservation_manage'))
            @foreach($sectionOrder as $sectionKey)
                @if($sectionKey === 'header')
                    <div class="bistro-card p-6 md:p-8">
                        <h1 class="bistro-title text-3xl text-stone-900 md:text-4xl">{{ $pageContent['title'] ?? 'Gérer ma réservation' }}</h1>
                        <div class="prose prose-stone mt-2 max-w-none text-sm text-stone-600 prose-p:my-1">
                            {!! \App\Support\SiteContent\SiteContentHtml::paragraph($pageContent['intro'] ?? 'Vous pouvez annuler ou choisir un autre créneau selon les disponibilités.') !!}
                        </div>
                        @if (session('manage_success'))
                            <div class="mt-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                                {{ session('manage_success') }}
                            </div>
                        @endif
                        @if ($errors->any())
                            <div class="mt-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                                <ul class="list-disc space-y-1 pl-5">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                @endif

                @if($sectionKey === 'summary')
                    <div class="bistro-card p-6 md:p-8">
                        <div class="rounded-2xl border border-stone-200 bg-stone-50 p-4 text-sm text-stone-700">
                            <p><strong>{{ $pageContent['details_customer_label'] ?? 'Client :' }}</strong> {{ $reservation->customer_name }}</p>
                            <p><strong>{{ $pageContent['details_date_label'] ?? 'Date actuelle :' }}</strong> {{ $reservation->reservation_at->format('d/m/Y H:i') }}</p>
                            <p><strong>{{ $pageContent['details_service_label'] ?? 'Service :' }}</strong> {{ $reservation->bookingService->name }}</p>
                            <p><strong>{{ $pageContent['details_covers_label'] ?? 'Couverts :' }}</strong> {{ $reservation->covers }}</p>
                            <p><strong>{{ $pageContent['details_status_label'] ?? 'Statut :' }}</strong> {{ $reservation->status }}</p>
                        </div>
                    </div>
                @endif

                @if($sectionKey === 'actions')
                    <div class="bistro-card p-6 md:p-8">
                        @if (! $canManage)
                            <p class="text-sm text-amber-700">{{ $pageContent['deadline_exceeded_label'] ?? 'Le délai pour annuler ou reprogrammer est dépassé.' }}</p>
                        @else
                            <form method="POST" action="{{ route('site.reservation.reschedule', ['token' => $token]) }}" class="space-y-4">
                                @csrf
                                <div class="grid gap-4 md:grid-cols-2">
                                    <label class="block">
                                        <span class="mb-1.5 block text-sm font-medium text-stone-700">{{ $pageContent['new_date_label'] ?? 'Nouvelle date' }}</span>
                                        <input id="manage-date" type="date" name="reservation_date" value="{{ old('reservation_date', $reservation->reservation_at->format('Y-m-d')) }}" required class="w-full rounded-xl border border-stone-300 px-3 py-2.5 text-sm text-stone-800" />
                                    </label>
                                    <label class="block">
                                        <span class="mb-1.5 block text-sm font-medium text-stone-700">{{ $pageContent['new_time_label'] ?? 'Nouvel horaire' }}</span>
                                        <select id="manage-time" name="reservation_time" required class="w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-800">
                                            <option value="{{ old('reservation_time', $reservation->reservation_at->format('H:i')) }}">{{ old('reservation_time', $reservation->reservation_at->format('H:i')) }}</option>
                                        </select>
                                    </label>
                                </div>

                                <div id="manage-help" class="prose prose-stone mt-1 max-w-none text-xs text-stone-500 prose-p:my-0">
                                    {!! \App\Support\SiteContent\SiteContentHtml::safe($pageContent['slots_help'] ?? 'Les créneaux proposés tiennent compte des disponibilités en temps réel.') !!}
                                </div>

                                <div class="flex flex-wrap gap-3">
                                    <button type="submit" class="bistro-btn-primary">{{ $pageContent['reschedule_label'] ?? 'Reprogrammer' }}</button>
                                </div>
                            </form>

                            <form method="POST" action="{{ route('site.reservation.cancel', ['token' => $token]) }}" class="mt-3">
                                @csrf
                                <button type="submit" class="bistro-btn-secondary">{{ $pageContent['cancel_label'] ?? 'Annuler ma réservation' }}</button>
                            </form>
                        @endif
                    </div>
                @endif
            @endforeach
        </div>
    </main>

    @if ($canManage)
        <script>
            (() => {
                const pc = @json($pageContent ?? []);
                const endpoint = @json(route('site.reservation.availability'));
                const serviceId = @json((string) $reservation->booking_service_id);
                const token = @json($token);
                const covers = @json((string) $reservation->covers);
                const dateInput = document.getElementById('manage-date');
                const timeSelect = document.getElementById('manage-time');
                const help = document.getElementById('manage-help');
                const previous = @json($reservation->reservation_at->format('H:i'));

                if (!dateInput || !timeSelect || !help) return;

                const refreshSlots = async () => {
                    const date = dateInput.value;
                    if (!date) return;

                    const url = new URL(endpoint, window.location.origin);
                    url.searchParams.set('booking_service_id', serviceId);
                    url.searchParams.set('reservation_date', date);
                    url.searchParams.set('covers', covers);
                    url.searchParams.set('reservation_token', token);

                    try {
                        const response = await fetch(url, { headers: { 'Accept': 'application/json' } });
                        const payload = await response.json();
                        const slots = Array.isArray(payload.time_slots) ? payload.time_slots : [];
                        timeSelect.innerHTML = '';

                        if (slots.length === 0) {
                            const empty = document.createElement('option');
                            empty.value = '';
                            empty.textContent = pc.js_empty_slots ?? 'Aucun créneau disponible';
                            timeSelect.appendChild(empty);
                        } else {
                            slots.forEach((slot) => {
                                const option = document.createElement('option');
                                option.value = slot.time;
                                option.textContent = `${slot.time} (${slot.remaining_covers} ${pc.js_slot_places ?? 'places'})`;
                                if (slot.time === previous) option.selected = true;
                                timeSelect.appendChild(option);
                            });
                        }

                        const prefix = pc.js_slots_count_prefix ?? 'Créneaux disponibles :';
                        help.textContent = payload.message ?? `${prefix} ${slots.length}`;
                    } catch (e) {
                        help.textContent = pc.js_error_load ?? 'Erreur lors du chargement des disponibilités.';
                    }
                };

                dateInput.addEventListener('change', refreshSlots);
                refreshSlots();
            })();
        </script>
    @endif
@endsection
