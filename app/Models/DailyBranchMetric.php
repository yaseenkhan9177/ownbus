<?php

namespace App\Models;

use App\Models\Traits\ScopedByBranch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyBranchMetric extends Model
{
    protected $connection = 'tenant';
    use HasFactory, ScopedByBranch;

    protected $fillable = [
        'branch_id',
        'date',
        'total_revenue',
        'total_expenses',
        'rentals_count',
        'active_vehicles_count',
    ];

    protected $casts = [
        'date' => 'date',
        'total_revenue' => 'decimal:2',
        'total_expenses' => 'decimal:2',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
