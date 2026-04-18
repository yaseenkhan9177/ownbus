<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FuelLog extends Model
{
    protected $connection = 'tenant';
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'vehicle_id',
        'vendor_id',
        'odometer_reading',
        'liters',
        'cost_per_liter',
        'total_amount',
        'date',
        'created_by',
    ];

    protected $casts = [
        'odometer_reading' => 'decimal:2',
        'liters' => 'decimal:2',
        'cost_per_liter' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'date' => 'date',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Customer::class, 'vendor_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
