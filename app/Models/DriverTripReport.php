<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverTripReport extends Model
{
    protected $connection = 'tenant';
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'rental_id',
        'type',
        'status',
        'notes',
        'photo_path',
        'metadata',
        'reported_at',
        'acknowledged_at',
    ];

    protected $casts = [
        'metadata'         => 'array',
        'reported_at'      => 'datetime',
        'acknowledged_at'  => 'datetime',
    ];

    // Type constants
    const TYPE_FUEL      = 'fuel_upload';
    const TYPE_BREAKDOWN = 'breakdown_report';
    const TYPE_STATUS    = 'trip_status';

    // Status constants
    const STATUS_PENDING      = 'pending';
    const STATUS_IN_PROGRESS  = 'in_progress';
    const STATUS_COMPLETED    = 'completed';
    const STATUS_ACKNOWLEDGED = 'acknowledged';
    public function scopeFuel($query)
    {
        return $query->where('type', self::TYPE_FUEL);
    }

    public function scopeBreakdowns($query)
    {
        return $query->where('type', self::TYPE_BREAKDOWN);
    }
}
