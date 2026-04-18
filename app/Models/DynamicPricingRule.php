<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DynamicPricingRule extends Model
{
    protected $connection = 'tenant';
    use HasFactory;

    protected $table = 'dynamic_pricing_rules';

    protected $fillable = [
        'name',
        'rule_type',
        'conditions',
        'adjustment_type',
        'adjustment_value',
        'priority',
        'is_active',
    ];

    protected $casts = [
        'conditions' => 'array',
        'adjustment_value' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
