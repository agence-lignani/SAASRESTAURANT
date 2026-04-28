<?php

namespace Tests\Feature;

use App\Mail\ReservationConfirmedMail;
use App\Mail\ReservationPendingClientMail;
use App\Mail\ReservationPendingTeamMail;
use App\Models\BookingService;
use App\Models\BookingSetting;
use App\Models\OpeningHourException;
use App\Models\Reservation;
use Carbon\CarbonImmutable;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SiteReservationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(PreventRequestForgery::class);
    }

    public function test_reservation_form_creates_confirmed_reservation_by_default_and_queues_emails(): void
    {
        Mail::fake();
        $this->seed(DatabaseSeeder::class);

        $service = BookingService::query()->where('name', 'Déjeuner')->firstOrFail();
        $reservationDate = $this->nextOpenDateForService($service);

        $response = $this->from('/reservation')->post('/reservation', [
            'booking_service_id' => $service->id,
            'reservation_date' => $reservationDate,
            'reservation_time' => '12:30',
            'covers' => 4,
            'customer_first_name' => 'Client',
            'customer_last_name' => 'Test',
            'customer_email' => 'client@example.test',
            'customer_phone' => '0600000000',
            'notes' => 'Table en terrasse si possible',
        ]);

        $response->assertRedirect(route('site.reservation'));
        $response->assertSessionHas('reservation_ok');

        $this->assertDatabaseHas('reservations', [
            'customer_email' => 'client@example.test',
            'status' => Reservation::STATUS_CONFIRMED,
            'covers' => 4,
        ]);

        Mail::assertQueued(ReservationConfirmedMail::class);
        Mail::assertNotQueued(ReservationPendingTeamMail::class);
        $this->assertDatabaseCount('notifications', 0);
    }

    public function test_reservation_can_be_created_as_pending_when_manual_confirmation_is_enabled(): void
    {
        Mail::fake();
        $this->seed(DatabaseSeeder::class);

        BookingSetting::query()->update(['manual_confirmation_required' => true]);

        $service = BookingService::query()->where('name', 'Déjeuner')->firstOrFail();
        $reservationDate = $this->nextOpenDateForService($service);

        $this->from('/reservation')->post('/reservation', [
            'booking_service_id' => $service->id,
            'reservation_date' => $reservationDate,
            'reservation_time' => '12:30',
            'covers' => 2,
            'customer_first_name' => 'Client',
            'customer_last_name' => 'Pending',
            'customer_email' => 'pending@example.test',
            'customer_phone' => '0600000000',
        ]);

        $this->assertDatabaseHas('reservations', [
            'customer_email' => 'pending@example.test',
            'status' => Reservation::STATUS_PENDING,
        ]);

        Mail::assertQueued(ReservationPendingClientMail::class);
        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => 'App\\Models\\User',
        ]);
    }

    private function nextOpenDateForService(BookingService $service): string
    {
        $day = CarbonImmutable::now()->startOfDay()->addDay();
        for ($i = 0; $i < 21; $i++) {
            if ($service->runsOnDate($day)) {
                return $day->toDateString();
            }
            $day = $day->addDay();
        }

        $this->fail('Aucun jour ouvert pour ce service dans la fenêtre de test.');
    }

    public function test_reservation_is_rejected_when_capacity_is_reached(): void
    {
        $this->seed(DatabaseSeeder::class);

        $service = BookingService::query()->where('name', 'Déjeuner')->firstOrFail();
        $date = now()->addDays(3)->startOfDay();

        Reservation::query()->create([
            'restaurant_id' => $service->restaurant_id,
            'booking_service_id' => $service->id,
            'reservation_at' => $date->setHour(12)->setMinute(30),
            'covers' => $service->capacity_covers,
            'customer_name' => 'Complet',
            'customer_email' => 'complet@example.test',
            'status' => Reservation::STATUS_CONFIRMED,
        ]);

        $response = $this->from('/reservation')->post('/reservation', [
            'booking_service_id' => $service->id,
            'reservation_date' => $date->format('Y-m-d'),
            'reservation_time' => '12:30',
            'covers' => 2,
            'customer_first_name' => 'Client',
            'customer_last_name' => 'Test',
            'customer_email' => 'client@example.test',
            'customer_phone' => '0600000000',
        ]);

        $response->assertSessionHasErrors('reservation_time');
        $this->assertDatabaseCount('reservations', 1);
    }

    public function test_availability_endpoint_returns_only_available_slots(): void
    {
        $this->seed(DatabaseSeeder::class);

        $service = BookingService::query()->where('name', 'Déjeuner')->firstOrFail();
        $date = now()->addDays(4)->startOfDay();

        Reservation::query()->create([
            'restaurant_id' => $service->restaurant_id,
            'booking_service_id' => $service->id,
            'reservation_at' => $date->setHour(12)->setMinute(0),
            'covers' => $service->capacity_covers,
            'customer_name' => 'Complet',
            'customer_email' => 'complet@example.test',
            'status' => Reservation::STATUS_CONFIRMED,
        ]);

        $response = $this->get('/reservation/availability?booking_service_id='.$service->id.'&reservation_date='.$date->format('Y-m-d').'&covers=2');

        $response
            ->assertOk()
            ->assertJsonPath('date', $date->format('Y-m-d'));

        $payload = $response->json();
        $slots = collect($payload['time_slots'] ?? [])->pluck('time')->all();

        $this->assertNotContains('12:00', $slots);
        $this->assertContains('12:30', $slots);
    }

    public function test_reservation_is_blocked_when_exceptional_closure_exists(): void
    {
        $this->seed(DatabaseSeeder::class);

        $service = BookingService::query()->where('name', 'Déjeuner')->firstOrFail();
        $date = now()->addDays(5)->startOfDay();

        OpeningHourException::query()->create([
            'restaurant_id' => $service->restaurant_id,
            'exception_date' => $date->format('Y-m-d'),
            'is_closed' => true,
        ]);

        $response = $this->from('/reservation')->post('/reservation', [
            'booking_service_id' => $service->id,
            'reservation_date' => $date->format('Y-m-d'),
            'reservation_time' => '12:30',
            'covers' => 2,
            'customer_first_name' => 'Client',
            'customer_last_name' => 'Fermeture',
            'customer_email' => 'closure@example.test',
            'customer_phone' => '0600000000',
        ]);

        $response->assertSessionHasErrors('reservation_date');
        $this->assertDatabaseCount('reservations', 0);
    }

    public function test_client_can_cancel_reservation_by_secure_token(): void
    {
        $this->seed(DatabaseSeeder::class);

        $reservation = Reservation::query()->create([
            'restaurant_id' => 1,
            'booking_service_id' => BookingService::query()->firstOrFail()->id,
            'reservation_at' => now()->addDays(3)->setHour(12)->setMinute(30),
            'covers' => 2,
            'customer_name' => 'Client Token',
            'customer_email' => 'token@example.test',
            'status' => Reservation::STATUS_CONFIRMED,
            'cancel_token' => 'token-abc-123',
        ]);

        $response = $this->post('/reservation/manage/token-abc-123/cancel');

        $response->assertRedirect('/reservation/manage/token-abc-123');
        $reservation->refresh();
        $this->assertSame(Reservation::STATUS_CANCELLED, $reservation->status);
    }

    public function test_client_can_reschedule_reservation_by_secure_token(): void
    {
        $this->seed(DatabaseSeeder::class);
        $service = BookingService::query()->where('name', 'Déjeuner')->firstOrFail();

        $reservation = Reservation::query()->create([
            'restaurant_id' => $service->restaurant_id,
            'booking_service_id' => $service->id,
            'reservation_at' => now()->addDays(4)->setHour(12)->setMinute(30),
            'covers' => 2,
            'customer_name' => 'Client Token',
            'customer_email' => 'token@example.test',
            'status' => Reservation::STATUS_CONFIRMED,
            'cancel_token' => 'token-resched-123',
        ]);

        $newAt = $reservation->reservation_at->copy()->addDay();
        while (! in_array($newAt->dayOfWeek, $service->days_of_week, true)) {
            $newAt = $newAt->addDay();
        }
        $newDate = $newAt->format('Y-m-d');
        $response = $this->from('/reservation/manage/token-resched-123')->post('/reservation/manage/token-resched-123/reschedule', [
            'reservation_date' => $newDate,
            'reservation_time' => '13:00',
        ]);

        $response->assertRedirect('/reservation/manage/token-resched-123');
        $reservation->refresh();
        $this->assertSame("{$newDate} 13:00:00", $reservation->reservation_at->format('Y-m-d H:i:s'));
        $this->assertSame(Reservation::STATUS_PENDING, $reservation->status);
    }
}
