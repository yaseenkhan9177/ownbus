<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GpsDevice extends Model
{
    protected $connection = 'tenant';
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vehicle_id',
        'imei_number',
        'provider',
        'status',
    ];

    /**
     * Relationship: A GPS device belongs to a vehicle.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}
