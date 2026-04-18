<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverPerformanceMetric extends Model
{
    protected $connection = 'tenant';
    use HasFactory;

    protected $fillable = [
        'user_id',
        'month_year',
        'trips_completed',
        'total_km_driven',
        'total_hours_driven',
        'fines_count',
        'safety_incidents',
        'safety_score',
    ];

    protected $casts = [
        'total_km_driven' => 'decimal:2',
        'total_hours_driven' => 'decimal:2',
        'safety_score' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
