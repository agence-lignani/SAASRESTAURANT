<?php

namespace Tests\Unit;

use App\Services\MenuImport\MenuOcrTextParser;
use PHPUnit\Framework\TestCase;

class MenuOcrTextParserTest extends TestCase
{
    public function test_parses_priced_lines_and_sections(): void
    {
        $parser = new MenuOcrTextParser;
        $text = <<<'TXT'
ENTREES
Velouté du potiron 7,50 €
Salade verte 9.00

PLATS
Plat du jour 16,00
TXT;

        $categories = $parser->categoriesFromText($text);

        $this->assertGreaterThanOrEqual(2, count($categories));
        $this->assertSame('ENTREES', $categories[0]['name']);
        $this->assertCount(2, $categories[0]['items']);
        $this->assertSame('Velouté du potiron', $categories[0]['items'][0]['name']);
        $this->assertSame('7.50', $categories[0]['items'][0]['price']);
        $this->assertSame('9.00', $categories[0]['items'][1]['price']);
    }

    public function test_price_first_format(): void
    {
        $parser = new MenuOcrTextParser;
        $categories = $parser->categoriesFromText("12,50 - Tartare de saumon\n");

        $this->assertNotEmpty($categories[0]['items']);
        $this->assertSame('Tartare de saumon', $categories[0]['items'][0]['name']);
        $this->assertSame('12.50', $categories[0]['items'][0]['price']);
    }

    public function test_does_not_treat_lowercase_dish_as_section(): void
    {
        $parser = new MenuOcrTextParser;
        $categories = $parser->categoriesFromText("Salade verte\nTomates 5,00\n");

        $names = array_map(fn (array $i) => $i['name'], $categories[0]['items']);
        $this->assertContains('Salade verte', $names);
        $this->assertContains('Tomates', $names);
    }

    public function test_splits_multiple_dishes_on_same_ocr_line_separated_by_prices(): void
    {
        $parser = new MenuOcrTextParser;
        $line = 'Demi jarret de cochon caramélisé, frites - 12,40 Foie de veau persillade, purée -';
        $categories = $parser->categoriesFromText($line);

        $items = $categories[0]['items'];
        $this->assertCount(2, $items);
        $this->assertStringContainsString('jarret', $items[0]['name']);
        $this->assertSame('12.40', $items[0]['price']);
        $this->assertStringContainsString('Foie de veau', $items[1]['name']);
        $this->assertNull($items[1]['price']);
    }

    public function test_splits_three_dishes_with_prices_on_one_line(): void
    {
        $parser = new MenuOcrTextParser;
        $categories = $parser->categoriesFromText('Tarte 8,00 Salade 7,50 Café 2,50');

        $items = $categories[0]['items'];
        $this->assertCount(3, $items);
        $this->assertSame('Tarte', $items[0]['name']);
        $this->assertSame('8.00', $items[0]['price']);
        $this->assertSame('Salade', $items[1]['name']);
        $this->assertSame('Café', $items[2]['name']);
        $this->assertSame('2.50', $items[2]['price']);
    }
}
