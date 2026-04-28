<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteContactTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(PreventRequestForgery::class);
    }

    public function test_contact_form_accepts_valid_message(): void
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->from('/contact')->post('/contact', [
            'name' => 'Jean Test',
            'email' => 'jean@example.com',
            'phone' => '',
            'subject' => 'reservation',
            'body' => 'Bonjour, nous serions 4 samedi soir.',
            'website' => '',
        ]);

        $response->assertRedirect(route('site.contact'));
        $response->assertSessionHas('contact_ok');

        $this->assertDatabaseCount('contact_messages', 1);
        $this->assertDatabaseHas('contact_messages', [
            'email' => 'jean@example.com',
            'subject' => 'reservation',
        ]);
    }

    public function test_honeypot_does_not_persist_message(): void
    {
        $this->seed(DatabaseSeeder::class);

        $before = ContactMessage::query()->count();

        $response = $this->from('/contact')->post('/contact', [
            'name' => 'Spam',
            'email' => 'spam@example.com',
            'subject' => 'other',
            'body' => 'Buy now',
            'website' => 'http://evil.test',
        ]);

        $response->assertRedirect(route('site.contact'));
        $response->assertSessionHas('contact_ok');

        $this->assertSame($before, ContactMessage::query()->count());
    }

    public function test_contact_is_throttled_after_five_posts(): void
    {
        $this->seed(DatabaseSeeder::class);

        $payload = [
            'name' => 'Jean Test',
            'email' => 'jean@example.com',
            'subject' => 'feedback',
            'body' => 'Message de test.',
            'website' => '',
        ];

        for ($i = 0; $i < 5; $i++) {
            $this->from('/contact')->post('/contact', $payload)->assertRedirect();
        }

        $this->from('/contact')->post('/contact', $payload)->assertStatus(429);
    }
}
