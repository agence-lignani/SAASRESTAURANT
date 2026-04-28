<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OpeningHourException extends Model
{
    protected $fillable = [
        'restaurant_id',
        'exception_date',
        'is_closed',
        'opens_at',
        'closes_at',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'exception_date' => 'date',
            'is_closed' => 'boolean',
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
