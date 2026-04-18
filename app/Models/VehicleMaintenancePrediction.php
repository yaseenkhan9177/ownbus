<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleMaintenancePrediction extends Model
{
    protected $connection = 'tenant';
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'predicted_service_date',
        'risk_level',
        'avg_km_per_day',
        'cost_growth_percentage',
        'interval_km',
        'calculated_at',
    ];

    protected $casts = [
        'predicted_service_date' => 'date',
        'calculated_at' => 'datetime',
        'avg_km_per_day' => 'decimal:2',
        'cost_growth_percentage' => 'decimal:2',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
