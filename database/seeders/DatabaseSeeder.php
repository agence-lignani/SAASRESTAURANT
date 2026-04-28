<?php

namespace Database\Seeders;

use App\Models\BookingService;
use App\Models\BookingSetting;
use App\Models\LegalPage;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\OpeningHour;
use App\Models\Restaurant;
use App\Models\RestaurantChatSetting;
use App\Models\RestaurantThemeSetting;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $restaurant = Restaurant::query()->create([
            'name' => 'Bistro démo',
            'slug' => 'bistro-demo',
            'public_host' => 'bistro-demo.test',
            'tagline' => 'Cuisine de saison · Ambiance bistrot',
            'contact_email' => 'contact@bistro-demo.test',
            'contact_phone' => '+33 1 23 45 67 89',
            'address_line1' => '12 rue du Marché',
            'city' => 'Paris',
            'postal_code' => '75000',
            'country' => 'France',
            'published_at' => now(),
        ]);

        RestaurantThemeSetting::query()->create([
            'restaurant_id' => $restaurant->id,
            'color_primary' => '#8B4513',
            'color_secondary' => '#2C1810',
            'color_text' => '#1a1a1a',
            'radius_sm' => 0.25,
            'radius_md' => 0.5,
            'radius_lg' => 1,
            'font_heading_key' => 'playfair-display',
            'font_body_key' => 'plus-jakarta-sans',
        ]);

        RestaurantChatSetting::query()->create([
            'restaurant_id' => $restaurant->id,
            'is_enabled' => true,
            'system_prompt_extra' => null,
            'max_user_message_length' => 2000,
            'max_messages_per_session' => 40,
            'max_messages_per_day_per_ip' => 80,
            'history_tail_messages' => 20,
            'widget_position' => 'bottom-end',
        ]);

        foreach (range(0, 6) as $dow) {
            $closed = in_array($dow, [0, 1], true);

            OpeningHour::query()->create([
                'restaurant_id' => $restaurant->id,
                'day_of_week' => $dow,
                'is_closed' => $closed,
                'opens_at' => $closed ? null : '12:00:00',
                'closes_at' => $closed ? null : '14:30:00',
            ]);
        }

        BookingSetting::query()->create([
            'restaurant_id' => $restaurant->id,
            'slot_minutes' => 30,
            'min_notice_hours' => 2,
            'max_days_ahead' => 30,
            'cancellation_hours' => 6,
            'allow_client_cancellation' => true,
            'manual_confirmation_required' => false,
            'reminder_enabled' => false,
            'reminder_hours_before' => 24,
            'notification_emails' => ['resa@bistro-demo.test'],
            'external_integrations' => [
                'thefork' => ['enabled' => false, 'restaurant_reference' => null, 'api_key' => null],
                'opentable' => ['enabled' => false, 'restaurant_reference' => null, 'api_key' => null],
                'zenchef' => ['enabled' => false, 'restaurant_reference' => null, 'api_key' => null],
            ],
        ]);

        BookingService::query()->create([
            'restaurant_id' => $restaurant->id,
            'name' => 'Déjeuner',
            'starts_at' => '12:00:00',
            'ends_at' => '14:30:00',
            'capacity_covers' => 40,
            'days_of_week' => [1, 2, 3, 4, 5, 6],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        BookingService::query()->create([
            'restaurant_id' => $restaurant->id,
            'name' => 'Dîner',
            'starts_at' => '19:00:00',
            'ends_at' => '22:30:00',
            'capacity_covers' => 50,
            'days_of_week' => [2, 3, 4, 5, 6],
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $owner = User::factory()->create([
            'name' => 'Admin démo',
            'family_name' => 'LIGNANI',
            'email' => 'admin@example.test',
            'password' => '123456',
        ]);

        LegalPage::query()->create([
            'restaurant_id' => $restaurant->id,
            'slug' => 'mentions-legales',
            'title' => 'Mentions légales',
            'body' => "Éditez ce texte depuis l'administration (Contenus légaux).\n\nResponsable de publication, hébergeur, propriété intellectuelle : à compléter selon votre situation.",
        ]);

        LegalPage::query()->create([
            'restaurant_id' => $restaurant->id,
            'slug' => 'politique-de-confidentialite',
            'title' => 'Politique de confidentialité',
            'body' => 'Politique de traitement des données personnelles : texte modèle à personnaliser (finalités, base légale, durées, droits RGPD, contact DPO le cas échéant).',
        ]);

        $owner->restaurants()->attach($restaurant->id, ['role' => 'owner']);

        $reservation = User::factory()->create([
            'name' => 'Salle démo',
            'family_name' => 'Salle',
            'email' => 'salle@example.test',
            'password' => '222222',
        ]);

        $reservation->restaurants()->attach($restaurant->id, ['role' => 'reservation']);

        $server = User::factory()->create([
            'name' => 'Serveur démo',
            'family_name' => 'Serveur',
            'email' => 'serveur@example.test',
            'password' => '333333',
        ]);

        $server->restaurants()->attach($restaurant->id, ['role' => 'server']);

        $entrees = MenuCategory::query()->create([
            'restaurant_id' => $restaurant->id,
            'name' => 'Entrées',
            'description' => 'Pour commencer en douceur.',
            'sort_order' => 1,
        ]);

        MenuItem::query()->create([
            'restaurant_id' => $restaurant->id,
            'menu_category_id' => $entrees->id,
            'name' => 'Velouté de saison',
            'description' => 'Légumes du marché, crème légère.',
            'price' => 8.50,
            'currency' => 'EUR',
            'allergens' => ['milk'],
            'dietary_flags' => ['vegetarian'],
            'sort_order' => 1,
            'is_available' => true,
        ]);

        $plats = MenuCategory::query()->create([
            'restaurant_id' => $restaurant->id,
            'name' => 'Plats',
            'description' => null,
            'sort_order' => 2,
        ]);

        MenuItem::query()->create([
            'restaurant_id' => $restaurant->id,
            'menu_category_id' => $plats->id,
            'name' => 'Pièce du boucher',
            'description' => 'Cuisson à point, garniture du jour.',
            'price' => 24.00,
            'currency' => 'EUR',
            'allergens' => null,
            'dietary_flags' => null,
            'sort_order' => 1,
            'is_available' => true,
        ]);
    }
}
