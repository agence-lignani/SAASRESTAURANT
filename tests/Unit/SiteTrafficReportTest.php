<?php

namespace Tests\Unit;

use App\Models\Restaurant;
use App\Models\SitePageView;
use App\Support\Analytics\SiteTrafficReport;
use Carbon\CarbonImmutable;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteTrafficReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_daily_counts_sum_to_total_within_window(): void
    {
        $this->seed(DatabaseSeeder::class);
        $restaurant = Restaurant::query()->firstOrFail();

        $today = CarbonImmutable::now();
        $yesterday = $today->subDay();

        SitePageView::query()->create([
            'restaurant_id' => $restaurant->id,
            'path' => '/',
            'route_name' => 'site.home',
            'viewed_at' => $today->setTime(10, 0),
        ]);
        SitePageView::query()->create([
            'restaurant_id' => $restaurant->id,
            'path' => '/carte',
            'route_name' => 'site.carte',
            'viewed_at' => $today->setTime(11, 0),
        ]);
        SitePageView::query()->create([
            'restaurant_id' => $restaurant->id,
            'path' => '/',
            'route_name' => 'site.home',
            'viewed_at' => $yesterday->setTime(9, 0),
        ]);

        $report = SiteTrafficReport::build($restaurant, 7);

        $this->assertSame(3, $report['total']);
        $this->assertSame(3, array_sum(array_column($report['daily'], 'count')));
        $this->assertCount(7, $report['daily']);
        $this->assertGreaterThanOrEqual(1, count($report['by_route']));
        $this->assertIsArray($report['charts']);
        $this->assertArrayHasKey('daily', $report['charts']);
        $this->assertCount(7, $report['charts']['daily']['datasets'][0]['data']);
    }
}
