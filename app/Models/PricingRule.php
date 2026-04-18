<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingRule extends Model
{
    protected $connection = 'tenant';
    use HasFactory;

    protected $table = 'pricing_rules';

    protected $fillable = [
        'pricing_policy_id',
        'rule_type',
        'value',
        'calculation_method',
    ];

    protected $casts = [
        'value' => 'decimal:2',
    ];

    public function policy()
    {
        return $this->belongsTo(PricingPolicy::class, 'pricing_policy_id');
    }
}
