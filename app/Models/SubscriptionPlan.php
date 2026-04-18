<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    protected $connection = 'mysql';

    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'version',
        'price_monthly',
        'price_yearly',
        'features',
        'is_active',
        'trial_days',
        'grace_period_days',
    ];

    protected $casts = [
        'features' => 'array',
        'price_monthly' => 'decimal:2',
        'price_yearly' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'plan_id');
    }

    public function hasFeature(string $feature): bool
    {
        return $this->features[$feature] ?? false;
    }

    public function getLimit(string $resource): ?int
    {
        return $this->features["max_{$resource}"] ?? null;
    }
}
