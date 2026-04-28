<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RestaurantThemeSetting extends Model
{
    protected $fillable = [
        'restaurant_id',
        'color_primary',
        'color_secondary',
        'color_text',
        'radius_sm',
        'radius_md',
        'radius_lg',
        'font_heading_key',
        'font_body_key',
    ];

    /**
     * @return BelongsTo<Restaurant, $this>
     */
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }
}
