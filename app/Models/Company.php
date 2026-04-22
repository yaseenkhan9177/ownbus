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
}
