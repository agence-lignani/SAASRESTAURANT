<?php

namespace Tests\Feature;

use App\Models\BookingService;
use App\Models\BookingSetting;
use App\Models\Reservation;
use App\Models\Restaurant;
use App\Services\Reservations\ExternalProviders\ExternalReservationProvider;
use App\Services\Reservations\ExternalReservationSyncService;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExternalReservationSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_sync_ignores_disabled_integrations(): void
    {
        $restaurant = Restaurant::query()->create([
            'name' => 'Test',
            'slug' => 'test',
            'public_host' => 'test.local',
        ]);

        BookingSetting::query()->create([
            'restaurant_id' => $restaurant->id,
            'external_integrations' => [
                'thefork' => ['enabled' => false, 'api_key' => 'abc', 'restaurant_reference' => 'r1'],
            ],
        ]);

        config()->set('reservations.providers.thefork', DisabledShouldNotBeCalledProvider::class);

        $result = app(ExternalReservationSyncService::class)->syncRestaurant($restaurant);

        $this->assertSame(['created' => 0, 'updated' => 0, 'skipped' => 0], $result);
        $this->assertDatabaseCount('reservations', 0);
    }

    public function test_sync_creates_and_updates_external_reservation(): void
    {
        $restaurant = Restaurant::query()->create([
            'name' => 'Test',
            'slug' => 'test',
            'public_host' => 'test.local',
        ]);

        $service = BookingService::query()->create([
            'restaurant_id' => $restaurant->id,
            'name' => 'Déjeuner',
            'starts_at' => '12:00:00',
            'ends_at' => '14:00:00',
            'capacity_covers' => 20,
            'days_of_week' => [1, 2, 3, 4, 5, 6],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        BookingSetting::query()->create([
            'restaurant_id' => $restaurant->id,
            'external_integrations' => [
                'thefork' => ['enabled' => true, 'api_key' => 'abc', 'restaurant_reference' => 'r1'],
            ],
        ]);

        config()->set('reservations.providers.thefork', FakeTheForkProvider::class);
        FakeTheForkProvider::$payload = [[
            'external_id' => 'tf_001',
            'reservation_at' => now()->addDay()->setHour(12)->setMinute(30)->toDateTimeString(),
            'covers' => 4,
            'customer_name' => 'Client Externe',
            'customer_email' => 'external@example.test',
            'customer_phone' => '0600000000',
            'status' => 'confirmed',
            'booking_service_id' => $service->id,
            'payload' => ['platform' => 'thefork'],
        ]];

        $first = app(ExternalReservationSyncService::class)->syncRestaurant($restaurant);
        $this->assertSame(1, $first['created']);

        FakeTheForkProvider::$payload = [[
            'external_id' => 'tf_001',
            'reservation_at' => now()->addDay()->setHour(12)->setMinute(30)->toDateTimeString(),
            'covers' => 6,
            'customer_name' => 'Client Externe',
            'customer_email' => 'external@example.test',
            'customer_phone' => '0600000000',
            'status' => 'confirmed',
            'booking_service_id' => $service->id,
            'payload' => ['platform' => 'thefork', 'updated' => true],
        ]];

        $second = app(ExternalReservationSyncService::class)->syncRestaurant($restaurant);
        $this->assertSame(1, $second['updated']);

        $this->assertDatabaseHas('reservations', [
            'restaurant_id' => $restaurant->id,
            'source' => Reservation::SOURCE_THEFORK,
            'external_id' => 'tf_001',
            'covers' => 6,
            'status' => Reservation::STATUS_CONFIRMED,
        ]);
    }
}

class FakeTheForkProvider implements ExternalReservationProvider
{
    /** @var array<int, array<string, mixed>> */
    public static array $payload = [];

    public function pull(Restaurant $restaurant, array $integrationConfig, CarbonImmutable $since): array
    {
        return self::$payload;
    }
}

class DisabledShouldNotBeCalledProvider implements ExternalReservationProvider
{
    public function pull(Restaurant $restaurant, array $integrationConfig, CarbonImmutable $since): array
    {
        throw new \RuntimeException('Provider should not be called when integration disabled.');
    }
}
