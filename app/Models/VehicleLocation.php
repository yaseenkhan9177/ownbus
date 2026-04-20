<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleLocation extends Model
{
    protected $connection = 'tenant';
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'latitude',
        'longitude',
        'speed',
        'heading',
        'ignition_status',
        'accuracy',
        'source',
        'recorded_at',
    ];

    protected $casts = [
        'latitude'       => 'decimal:8',
        'longitude'      => 'decimal:8',
        'speed'          => 'decimal:2',
        'ignition_status' => 'boolean',
        'recorded_at'    => 'datetime',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the latest location for a given vehicle.
     */
    public static function latestFor(int $vehicleId): ?static
    {
        return static::where('vehicle_id', $vehicleId)
            ->orderByDesc('recorded_at')
            ->first();
    }
}
