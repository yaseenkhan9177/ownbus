<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusUtilizationLog extends Model
{
    protected $connection = 'tenant';
    use HasFactory;

    protected $fillable = [
        'bus_id',
        'rental_id',
        'hours_used',
        'km_used',
        'fuel_consumed',
        'date',
    ];

    protected $casts = [
        'hours_used' => 'decimal:2',
        'km_used' => 'decimal:2',
        'fuel_consumed' => 'decimal:2',
        'date' => 'date',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'bus_id');
    }

    public function rental()
    {
        return $this->belongsTo(Rental::class);
    }
}
