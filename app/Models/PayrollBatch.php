<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollBatch extends Model
{
    protected $connection = 'tenant';
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = [
        'branch_id',
        'period_name',
        'status',
        'total_net',
        'created_by',
    ];

    protected $casts = [
        'total_net' => 'decimal:2',
    ];

    public function slips()
    {
        return $this->hasMany(SalarySlip::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
