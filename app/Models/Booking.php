<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $connection = 'tenant';
    protected $fillable = ['vehicle_id', 'pickup_time', 'dropoff_time', 'total_price', 'status'];

    protected $casts = [
        'pickup_time' => 'datetime',
        'dropoff_time' => 'datetime',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
