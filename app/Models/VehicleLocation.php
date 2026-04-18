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
        'lat',
        'lng',
        'speed',
        'heading',
        'accuracy',
        'source',
        'recorded_at',
    ];

    protected $casts = [
        'lat'         => 'decimal:7',
        'lng'         => 'decimal:7',
        'speed'       => 'decimal:2',
        'recorded_at' => 'datetime',
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
