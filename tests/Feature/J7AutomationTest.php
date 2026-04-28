<?php

namespace Tests\Feature;

use App\Mail\ReservationReminderMail;
use App\Models\BookingService;
use App\Models\BookingSetting;
use App\Models\Reservation;
use App\Models\Restaurant;
use App\Models\SitePageView;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class J7AutomationTest extends TestCase
{
    use RefreshDatabase;

    public function test_reservation_reminder_m6_is_sent_when_in_window_and_enabled(): void
    {
        Mail::fake();
        $this->seed(DatabaseSeeder::class);

        $restaurant = Restaurant::query()->firstOrFail();
        BookingSetting::query()->where('restaurant_id', $restaurant->id)->update([
            'reminder_enabled' => true,
            'reminder_hours_before' => 24,
        ]);

        $service = BookingService::query()->where('restaurant_id', $restaurant->id)->firstOrFail();

        Reservation::query()->create([
            'restaurant_id' => $restaurant->id,
            'booking_service_id' => $service->id,
            'reservation_at' => now()->addHours(12),
            'covers' => 2,
            'customer_name' => 'Client Rappel',
            'customer_email' => 'rappel@example.test',
            'customer_phone' => '0600000000',
            'status' => Reservation::STATUS_CONFIRMED,
            'source' => Reservation::SOURCE_SITE,
            'cancel_token' => bin2hex(random_bytes(24)),
        ]);

        Artisan::call('reservations:send-reminders');

        Mail::assertQueued(ReservationReminderMail::class, 1);

        $this->assertNotNull(
            Reservation::query()->where('customer_email', 'rappel@example.test')->value('reminder_sent_at')
        );
    }

    public function test_reservation_reminder_is_not_sent_when_disabled(): void
    {
        Mail::fake();
        $this->seed(DatabaseSeeder::class);

        $restaurant = Restaurant::query()->firstOrFail();
        BookingSetting::query()->where('restaurant_id', $restaurant->id)->update([
            'reminder_enabled' => false,
            'reminder_hours_before' => 24,
        ]);

        $service = BookingService::query()->where('restaurant_id', $restaurant->id)->firstOrFail();

        Reservation::query()->create([
            'restaurant_id' => $restaurant->id,
            'booking_service_id' => $service->id,
            'reservation_at' => now()->addHours(12),
            'covers' => 2,
            'customer_name' => 'Sans rappel',
            'customer_email' => 'sans-rappel@example.test',
            'customer_phone' => '0600000000',
            'status' => Reservation::STATUS_CONFIRMED,
            'source' => Reservation::SOURCE_SITE,
            'cancel_token' => bin2hex(random_bytes(24)),
        ]);

        Artisan::call('reservations:send-reminders');

        Mail::assertNothingQueued();
    }

    public function test_reservation_reminder_not_sent_for_pending_reservation(): void
    {
        Mail::fake();
        $this->seed(DatabaseSeeder::class);

        $restaurant = Restaurant::query()->firstOrFail();
        BookingSetting::query()->where('restaurant_id', $restaurant->id)->update([
            'reminder_enabled' => true,
            'reminder_hours_before' => 24,
        ]);

        $service = BookingService::query()->where('restaurant_id', $restaurant->id)->firstOrFail();

        Reservation::query()->create([
            'restaurant_id' => $restaurant->id,
            'booking_service_id' => $service->id,
            'reservation_at' => now()->addHours(12),
            'covers' => 2,
            'customer_name' => 'En attente',
            'customer_email' => 'pending-reminder@example.test',
            'customer_phone' => '0600000000',
            'status' => Reservation::STATUS_PENDING,
            'source' => Reservation::SOURCE_SITE,
            'cancel_token' => bin2hex(random_bytes(24)),
        ]);

        Artisan::call('reservations:send-reminders');

        Mail::assertNothingQueued();
    }

    public function test_site_page_view_is_recorded_for_tracked_public_route(): void
    {
        $this->seed(DatabaseSeeder::class);

        $restaurant = Restaurant::query()->firstOrFail();

        $this->get('/');

        $this->assertSame(1, SitePageView::query()->where('restaurant_id', $restaurant->id)->count());
        $this->assertDatabaseHas('site_page_views', [
            'restaurant_id' => $restaurant->id,
            'route_name' => 'site.home',
            'path' => '/',
        ]);
    }

    public function test_availability_json_route_does_not_increment_page_views(): void
    {
        $this->seed(DatabaseSeeder::class);

        $service = BookingService::query()->firstOrFail();
        $date = now()->addDays(3)->format('Y-m-d');

        $this->get('/reservation/availability?booking_service_id='.$service->id.'&reservation_date='.$date);

        $this->assertSame(0, SitePageView::query()->count());
    }
}
