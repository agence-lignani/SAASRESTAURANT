<?php

namespace Tests\Feature;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Restaurant;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteCarteTenantTest extends TestCase
{
    use RefreshDatabase;

    public function test_carte_shows_only_current_tenant_items(): void
    {
        $this->seed(DatabaseSeeder::class);

        $demo = Restaurant::query()->where('slug', 'bistro-demo')->firstOrFail();

        $other = Restaurant::query()->create([
            'name' => 'Autre lieu',
            'slug' => 'autre-lieu',
            'public_host' => 'autre-lieu.test',
            'published_at' => now(),
        ]);

        $cat = MenuCategory::query()->create([
            'restaurant_id' => $other->id,
            'name' => 'Spécialités',
            'sort_order' => 1,
        ]);

        MenuItem::query()->create([
            'restaurant_id' => $other->id,
            'menu_category_id' => $cat->id,
            'name' => 'Plat secret autre tenant',
            'price' => 99.00,
            'sort_order' => 1,
            'is_available' => true,
        ]);

        $this->get('http://'.$demo->public_host.'/carte')
            ->assertOk()
            ->assertSee('Velouté de saison', false)
            ->assertDontSee('Plat secret autre tenant', false);

        $this->get('http://'.$other->public_host.'/carte')
            ->assertOk()
            ->assertSee('Plat secret autre tenant', false)
            ->assertDontSee('Velouté de saison', false);
    }
}
