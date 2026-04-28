<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingSetting extends Model
{
    protected $fillable = [
        'restaurant_id',
        'slot_minutes',
        'min_notice_hours',
        'max_days_ahead',
        'cancellation_hours',
        'allow_client_cancellation',
        'manual_confirmation_required',
        'reminder_enabled',
        'reminder_hours_before',
        'notification_emails',
        'external_integrations',
    ];

    protected function casts(): array
    {
        return [
            'allow_client_cancellation' => 'boolean',
            'manual_confirmation_required' => 'boolean',
            'reminder_enabled' => 'boolean',
            'notification_emails' => 'array',
            'external_integrations' => 'array',
        ];
    }

    /**
     * @return array{enabled: bool, api_key: ?string, restaurant_reference: ?string}
     */
    public function integration(string $provider): array
    {
        $config = $this->external_integrations[$provider] ?? [];

        return [
            'enabled' => (bool) ($config['enabled'] ?? false),
            'api_key' => isset($config['api_key']) && is_string($config['api_key']) ? trim($config['api_key']) : null,
            'restaurant_reference' => isset($config['restaurant_reference']) && is_string($config['restaurant_reference']) ? trim($config['restaurant_reference']) : null,
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
