<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenancePrediction extends Model
{
    protected $connection = 'tenant';
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'prediction_type',
        'predicted_date',
        'confidence_score',
        'reason',
        'status',
    ];

    protected $casts = [
        'predicted_date' => 'date',
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
