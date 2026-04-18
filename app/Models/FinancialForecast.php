<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialForecast extends Model
{
    protected $connection = 'tenant';
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'forecast_date',
        'metric_type',
        'predicted_value',
        'confidence_score',
    ];

    protected $casts = [
        'forecast_date' => 'date',
        'predicted_value' => 'decimal:2',
        'confidence_score' => 'decimal:2',
    ];
}
