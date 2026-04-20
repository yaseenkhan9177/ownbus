<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    protected $connection = 'tenant';
    use HasFactory, Auditable, SoftDeletes;

    protected static function booted()
    {
        static::observe(\App\Observers\VehicleCacheObserver::class);
    }

    const STATUS_AVAILABLE = 'available';
    const STATUS_RENTED = 'rented';
    const STATUS_MAINTENANCE = 'maintenance';
    const STATUS_INACTIVE = 'inactive';

    protected $casts = [
        'year' => 'integer',
        'current_odometer' => 'integer',
        'last_service_odometer' => 'integer',
        'next_service_odometer' => 'integer',
        'registration_expiry' => 'date',
        'insurance_expiry' => 'date',
        'inspection_expiry_date' => 'date',
        'purchase_date' => 'date',
        'last_gps_ping_at' => 'datetime',
        'is_available'     => 'boolean',
        'daily_rate'       => 'decimal:2',
        'asset_value'      => 'decimal:2',
        'depreciation_rate' => 'decimal:2',
        'deleted_at'       => 'datetime',
    ];

    protected $fillable = [
        'branch_id',
        'vendor_id',
        'name',
        'gps_imei',
        'vehicle_number',
        'make',
        'model',
        'color',
        'year',
        'type',
        'seating_capacity',
        'fuel_type',
        'transmission',
        'status',
        'ownership_type',
        'asset_value',
        'depreciation_method',
        'purchase_price',
        'purchase_date',
        'registration_expiry',
        'insurance_expiry',
        'inspection_expiry_date',
        'current_odometer',
        'last_service_odometer',
        'next_service_odometer',
        'telematics_device_id',
        'tracking_status',
        'last_gps_ping_at',
        'daily_rate',
        'image_path',
        'notes',
    ];

    // GPS tracking status constants
    const TRACKING_LIVE    = 'live';
    const TRACKING_OFFLINE = 'offline';
    const TRACKING_UNKNOWN = 'unknown';

    public function isLive(): bool
    {
        return $this->tracking_status === self::TRACKING_LIVE;
    }

    public function locations()
    {
        return $this->hasMany(\App\Models\VehicleLocation::class);
    }

    public function latestLocation()
    {
        return $this->hasOne(\App\Models\VehicleLocation::class)->latestOfMany('recorded_at');
    }

    /**
     * Check if any document is expiring within $days days or already expired.
     */
    public function hasExpiringDocument(int $days = 30): bool
    {
        $threshold = now()->addDays($days);
        return ($this->registration_expiry && $this->registration_expiry->lte($threshold))
            || ($this->insurance_expiry && $this->insurance_expiry->lte($threshold))
            || ($this->inspection_expiry_date && $this->inspection_expiry_date->lte($threshold));
    }

    /**
     * Get all expiring documents with label and expiry date.
     */
    public function getExpiringDocuments(int $days = 30): array
    {
        $threshold = now()->addDays($days);
        $docs = [];
        $checks = [
            'Mulkiya (Registration)' => $this->registration_expiry,
            'Insurance' => $this->insurance_expiry,
            'Inspection' => $this->inspection_expiry_date,
        ];
        foreach ($checks as $label => $date) {
            if ($date && $date->lte($threshold)) {
                $docs[] = [
                    'label' => $label,
                    'expiry' => $date,
                    'is_expired' => $date->isPast(),
                    'days_left' => max(0, (int) now()->diffInDays($date, false)),
                ];
            }
        }
        return $docs;
    }

    public function bookings()
    {
        /** @var \App\Models\Booking */
        return $this->hasMany(Booking::class);
    }

    public function unavailabilities()
    {
        return $this->hasMany(VehicleUnavailability::class);
    }

    public function rentals()
    {
        return $this->hasMany(Rental::class, 'vehicle_id');
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function fines()
    {
        return $this->hasMany(VehicleFine::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function maintenanceRecords()
    {
        return $this->hasMany(\App\Models\VehicleUnavailability::class, 'vehicle_id');
    }

    /**
     * High-density UI Accessors
     */
    public function getAvgRentalAttribute()
    {
        $count = $this->rentals()->count();
        return $count > 0 ? ($this->total_revenue ?? 0) / $count : 0;
    }

    public function getUtilizationRateAttribute()
    {
        // Simple heuristic for demo purposes: 
        // In a real system, this would calculate overlap of rental durations over a period.
        // Returning a semi-stable value based on rental count for visualization.
        $count = $this->rentals()->count();
        return min(100, $count * 5 + 65); // Realistic looking baseline
    }
}
