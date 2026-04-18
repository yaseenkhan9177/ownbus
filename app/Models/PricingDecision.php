<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingDecision extends Model
{
    protected $connection = 'tenant';
    protected $fillable = [
        'rental_uuid',
        'vehicle_id',
        'branch_id',
        'customer_id',
        'base_rate',
        'optimized_rate',
        'multipliers_json',
        'was_accepted',
        'final_margin',
    ];

    protected $casts = [
        'multipliers_json' => 'json',
        'base_rate' => 'float',
        'optimized_rate' => 'float',
        'final_margin' => 'float',
        'was_accepted' => 'boolean',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
