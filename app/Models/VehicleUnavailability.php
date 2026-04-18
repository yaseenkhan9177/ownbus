<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleUnavailability extends Model
{
    protected $connection = 'tenant';
    use HasFactory;

    protected static function booted()
    {
        static::observe(\App\Observers\VehicleUnavailabilityObserver::class);
    }

    protected $fillable = [
        'vehicle_id',
        'start_datetime',
        'end_datetime',
        'reason_type',
        'description',
        'created_by',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
