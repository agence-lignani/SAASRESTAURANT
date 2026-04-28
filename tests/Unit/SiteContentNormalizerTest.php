<?php

namespace Tests\Unit;

use App\Support\SiteContent\PageSectionCatalog;
use App\Support\SiteContent\SiteContentNormalizer;
use PHPUnit\Framework\TestCase;

class SiteContentNormalizerTest extends TestCase
{
    public function test_unknown_root_keys_are_dropped(): void
    {
        $out = SiteContentNormalizer::normalize([
            'home' => ['hero' => ['title' => 'OK']],
            'intrusion_root' => ['x' => 1],
        ]);

        $this->assertArrayHasKey('home', $out);
        $this->assertArrayNotHasKey('intrusion_root', $out);
    }

    public function test_unknown_home_blocks_are_dropped(): void
    {
        $out = SiteContentNormalizer::normalize([
            'home' => [
                'hero' => ['title' => 'OK'],
                'custom_block_invente_par_client' => ['text' => 'spam'],
            ],
        ]);

        $this->assertArrayHasKey('home', $out);
        $this->assertArrayHasKey('hero', $out['home']);
        $this->assertArrayNotHasKey('custom_block_invente_par_client', $out['home']);
        $this->assertSame(PageSectionCatalog::defaultOrder('home'), $out['home']['section_order']);
    }

    public function test_invalid_section_order_falls_back_to_default(): void
    {
        $out = SiteContentNormalizer::normalize([
            'home' => [
                'hero' => ['title' => 'X'],
                'section_order' => ['hero'],
            ],
        ]);

        $this->assertSame(PageSectionCatalog::defaultOrder('home'), $out['home']['section_order']);
    }

    public function test_home_section_order_accepts_filament_repeater_shape(): void
    {
        $expected = PageSectionCatalog::defaultOrder('home');
        $repeater = array_map(
            fn (string $key): array => ['section' => $key],
            $expected,
        );

        $out = SiteContentNormalizer::normalize([
            'home' => [
                'hero' => ['title' => 'X'],
                'section_order' => $repeater,
            ],
        ]);

        $this->assertSame($expected, $out['home']['section_order']);
    }

    public function test_invalid_contact_section_order_falls_back_to_default(): void
    {
        $out = SiteContentNormalizer::normalize([
            'contact' => [
                'section_order' => ['header', 'form'],
            ],
        ]);

        $this->assertSame(PageSectionCatalog::defaultOrder('contact'), $out['contact']['section_order']);
    }
}
