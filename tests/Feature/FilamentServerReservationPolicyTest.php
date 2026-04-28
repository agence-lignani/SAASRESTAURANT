<?php

namespace Tests\Feature;

use App\Filament\Widgets\ServerTodayReservationsWidget;
use App\Models\BookingService;
use App\Models\Reservation;
use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilamentServerReservationPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_server_profile_can_view_reservations_but_not_modify(): void
    {
        $this->seed(DatabaseSeeder::class);

        $serverUser = User::query()->where('email', 'serveur@example.test')->firstOrFail();
        $service = BookingService::query()->firstOrFail();
        $restaurant = Restaurant::query()->firstOrFail();

        $reservation = Reservation::query()->create([
            'restaurant_id' => $restaurant->id,
            'booking_service_id' => $service->id,
            'reservation_at' => now()->addDay(),
            'covers' => 2,
            'customer_name' => 'Client Test',
            'customer_email' => 'client-test@example.test',
            'customer_phone' => null,
            'notes' => null,
            'status' => Reservation::STATUS_CONFIRMED,
            'cancel_token' => bin2hex(random_bytes(8)),
        ]);

        $this->actingAs($serverUser);
        session(['filament_profile_role' => 'server']);

        $this->assertTrue($serverUser->can('viewAny', Reservation::class));
        $this->assertTrue($serverUser->can('view', $reservation));
        $this->assertFalse($serverUser->can('create', Reservation::class));
        $this->assertFalse($serverUser->can('update', $reservation));
        $this->assertFalse($serverUser->can('delete', $reservation));

        $this->assertTrue($serverUser->can('delayReservation', $reservation));
        $this->assertTrue($serverUser->can('cancelReservation', $reservation));
        $this->assertTrue($serverUser->can('confirmPresence', $reservation));

        $reservation->update(['status' => Reservation::STATUS_CANCELLED]);
        $this->assertFalse($serverUser->can('delayReservation', $reservation->fresh()));
        $this->assertFalse($serverUser->can('cancelReservation', $reservation));
        $this->assertFalse($serverUser->can('confirmPresence', $reservation));
    }

    public function test_owner_cannot_use_server_floor_reservation_abilities(): void
    {
        $this->seed(DatabaseSeeder::class);

        $owner = User::query()->where('email', 'admin@example.test')->firstOrFail();
        $service = BookingService::query()->firstOrFail();
        $restaurant = Restaurant::query()->firstOrFail();

        $reservation = Reservation::query()->create([
            'restaurant_id' => $restaurant->id,
            'booking_service_id' => $service->id,
            'reservation_at' => now()->addDay(),
            'covers' => 2,
            'customer_name' => 'Client Test',
            'customer_email' => 'client-test@example.test',
            'customer_phone' => null,
            'notes' => null,
            'status' => Reservation::STATUS_CONFIRMED,
            'cancel_token' => bin2hex(random_bytes(8)),
        ]);

        $this->actingAs($owner);
        session(['filament_profile_role' => 'owner']);

        $this->assertFalse($owner->can('delayReservation', $reservation));
        $this->assertFalse($owner->can('cancelReservation', $reservation));
        $this->assertFalse($owner->can('confirmPresence', $reservation));
    }

    public function test_editor_profile_cannot_view_reservations(): void
    {
        $this->seed(DatabaseSeeder::class);

        $editor = User::factory()->create();
        $restaurant = Restaurant::query()->firstOrFail();
        $editor->restaurants()->attach($restaurant->id, ['role' => 'editor']);

        $this->actingAs($editor);
        session(['filament_profile_role' => 'editor']);

        $this->assertFalse($editor->can('viewAny', Reservation::class));
    }

    public function test_server_today_reservations_dashboard_widget_visibility(): void
    {
        session(['filament_profile_role' => 'server']);
        $this->assertTrue(ServerTodayReservationsWidget::canView());

        session(['filament_profile_role' => 'owner']);
        $this->assertFalse(ServerTodayReservationsWidget::canView());
    }

    public function test_owner_reservation_view_shows_customer_email(): void
    {
        $this->seed(DatabaseSeeder::class);

        $owner = User::query()->where('email', 'admin@example.test')->firstOrFail();
        $service = BookingService::query()->firstOrFail();
        $restaurant = Restaurant::query()->firstOrFail();

        $reservation = Reservation::query()->create([
            'restaurant_id' => $restaurant->id,
            'booking_service_id' => $service->id,
            'reservation_at' => now()->addDay(),
            'covers' => 2,
            'customer_name' => 'Client Test',
            'customer_email' => 'client-pii-secret@example.test',
            'customer_phone' => '+33 6 12 34 56 78',
            'notes' => null,
            'status' => Reservation::STATUS_CONFIRMED,
            'cancel_token' => bin2hex(random_bytes(8)),
        ]);

        $this->actingAs($owner)
            ->withSession(['filament_profile_role' => 'owner'])
            ->get('/admin/reservations/'.$reservation->getKey())
            ->assertSuccessful()
            ->assertSee('client-pii-secret@example.test', false);
    }

    public function test_server_reservation_view_hides_customer_email_only(): void
    {
        $this->seed(DatabaseSeeder::class);

        $serverUser = User::query()->where('email', 'serveur@example.test')->firstOrFail();
        $service = BookingService::query()->firstOrFail();
        $restaurant = Restaurant::query()->firstOrFail();

        $reservation = Reservation::query()->create([
            'restaurant_id' => $restaurant->id,
            'booking_service_id' => $service->id,
            'reservation_at' => now()->addDay(),
            'covers' => 2,
            'customer_name' => 'Client Test',
            'customer_email' => 'client-pii-secret@example.test',
            'customer_phone' => '+33 6 12 34 56 78',
            'notes' => null,
            'status' => Reservation::STATUS_CONFIRMED,
            'cancel_token' => bin2hex(random_bytes(8)),
        ]);

        $this->actingAs($serverUser)
            ->withSession(['filament_profile_role' => 'server'])
            ->get('/admin/reservations/'.$reservation->getKey())
            ->assertSuccessful()
            ->assertDontSee('client-pii-secret@example.test', false)
            ->assertSee('+33 6 12 34 56 78', false);
    }
}
