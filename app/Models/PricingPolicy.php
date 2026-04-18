<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PricingPolicy extends Model
{
    protected $connection = 'tenant';
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'name',
        'rental_type', // hourly, daily, etc.
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function rules()
    {
        return $this->hasMany(PricingRule::class);
    }
}
