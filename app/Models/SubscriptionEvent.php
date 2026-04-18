<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionEvent extends Model
{
    protected $connection = 'mysql';

    use HasFactory;

    protected $fillable = [
        'subscription_id',
        'event_type',
        'payload_json',
        'stripe_event_id',
    ];

    protected $casts = [
        'payload_json' => 'array',
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Check if this event has already been processed (idempotency).
     */
    public static function isProcessed(string $stripeEventId): bool
    {
        return static::where('stripe_event_id', $stripeEventId)->exists();
    }
}
