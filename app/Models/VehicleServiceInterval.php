<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleServiceInterval extends Model
{
    protected $connection = 'tenant';
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'service_type',
        'interval_km',
        'interval_days',
        'last_service_odometer',
        'last_service_date',
        'next_due_odometer',
        'next_due_date',
    ];

    protected $casts = [
        'last_service_date' => 'date',
        'next_due_date' => 'date',
        'interval_km' => 'integer',
        'interval_days' => 'integer',
        'last_service_odometer' => 'integer',
        'next_due_odometer' => 'integer',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
