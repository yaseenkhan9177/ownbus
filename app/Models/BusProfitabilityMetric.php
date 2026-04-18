<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusProfitabilityMetric extends Model
{
    protected $connection = 'tenant';
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'month_year',
        'total_revenue',
        'fuel_cost',
        'maintenance_cost',
        'net_profit',
        'days_rented',
        'total_km',
    ];

    protected $casts = [
        'total_revenue' => 'decimal:2',
        'fuel_cost' => 'decimal:2',
        'maintenance_cost' => 'decimal:2',
        'net_profit' => 'decimal:2',
        'total_km' => 'decimal:2',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
