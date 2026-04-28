<?php

namespace Tests\Unit;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Restaurant;
use App\Services\Chat\MenuContextBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuContextBuilderTest extends TestCase
{
    use RefreshDatabase;

    public function test_context_contains_only_menu_of_given_restaurant(): void
    {
        $a = Restaurant::query()->create([
            'name' => 'A',
            'slug' => 'a',
            'public_host' => 'a.test',
            'published_at' => now(),
        ]);
        $b = Restaurant::query()->create([
            'name' => 'B',
            'slug' => 'b',
            'public_host' => 'b.test',
            'published_at' => now(),
        ]);

        $catA = MenuCategory::query()->create([
            'restaurant_id' => $a->id,
            'name' => 'Plats A',
            'sort_order' => 1,
        ]);
        MenuItem::query()->create([
            'restaurant_id' => $a->id,
            'menu_category_id' => $catA->id,
            'name' => 'Plat alpha tenant A',
            'description' => 'Desc',
            'price' => 12,
            'currency' => 'EUR',
            'sort_order' => 1,
            'is_available' => true,
        ]);

        $catB = MenuCategory::query()->create([
            'restaurant_id' => $b->id,
            'name' => 'Plats B',
            'sort_order' => 1,
        ]);
        MenuItem::query()->create([
            'restaurant_id' => $b->id,
            'menu_category_id' => $catB->id,
            'name' => 'SECRET_TENANT_B_PLAT',
            'price' => 99,
            'currency' => 'EUR',
            'sort_order' => 1,
            'is_available' => true,
        ]);

        $text = (new MenuContextBuilder)->build($a);

        $this->assertStringContainsString('Plat alpha tenant A', $text);
        $this->assertStringNotContainsString('SECRET_TENANT_B_PLAT', $text);
    }
}
