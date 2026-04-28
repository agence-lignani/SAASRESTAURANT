<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RestaurantChatSetting extends Model
{
    protected $fillable = [
        'restaurant_id',
        'is_enabled',
        'system_prompt_extra',
        'max_user_message_length',
        'max_messages_per_session',
        'max_messages_per_day_per_ip',
        'history_tail_messages',
        'widget_position',
    ];

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Restaurant, $this>
     */
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }
}
