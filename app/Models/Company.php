<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $connection = 'mysql';

    use HasFactory;

    protected static function booted()
    {
        static::observe(\App\Observers\CompanyObserver::class);
    }

    protected $fillable = [
        'name',
        'logo_path',
        'owner_name',
        'email',
        'phone',
        'trn_number',
        'trade_license_number',
        'total_vehicles',
        'country',
        'registration_source',
        'status',
        'agreed_to_terms',
        'address',
        'currency',
        'tax_rate',
        'invoice_prefix',
        'logo_url',
        'database_name', // Tenant-isolated MySQL database
        'trial_ends_at',
        'subscription_status',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
    ];

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function subscription()
    {
        return $this->hasOne(Subscription::class);
    }

    public function usageMetrics()
    {
        return $this->hasMany(UsageMetric::class);
    }

    public function owner()
    {
        return $this->hasOne(User::class)->oldest(); // Fallback to first registered user for the tenant
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    public function drivers()
    {
        return $this->hasMany(Driver::class);
    }

    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }

    public function notificationSetting()
    {
        return $this->hasOne(NotificationSetting::class);
    }

    public function companyNotificationSettings()
    {
        return $this->hasOne(CompanyNotificationSettings::class);
    }

    // Subscription Helper Attributes
    public function getDaysRemainingAttribute(): int
    {
        if ($this->subscription_status === 'active') {
            $sub = $this->subscription;
            if (!$sub || !$sub->current_period_end) return 0;
            return max(0, (int) now()->diffInDays($sub->current_period_end, false));
        }
        
        if ($this->subscription_status === 'trial') {
            if (!$this->trial_ends_at) return 0;
            return max(0, (int) now()->diffInDays($this->trial_ends_at, false));
        }
        
        return 0;
    }

    public function getSubscriptionBadgeColorAttribute(): string
    {
        $days = $this->days_remaining;
        
        if ($this->subscription_status === 'expired') return 'red';
        if ($this->subscription_status === 'suspended') return 'slate';
        
        if ($days <= 3) return 'red';
        if ($days <= 7) return 'orange';
        if ($days <= 14) return 'yellow';
        return 'green';
    }

    public function getSubscriptionLabelAttribute(): string
    {
        return match($this->subscription_status) {
            'trial' => 'FREE TRIAL',
            'active' => 'ACTIVE',
            'expired' => 'EXPIRED',
            'suspended' => 'SUSPENDED',
            default => 'UNKNOWN'
        };
    }
}
