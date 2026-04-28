<?php

namespace Tests\Feature;

use App\Models\Restaurant;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SitePublicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_unpublished_site_returns_503(): void
    {
        $this->seed(DatabaseSeeder::class);

        Restaurant::query()->update(['published_at' => null]);

        $response = $this->get('/');

        $response->assertStatus(503);
        $response->assertSee('En ligne prochainement', false);
    }

    public function test_published_home_shows_tagline_from_restaurant(): void
    {
        $this->seed(DatabaseSeeder::class);

        Restaurant::query()->first()?->update([
            'tagline' => 'Slogan vitrine J2',
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Slogan vitrine J2', false);
    }
}
