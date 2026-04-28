<?php

namespace App\Services\MenuImport;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Restaurant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;

final class ApplyMenuDraftToMenu
{
    /**
     * Crée catégories et plats à partir du brouillon (ajout en fin de liste).
     *
     * @param  array<string, mixed>  $draft
     */
    public function apply(Restaurant $restaurant, array $draft): int
    {
        $validator = Validator::make($draft, [
            'categories' => ['required', 'array', 'min:1'],
            'categories.*.name' => ['required', 'string', 'max:255'],
            'categories.*.items' => ['nullable', 'array'],
            'categories.*.items.*.name' => ['required_with:categories.*.items', 'string', 'max:255'],
            'categories.*.items.*.price' => ['nullable', 'string', 'max:32'],
            'categories.*.items.*.description' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors()->first());
        }

        $createdItems = 0;

        DB::transaction(function () use ($restaurant, $draft, &$createdItems): void {
            $catOrder = (int) MenuCategory::query()->where('restaurant_id', $restaurant->id)->max('sort_order');

            foreach ($draft['categories'] as $cat) {
                $catOrder++;
                $category = MenuCategory::query()->create([
                    'restaurant_id' => $restaurant->id,
                    'name' => $cat['name'],
                    'description' => $cat['description'] ?? null,
                    'sort_order' => $catOrder,
                ]);

                $itemOrder = 0;
                foreach ($cat['items'] ?? [] as $row) {
                    $itemOrder++;
                    $price = null;
                    if (! empty($row['price'])) {
                        $price = (float) str_replace(',', '.', preg_replace('/[^\d,.-]/', '', (string) $row['price']));
                    }

                    MenuItem::query()->create([
                        'restaurant_id' => $restaurant->id,
                        'menu_category_id' => $category->id,
                        'name' => $row['name'],
                        'description' => $row['description'] ?? null,
                        'price' => $price,
                        'sort_order' => $itemOrder,
                    ]);
                    $createdItems++;
                }
            }
        });

        return $createdItems;
    }
}
