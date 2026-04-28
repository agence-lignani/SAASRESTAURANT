<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuItem extends Model
{
    protected $fillable = [
        'restaurant_id',
        'menu_category_id',
        'name',
        'description',
        'price',
        'currency',
        'allergens',
        'dietary_flags',
        'sort_order',
        'is_available',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'allergens' => 'array',
            'dietary_flags' => 'array',
            'is_available' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Restaurant, $this>
     */
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    /**
     * @return BelongsTo<MenuCategory, $this>
     */
    public function menuCategory(): BelongsTo
    {
        return $this->belongsTo(MenuCategory::class);
    }

    protected static function booted(): void
    {
        static::saving(function (MenuItem $item): void {
            if (! $item->menu_category_id) {
                return;
            }

            $rid = MenuCategory::query()->whereKey($item->menu_category_id)->value('restaurant_id');
            if ($rid) {
                $item->restaurant_id = $rid;
            }
        });
    }
}
