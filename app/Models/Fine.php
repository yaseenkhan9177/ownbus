<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fine extends Model
{
    protected $connection = 'tenant';
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vehicle_id',
        'driver_id',
        'rental_id',
        'authority',
        'reference_number',
        'amount',
        'fine_datetime',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fine_datetime' => 'datetime',
    ];

    public function bus()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function rental()
    {
        return $this->belongsTo(Rental::class);
    }
}
