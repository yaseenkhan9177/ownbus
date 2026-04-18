<?php

namespace App\Models;

use App\Models\Traits\ScopedByCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $connection = 'mysql';

    use HasFactory, ScopedByCompany;

    protected $fillable = [
        'company_id',
        'plan_id',
        'plan_version',
        'status',
        'trial_ends_at',
        'grace_ends_at',
        'current_period_start',
        'current_period_end',
        'stripe_subscription_id',
        'stripe_customer_id',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'grace_ends_at' => 'datetime',
        'current_period_start' => 'datetime',
        'current_period_end' => 'datetime',
    ];

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    public function invoices()
    {
        return $this->hasMany(SubscriptionInvoice::class);
    }

    public function events()
    {
        return $this->hasMany(SubscriptionEvent::class);
    }

    public function changeRequests()
    {
        return $this->hasMany(SubscriptionChangeRequest::class);
    }

    // Helper Methods
    public function isActive(): bool
    {
        return in_array($this->status, ['trialing', 'active', 'grace']);
    }

    public function isTrialing(): bool
    {
        return $this->status === 'trialing';
    }

    public function isInGracePeriod(): bool
    {
        return $this->status === 'grace';
    }

    public function isSuspended(): bool
    {
        return in_array($this->status, ['suspended', 'canceled']);
    }

    public function canUseFeature(string $feature): bool
    {
        return $this->isActive() && $this->plan->hasFeature($feature);
    }
}
