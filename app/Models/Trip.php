<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Trip extends Model
{
    use HasFactory;

    protected $connection = 'tenant';

    const STATUS_PENDING     = 'pending';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED   = 'completed';
    const STATUS_CANCELLED   = 'cancelled';

    protected $fillable = [
        'uuid',
        'rental_id',
        'driver_id',
        'vehicle_id',
        'branch_id',
        'status',
        'scheduled_start',
        'scheduled_end',
        'actual_start',
        'actual_end',
        'duration_minutes',
        'odometer_start',
        'odometer_end',
        'distance_km',
        'pickup_location',
        'dropoff_location',
        'start_lat',
        'start_lng',
        'end_lat',
        'end_lng',
        'driver_notes',
        'driver_rating',
        'fuel_used_liters',
        'created_by',
    ];

    protected $casts = [
        'scheduled_start' => 'datetime',
        'scheduled_end'   => 'datetime',
        'actual_start'    => 'datetime',
        'actual_end'      => 'datetime',
        'start_lat'       => 'float',
        'start_lng'       => 'float',
        'end_lat'         => 'float',
        'end_lng'         => 'float',
    ];

    // ─── Relationships ─────────────────────────────────────────────

    public function rental()
    {
        return $this->belongsTo(Rental::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    // ─── Scopes ────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_IN_PROGRESS);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    // ─── Helpers ───────────────────────────────────────────────────

    /**
     * Calculate distance from odometer readings.
     */
    public function getCalculatedDistance(): ?int
    {
        if ($this->odometer_end && $this->odometer_start) {
            return max(0, $this->odometer_end - $this->odometer_start);
        }
        return null;
    }

    /**
     * Get human-readable duration.
     */
    public function getFormattedDuration(): string
    {
        if (!$this->duration_minutes) return '—';
        $h = intdiv($this->duration_minutes, 60);
        $m = $this->duration_minutes % 60;
        return $h > 0 ? "{$h}h {$m}m" : "{$m}m";
    }

    /**
     * Fuel efficiency in km/L for this trip.
     */
    public function getFuelEfficiency(): ?float
    {
        if ($this->fuel_used_liters > 0 && $this->distance_km > 0) {
            return round($this->distance_km / $this->fuel_used_liters, 2);
        }
        return null;
    }

    /**
     * Whether this trip is currently active.
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    /**
     * Whether this trip is done.
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }
}
