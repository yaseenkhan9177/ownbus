<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeasonalPricingRule extends Model
{
    protected $connection = 'tenant';
    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'multiplier',
        'branch_id',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'multiplier' => 'float',
        'is_active' => 'boolean',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
