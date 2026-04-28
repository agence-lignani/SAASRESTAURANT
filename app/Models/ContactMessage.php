<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactMessage extends Model
{
    public const SUBJECT_RESERVATION = 'reservation';

    public const SUBJECT_EVENT = 'event';

    public const SUBJECT_FEEDBACK = 'feedback';

    public const SUBJECT_OTHER = 'other';

    protected $fillable = [
        'restaurant_id',
        'name',
        'email',
        'phone',
        'subject',
        'body',
        'ip_address',
        'meta',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'read_at' => 'datetime',
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
     * @return array<string, string>
     */
    public static function subjectOptions(): array
    {
        return [
            self::SUBJECT_RESERVATION => 'Réservation / groupe',
            self::SUBJECT_EVENT => 'Évènement / privatisation',
            self::SUBJECT_FEEDBACK => 'Avis / suggestion',
            self::SUBJECT_OTHER => 'Autre demande',
        ];
    }
}
