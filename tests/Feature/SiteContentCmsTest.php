<?php

namespace Tests\Feature;

use App\Models\Restaurant;
use App\Models\RestaurantPageContent;
use App\Support\SiteContent\PageSectionCatalog;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteContentCmsTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_uses_customized_content_when_defined_in_backoffice(): void
    {
        $this->seed(DatabaseSeeder::class);
        $restaurant = Restaurant::query()->firstOrFail();

        RestaurantPageContent::query()->create([
            'restaurant_id' => $restaurant->id,
            'content' => [
                'home' => [
                    'hero' => [
                        'title' => 'Titre home personnalisé',
                    ],
                    'manifesto' => [
                        'heading' => 'Manifeste personnalisé',
                    ],
                ],
            ],
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Titre home personnalisé', false);
        $response->assertSee('Manifeste personnalisé', false);
    }

    public function test_home_uses_fallback_when_no_custom_content_exists(): void
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Une table ancrée dans son quartier', false);
        $response->assertSee('Venir au restaurant', false);
    }

    public function test_contact_and_reservation_use_customizable_page_texts(): void
    {
        $this->seed(DatabaseSeeder::class);
        $restaurant = Restaurant::query()->firstOrFail();

        RestaurantPageContent::query()->create([
            'restaurant_id' => $restaurant->id,
            'content' => [
                'contact' => [
                    'title' => 'Contactez notre équipe',
                    'intro' => 'Texte d’introduction contact personnalisé.',
                ],
                'reservation' => [
                    'eyebrow' => 'Préparez votre venue',
                    'submit_label' => 'Valider ma réservation',
                ],
            ],
        ]);

        $contactResponse = $this->get('/contact');
        $contactResponse->assertOk();
        $contactResponse->assertSee('Contactez notre équipe', false);
        $contactResponse->assertSee('Texte d’introduction contact personnalisé.', false);

        $reservationResponse = $this->get('/reservation');
        $reservationResponse->assertOk();
        $reservationResponse->assertSee('Préparez votre venue', false);
        $reservationResponse->assertSee('Valider ma réservation', false);
    }

    public function test_carte_and_galerie_use_customizable_page_texts(): void
    {
        $this->seed(DatabaseSeeder::class);
        $restaurant = Restaurant::query()->firstOrFail();

        RestaurantPageContent::query()->create([
            'restaurant_id' => $restaurant->id,
            'content' => [
                'carte' => [
                    'title' => 'Carte du moment',
                    'intro' => 'Intro carte personnalisée.',
                ],
                'galerie' => [
                    'title' => 'Photos du restaurant',
                    'intro' => 'Intro galerie personnalisée.',
                ],
            ],
        ]);

        $carteResponse = $this->get('/carte');
        $carteResponse->assertOk();
        $carteResponse->assertSee('Carte du moment', false);
        $carteResponse->assertSee('Intro carte personnalisée.', false);

        $galerieResponse = $this->get('/galerie');
        $galerieResponse->assertOk();
        $galerieResponse->assertSee('Photos du restaurant', false);
        $galerieResponse->assertSee('Intro galerie personnalisée.', false);
    }

    public function test_existing_public_business_pages_still_render(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->get('/carte')->assertOk();
        $this->get('/galerie')->assertOk();
        $this->get('/reservation')->assertOk();
    }

    public function test_home_reflects_section_order_of_whole_blocks(): void
    {
        $this->seed(DatabaseSeeder::class);
        $restaurant = Restaurant::query()->firstOrFail();

        $order = PageSectionCatalog::keys('home');
        $order = array_merge(
            ['manifesto', 'hero'],
            array_values(array_diff($order, ['manifesto', 'hero']))
        );

        RestaurantPageContent::query()->create([
            'restaurant_id' => $restaurant->id,
            'content' => [
                'home' => [
                    'section_order' => $order,
                    'hero' => [
                        'title' => 'TITRE_HERO_ORDRE_TEST',
                    ],
                    'manifesto' => [
                        'heading' => 'HEADING_MANIFESTO_ORDRE_TEST',
                    ],
                ],
            ],
        ]);

        $html = $this->get('/')->assertOk()->getContent();
        $posManifesto = strpos($html, 'HEADING_MANIFESTO_ORDRE_TEST');
        $posHero = strpos($html, 'TITRE_HERO_ORDRE_TEST');
        $this->assertNotFalse($posManifesto);
        $this->assertNotFalse($posHero);
        $this->assertLessThan($posHero, $posManifesto);
    }

    public function test_contact_reflects_section_order_of_whole_blocks(): void
    {
        $this->seed(DatabaseSeeder::class);
        $restaurant = Restaurant::query()->firstOrFail();

        RestaurantPageContent::query()->create([
            'restaurant_id' => $restaurant->id,
            'content' => [
                'contact' => [
                    'section_order' => ['form', 'header', 'feedback'],
                    'title' => 'TITRE_CONTACT_ORDRE_TEST',
                    'label_message' => 'MESSAGE_CONTACT_ORDRE_TEST',
                ],
            ],
        ]);

        $html = $this->get('/contact')->assertOk()->getContent();
        $posForm = strpos($html, 'MESSAGE_CONTACT_ORDRE_TEST');
        $posHeader = strpos($html, 'TITRE_CONTACT_ORDRE_TEST');
        $this->assertNotFalse($posForm);
        $this->assertNotFalse($posHeader);
        $this->assertLessThan($posForm, $posHeader);
    }
}
