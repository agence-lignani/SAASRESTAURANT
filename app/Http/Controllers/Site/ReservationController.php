<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Site\Concerns\PreparesBistroPublicPage;
use App\Mail\ReservationConfirmedMail;
use App\Mail\ReservationPendingClientMail;
use App\Models\BookingService;
use App\Models\OpeningHourException;
use App\Models\Reservation;
use App\Models\Restaurant;
use App\Support\SiteContent\SiteContentResolver;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ReservationController extends Controller
{
    use PreparesBistroPublicPage;

    public function show(Request $request): View
    {
        /** @var Restaurant $restaurant */
        $restaurant = $request->attributes->get('restaurant');
        $restaurant->loadMissing('pageContent');
        $siteContent = SiteContentResolver::forRestaurant($restaurant);

        try {
            $services = $restaurant->bookingServices()
                ->active()
                ->orderBy('sort_order')
                ->get();
        } catch (\Throwable $e) {
            throw $e;
        }

        return view('site.reservation', array_merge($this->bistroThemePayload($restaurant), [
            'services' => $services,
            'bookingSettings' => $restaurant->bookingSetting,
            'pageContent' => $siteContent['reservation'],
        ]));
    }

    public function store(Request $request): RedirectResponse
    {
        /** @var Restaurant $restaurant */
        $restaurant = $request->attributes->get('restaurant');

        $validated = $request->validate(
            [
                'booking_service_id' => ['required', 'integer'],
                'reservation_date' => ['required', 'date_format:Y-m-d'],
                'reservation_time' => ['required', 'date_format:H:i'],
                'covers' => ['required', 'integer', 'min:1', 'max:20'],
                'customer_first_name' => ['required', 'string', 'max:120'],
                'customer_last_name' => ['required', 'string', 'max:120'],
                'customer_email' => ['required', 'email:rfc', 'max:255'],
                'customer_phone' => ['required', 'string', 'max:64', 'regex:/^[0-9+().\-\s]{6,64}$/'],
                'notes' => ['nullable', 'string', 'max:2000'],
            ],
            [
                'booking_service_id.required' => 'Veuillez choisir un service.',
                'booking_service_id.integer' => 'Le service sélectionné est invalide.',
                'reservation_date.required' => 'Veuillez choisir une date.',
                'reservation_date.date_format' => 'Le format de la date est invalide.',
                'reservation_time.required' => 'Veuillez choisir un horaire.',
                'reservation_time.date_format' => 'Le format de l’horaire est invalide.',
                'covers.required' => 'Veuillez sélectionner le nombre de couverts.',
                'covers.integer' => 'Le nombre de couverts doit être un nombre entier.',
                'covers.min' => 'Le nombre de couverts doit être au minimum :min.',
                'covers.max' => 'Le nombre de couverts ne peut pas dépasser :max.',
                'customer_first_name.required' => 'Le prénom est obligatoire.',
                'customer_first_name.max' => 'Le prénom ne doit pas dépasser :max caractères.',
                'customer_last_name.required' => 'Le nom est obligatoire.',
                'customer_last_name.max' => 'Le nom ne doit pas dépasser :max caractères.',
                'customer_email.required' => 'L’e-mail est obligatoire.',
                'customer_email.email' => 'Veuillez saisir une adresse e-mail valide.',
                'customer_email.max' => 'L’e-mail ne doit pas dépasser :max caractères.',
                'customer_phone.required' => 'Le téléphone est obligatoire.',
                'customer_phone.max' => 'Le téléphone ne doit pas dépasser :max caractères.',
                'customer_phone.regex' => 'Le format du téléphone est invalide.',
                'notes.max' => 'Les notes ne doivent pas dépasser :max caractères.',
            ],
            [
                'booking_service_id' => 'service',
                'reservation_date' => 'date',
                'reservation_time' => 'horaire',
                'covers' => 'couverts',
                'customer_first_name' => 'prénom',
                'customer_last_name' => 'nom',
                'customer_email' => 'e-mail',
                'customer_phone' => 'téléphone',
                'notes' => 'notes',
            ],
        );

        /** @var BookingService $service */
        $service = $restaurant->bookingServices()
            ->active()
            ->whereKey($validated['booking_service_id'])
            ->firstOrFail();

        $reservationAt = CarbonImmutable::parse($validated['reservation_date'].' '.$validated['reservation_time'], config('app.timezone'));
        $settings = $restaurant->bookingSetting;
        $minNoticeHours = (int) ($settings?->min_notice_hours ?? 2);
        $maxDaysAhead = (int) ($settings?->max_days_ahead ?? 30);
        $manualConfirmationRequired = (bool) ($settings?->manual_confirmation_required ?? false);
        $defaultStatus = $manualConfirmationRequired ? Reservation::STATUS_PENDING : Reservation::STATUS_CONFIRMED;

        if (! $service->runsOnDate($reservationAt)) {
            throw ValidationException::withMessages([
                'reservation_date' => 'Ce service n’est pas disponible ce jour.',
            ]);
        }

        $serviceWindow = $this->resolveServiceWindowForDate($restaurant, $service, $reservationAt->startOfDay());
        if ($serviceWindow === null) {
            throw ValidationException::withMessages([
                'reservation_date' => 'Le restaurant est fermé à cette date.',
            ]);
        }

        [$startsAt, $endsAt] = $serviceWindow;

        if ($reservationAt->lt($startsAt) || $reservationAt->gt($endsAt)) {
            throw ValidationException::withMessages([
                'reservation_time' => 'L’horaire choisi est hors plage du service.',
            ]);
        }

        if ($reservationAt->lt(now()->addHours($minNoticeHours))) {
            throw ValidationException::withMessages([
                'reservation_time' => "Merci de réserver au moins {$minNoticeHours}h à l’avance.",
            ]);
        }

        if ($reservationAt->gt(now()->addDays($maxDaysAhead))) {
            throw ValidationException::withMessages([
                'reservation_date' => "Vous pouvez réserver jusqu’à {$maxDaysAhead} jours à l’avance.",
            ]);
        }

        $reservation = DB::transaction(function () use ($restaurant, $service, $reservationAt, $validated, $defaultStatus): Reservation {
            $alreadyBooked = Reservation::query()
                ->where('restaurant_id', $restaurant->id)
                ->where('booking_service_id', $service->id)
                ->where('reservation_at', $reservationAt)
                ->countedInCapacity()
                ->lockForUpdate()
                ->sum('covers');

            if (($alreadyBooked + (int) $validated['covers']) > $service->capacity_covers) {
                throw ValidationException::withMessages([
                    'reservation_time' => 'Ce créneau n’est plus disponible. Merci de choisir un autre horaire.',
                ]);
            }

            return Reservation::query()->create([
                'restaurant_id' => $restaurant->id,
                'booking_service_id' => $service->id,
                'reservation_at' => $reservationAt,
                'covers' => (int) $validated['covers'],
                'customer_name' => trim($validated['customer_first_name'].' '.$validated['customer_last_name']),
                'customer_email' => $validated['customer_email'],
                'customer_phone' => $validated['customer_phone'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'status' => $defaultStatus,
                'source' => Reservation::SOURCE_SITE,
                'cancel_token' => bin2hex(random_bytes(24)),
            ]);
        });

        $reservation->loadMissing(['restaurant', 'bookingService']);

        if ($reservation->status === Reservation::STATUS_PENDING) {
            Mail::to($reservation->customer_email)->queue(new ReservationPendingClientMail($reservation));
        } else {
            Mail::to($reservation->customer_email)->queue(new ReservationConfirmedMail($reservation));
        }

        return redirect()->route('site.reservation')->with('reservation_ok', true);
    }

    public function availability(Request $request): JsonResponse
    {
        /** @var Restaurant $restaurant */
        $restaurant = $request->attributes->get('restaurant');

        $validated = $request->validate(
            [
                'booking_service_id' => ['required', 'integer'],
                'reservation_date' => ['required', 'date_format:Y-m-d'],
                'covers' => ['nullable', 'integer', 'min:1', 'max:20'],
                'reservation_token' => ['nullable', 'string'],
            ],
            [
                'booking_service_id.required' => 'Veuillez choisir un service.',
                'booking_service_id.integer' => 'Le service sélectionné est invalide.',
                'reservation_date.required' => 'Veuillez choisir une date.',
                'reservation_date.date_format' => 'Le format de la date est invalide.',
                'covers.integer' => 'Le nombre de couverts doit être un nombre entier.',
                'covers.min' => 'Le nombre de couverts doit être au minimum :min.',
                'covers.max' => 'Le nombre de couverts ne peut pas dépasser :max.',
            ],
        );

        /** @var BookingService $service */
        $service = $restaurant->bookingServices()
            ->active()
            ->whereKey((int) $validated['booking_service_id'])
            ->firstOrFail();

        $reservationDate = CarbonImmutable::parse($validated['reservation_date'], config('app.timezone'))->startOfDay();
        $settings = $restaurant->bookingSetting;
        $slotMinutes = max(5, (int) ($settings?->slot_minutes ?? 30));
        $minNoticeHours = (int) ($settings?->min_notice_hours ?? 2);
        $maxDaysAhead = (int) ($settings?->max_days_ahead ?? 30);
        $covers = (int) ($validated['covers'] ?? 1);
        $excludeReservationId = $this->reservationIdFromToken($restaurant, $validated['reservation_token'] ?? null);

        if (! $service->runsOnDate($reservationDate)) {
            return response()->json([
                'date' => $reservationDate->toDateString(),
                'slot_minutes' => $slotMinutes,
                'time_slots' => [],
                'message' => 'Ce service n’est pas disponible à cette date.',
            ]);
        }

        $serviceWindow = $this->resolveServiceWindowForDate($restaurant, $service, $reservationDate);
        if ($serviceWindow === null) {
            return response()->json([
                'date' => $reservationDate->toDateString(),
                'slot_minutes' => $slotMinutes,
                'time_slots' => [],
                'message' => 'Le restaurant est fermé à cette date.',
            ]);
        }

        if ($reservationDate->lt(now()->startOfDay())) {
            return response()->json([
                'date' => $reservationDate->toDateString(),
                'slot_minutes' => $slotMinutes,
                'time_slots' => [],
                'message' => 'Cette date est passée.',
            ]);
        }

        if ($reservationDate->gt(now()->addDays($maxDaysAhead)->startOfDay())) {
            return response()->json([
                'date' => $reservationDate->toDateString(),
                'slot_minutes' => $slotMinutes,
                'time_slots' => [],
                'message' => "Réservation possible jusqu’à {$maxDaysAhead} jours à l’avance.",
            ]);
        }

        [$windowStartsAt, $windowEndsAt] = $serviceWindow;

        $slots = $this->buildAvailableSlots(
            $restaurant,
            $service,
            $reservationDate,
            $slotMinutes,
            $covers,
            $minNoticeHours,
            $windowStartsAt,
            $windowEndsAt,
            $excludeReservationId,
        );

        return response()->json([
            'date' => $reservationDate->toDateString(),
            'slot_minutes' => $slotMinutes,
            'time_slots' => $slots,
            'message' => $slots === [] ? 'Aucun créneau disponible pour cette date.' : null,
        ]);
    }

    /**
     * @return array<int, array{time: string, remaining_covers: int}>
     */
    private function buildAvailableSlots(
        Restaurant $restaurant,
        BookingService $service,
        CarbonImmutable $reservationDate,
        int $slotMinutes,
        int $covers,
        int $minNoticeHours,
        CarbonImmutable $startsAt,
        CarbonImmutable $endsAt,
        ?int $excludeReservationId = null,
    ): array {
        $minAllowed = now()->addHours($minNoticeHours);

        $bookedBySlot = Reservation::query()
            ->where('restaurant_id', $restaurant->id)
            ->where('booking_service_id', $service->id)
            ->whereDate('reservation_at', $reservationDate->toDateString())
            ->when($excludeReservationId, fn ($query) => $query->whereKeyNot($excludeReservationId))
            ->countedInCapacity()
            ->selectRaw("strftime('%H:%M', reservation_at) as slot_time")
            ->selectRaw('SUM(covers) as booked_covers')
            ->groupBy('slot_time')
            ->pluck('booked_covers', 'slot_time');

        $slots = [];
        for ($slot = $startsAt; $slot->lte($endsAt); $slot = $slot->addMinutes($slotMinutes)) {
            if ($slot->lt($minAllowed)) {
                continue;
            }

            $slotKey = $slot->format('H:i');
            $remaining = (int) $service->capacity_covers - (int) ($bookedBySlot[$slotKey] ?? 0);

            if ($remaining >= $covers) {
                $slots[] = [
                    'time' => $slotKey,
                    'remaining_covers' => $remaining,
                ];
            }
        }

        return $slots;
    }

    /**
     * @return array{0: CarbonImmutable, 1: CarbonImmutable}|null
     */
    private function resolveServiceWindowForDate(
        Restaurant $restaurant,
        BookingService $service,
        CarbonImmutable $date,
    ): ?array {
        /** @var OpeningHourException|null $exception */
        $exception = $restaurant->openingHourExceptions()
            ->whereDate('exception_date', $date->toDateString())
            ->first();

        if ($exception?->is_closed) {
            return null;
        }

        $startsAt = CarbonImmutable::parse($date->format('Y-m-d').' '.$service->starts_at, config('app.timezone'));
        $endsAt = CarbonImmutable::parse($date->format('Y-m-d').' '.$service->ends_at, config('app.timezone'));

        if ($exception && filled($exception->opens_at) && filled($exception->closes_at)) {
            $exceptionStartsAt = CarbonImmutable::parse($date->format('Y-m-d').' '.$exception->opens_at, config('app.timezone'));
            $exceptionEndsAt = CarbonImmutable::parse($date->format('Y-m-d').' '.$exception->closes_at, config('app.timezone'));

            if ($exceptionEndsAt->lte($exceptionStartsAt)) {
                return null;
            }

            // On applique l'intervalle le plus restrictif entre le service et l'exception.
            $startsAt = $exceptionStartsAt->gt($startsAt) ? $exceptionStartsAt : $startsAt;
            $endsAt = $exceptionEndsAt->lt($endsAt) ? $exceptionEndsAt : $endsAt;
        }

        if ($endsAt->lte($startsAt)) {
            return null;
        }

        return [$startsAt, $endsAt];
    }

    public function manage(Request $request, string $token): View
    {
        /** @var Restaurant $restaurant */
        $restaurant = $request->attributes->get('restaurant');
        $restaurant->loadMissing('pageContent');
        $siteContent = SiteContentResolver::forRestaurant($restaurant);
        $reservation = $this->reservationByToken($restaurant, $token);

        abort_unless($reservation, 404);

        return view('site.reservation-manage', array_merge($this->bistroThemePayload($restaurant), [
            'reservation' => $reservation,
            'canManage' => $this->canManageByToken($reservation),
            'token' => $token,
            'pageContent' => $siteContent['reservation_manage'],
        ]));
    }

    public function cancelByToken(Request $request, string $token): RedirectResponse
    {
        /** @var Restaurant $restaurant */
        $restaurant = $request->attributes->get('restaurant');
        $reservation = $this->reservationByToken($restaurant, $token);

        abort_unless($reservation, 404);

        if (! $this->canManageByToken($reservation)) {
            return back()->withErrors(['reservation' => 'Le délai d’annulation/reprogrammation est dépassé.']);
        }

        $reservation->status = Reservation::STATUS_CANCELLED;
        $reservation->save();

        return redirect()->route('site.reservation.manage', ['token' => $token])->with('manage_success', 'Réservation annulée.');
    }

    public function rescheduleByToken(Request $request, string $token): RedirectResponse
    {
        /** @var Restaurant $restaurant */
        $restaurant = $request->attributes->get('restaurant');
        $reservation = $this->reservationByToken($restaurant, $token);

        abort_unless($reservation, 404);

        if (! $this->canManageByToken($reservation)) {
            return back()->withErrors(['reservation' => 'Le délai d’annulation/reprogrammation est dépassé.']);
        }

        $validated = $request->validate([
            'reservation_date' => ['required', 'date_format:Y-m-d'],
            'reservation_time' => ['required', 'date_format:H:i'],
        ], [
            'reservation_date.required' => 'Veuillez choisir une date.',
            'reservation_time.required' => 'Veuillez choisir un horaire.',
        ]);

        $service = $reservation->bookingService;
        $newAt = CarbonImmutable::parse($validated['reservation_date'].' '.$validated['reservation_time'], config('app.timezone'));
        $settings = $restaurant->bookingSetting;
        $minNoticeHours = (int) ($settings?->min_notice_hours ?? 2);
        $maxDaysAhead = (int) ($settings?->max_days_ahead ?? 30);

        if (! $service->runsOnDate($newAt)) {
            throw ValidationException::withMessages(['reservation_date' => 'Ce service n’est pas disponible ce jour.']);
        }

        $serviceWindow = $this->resolveServiceWindowForDate($restaurant, $service, $newAt->startOfDay());
        if ($serviceWindow === null) {
            throw ValidationException::withMessages(['reservation_date' => 'Le restaurant est fermé à cette date.']);
        }

        [$startsAt, $endsAt] = $serviceWindow;

        if ($newAt->lt($startsAt) || $newAt->gt($endsAt)) {
            throw ValidationException::withMessages(['reservation_time' => 'L’horaire choisi est hors plage du service.']);
        }
        if ($newAt->lt(now()->addHours($minNoticeHours))) {
            throw ValidationException::withMessages(['reservation_time' => "Merci de réserver au moins {$minNoticeHours}h à l’avance."]);
        }
        if ($newAt->gt(now()->addDays($maxDaysAhead))) {
            throw ValidationException::withMessages(['reservation_date' => "Vous pouvez réserver jusqu’à {$maxDaysAhead} jours à l’avance."]);
        }

        DB::transaction(function () use ($reservation, $restaurant, $service, $newAt): void {
            $alreadyBooked = Reservation::query()
                ->where('restaurant_id', $restaurant->id)
                ->where('booking_service_id', $service->id)
                ->where('reservation_at', $newAt)
                ->whereKeyNot($reservation->id)
                ->countedInCapacity()
                ->lockForUpdate()
                ->sum('covers');

            if (($alreadyBooked + $reservation->covers) > $service->capacity_covers) {
                throw ValidationException::withMessages([
                    'reservation_time' => 'Ce créneau n’est plus disponible. Merci de choisir un autre horaire.',
                ]);
            }

            $reservation->reservation_at = $newAt;
            $reservation->status = Reservation::STATUS_PENDING;
            $reservation->save();
        });

        return redirect()->route('site.reservation.manage', ['token' => $token])->with('manage_success', 'Réservation reprogrammée (en attente de confirmation).');
    }

    private function reservationByToken(Restaurant $restaurant, string $token): ?Reservation
    {
        return Reservation::query()
            ->where('restaurant_id', $restaurant->id)
            ->where('cancel_token', $token)
            ->with(['bookingService', 'restaurant'])
            ->first();
    }

    private function reservationIdFromToken(Restaurant $restaurant, ?string $token): ?int
    {
        if (! is_string($token) || $token === '') {
            return null;
        }

        return Reservation::query()
            ->where('restaurant_id', $restaurant->id)
            ->where('cancel_token', $token)
            ->value('id');
    }

    private function canManageByToken(Reservation $reservation): bool
    {
        $settings = $reservation->restaurant->bookingSetting;
        if (! (bool) ($settings?->allow_client_cancellation ?? true)) {
            return false;
        }

        $cancellationHours = (int) ($settings?->cancellation_hours ?? 6);

        return $reservation->reservation_at?->gt(now()->addHours($cancellationHours)) ?? false;
    }
}
