<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    public const SOURCE_SITE = 'site';

    public const SOURCE_THEFORK = 'thefork';

    public const SOURCE_OPENTABLE = 'opentable';

    public const SOURCE_ZENCHEF = 'zenchef';

    public const STATUS_PENDING = 'pending';

    public const STATUS_CONFIRMED = 'confirmed';

    public const STATUS_REFUSED = 'refused';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_NO_SHOW = 'no_show';

    /** Retard signalé : l’horaire affiché est le nouveau créneau convenu en salle. */
    public const STATUS_DELAYED = 'delayed';

    /** Présence du client confirmée en salle (table honorée). */
    public const STATUS_ATTENDED = 'attended';

    protected $fillable = [
        'restaurant_id',
        'booking_service_id',
        'reservation_at',
        'covers',
        'customer_name',
        'customer_email',
        'customer_phone',
        'notes',
        'status',
        'source',
        'external_id',
        'external_payload',
        'synced_at',
        'confirmed_at',
        'refused_at',
        'cancelled_at',
        'cancel_token',
        'reminder_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'reservation_at' => 'datetime',
            'confirmed_at' => 'datetime',
            'refused_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'synced_at' => 'datetime',
            'external_payload' => 'array',
            'reminder_sent_at' => 'datetime',
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
     * @return BelongsTo<BookingService, $this>
     */
    public function bookingService(): BelongsTo
    {
        return $this->belongsTo(BookingService::class);
    }

    public function scopeCountedInCapacity(Builder $query): Builder
    {
        return $query->whereIn('status', [
            self::STATUS_PENDING,
            self::STATUS_CONFIRMED,
            self::STATUS_DELAYED,
        ]);
    }

    public function markStatusTimestamps(): void
    {
        $now = now();

        if ($this->status === self::STATUS_CONFIRMED && blank($this->confirmed_at)) {
            $this->confirmed_at = $now;
        }

        if ($this->status === self::STATUS_REFUSED && blank($this->refused_at)) {
            $this->refused_at = $now;
        }

        if ($this->status === self::STATUS_CANCELLED && blank($this->cancelled_at)) {
            $this->cancelled_at = $now;
        }
    }
}
