<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuPhotoImport extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_PROCESSING = 'processing';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'restaurant_id',
        'disk',
        'path',
        'original_name',
        'mime',
        'size',
        'status',
        'draft_json',
        'error_message',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'processed_at' => 'datetime',
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
     * @return array<string, mixed>|null
     */
    public function draftArray(): ?array
    {
        if ($this->draft_json === null || $this->draft_json === '') {
            return null;
        }

        $decoded = json_decode($this->draft_json, true);

        return is_array($decoded) ? $decoded : null;
    }
}
