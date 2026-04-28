<?php

namespace Tests\Feature;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Restaurant;
use App\Models\RestaurantChatSetting;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ChatAssistantTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(PreventRequestForgery::class);
    }

    public function test_chat_is_disabled_without_settings_or_flag(): void
    {
        $this->seed(DatabaseSeeder::class);

        $demo = Restaurant::query()->where('slug', 'bistro-demo')->firstOrFail();

        RestaurantChatSetting::query()->where('restaurant_id', $demo->id)->delete();

        $this->post('http://'.$demo->public_host.'/chat/message', [
            'message' => 'Bonjour',
        ])->assertStatus(404);
    }

    public function test_chat_returns_reply_when_enabled_with_fake_llm(): void
    {
        $this->seed(DatabaseSeeder::class);

        $demo = Restaurant::query()->where('slug', 'bistro-demo')->firstOrFail();

        $res = $this->postJson('http://'.$demo->public_host.'/chat/message', [
            'message' => 'Y a-t-il un plat végétarien ?',
        ]);

        $res->assertOk()
            ->assertJsonStructure(['reply', 'session_token', 'disclaimer']);

        $this->assertDatabaseHas('chat_sessions', [
            'restaurant_id' => $demo->id,
        ]);
    }

    public function test_openai_payload_contains_only_current_tenant_menu_items(): void
    {
        config(['llm.driver' => 'openai_compat']);
        config(['llm.openai_compat.api_key' => 'test-key']);
        config(['llm.openai_compat.base_url' => 'https://api.example.test/v1']);

        $this->seed(DatabaseSeeder::class);
        $demo = Restaurant::query()->where('slug', 'bistro-demo')->firstOrFail();

        $autre = Restaurant::query()->create([
            'name' => 'Autre',
            'slug' => 'autre',
            'public_host' => 'autre.test',
            'published_at' => now(),
        ]);

        $catAutre = MenuCategory::query()->create([
            'restaurant_id' => $autre->id,
            'name' => 'Secrets',
            'sort_order' => 1,
        ]);
        MenuItem::query()->create([
            'restaurant_id' => $autre->id,
            'menu_category_id' => $catAutre->id,
            'name' => 'PLAT_CONFIDENTIEL_AUTRE_TENANT',
            'price' => 50,
            'currency' => 'EUR',
            'sort_order' => 1,
            'is_available' => true,
        ]);

        Http::fake([
            'https://api.example.test/v1/chat/completions' => Http::response([
                'choices' => [
                    ['message' => ['content' => 'Réponse synthétique.']],
                ],
            ], 200),
        ]);

        $this->postJson('http://'.$demo->public_host.'/chat/message', [
            'message' => 'Bonjour',
        ])->assertOk();

        Http::assertSent(function ($request): bool {
            $body = json_decode($request->body(), true);
            $sys = '';
            foreach (data_get($body, 'messages', []) as $m) {
                if (($m['role'] ?? '') === 'system') {
                    $sys .= (string) ($m['content'] ?? '');
                }
            }

            return str_contains($sys, 'Velouté de saison')
                && ! str_contains($sys, 'PLAT_CONFIDENTIEL_AUTRE_TENANT');
        });
    }

    public function test_session_message_quota_returns_429(): void
    {
        $this->seed(DatabaseSeeder::class);
        $demo = Restaurant::query()->where('slug', 'bistro-demo')->firstOrFail();

        RestaurantChatSetting::query()
            ->where('restaurant_id', $demo->id)
            ->update(['max_messages_per_session' => 2]);

        $token = null;
        for ($i = 0; $i < 2; $i++) {
            $r = $this->postJson('http://'.$demo->public_host.'/chat/message', array_filter([
                'message' => 'msg '.$i,
                'session_token' => $token,
            ]));
            $r->assertOk();
            $token = $r->json('session_token');
        }

        $this->postJson('http://'.$demo->public_host.'/chat/message', [
            'message' => 'overflow',
            'session_token' => $token,
        ])->assertStatus(429);
    }
}
