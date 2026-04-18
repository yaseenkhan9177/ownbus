<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalarySlip extends Model
{
    protected $connection = 'tenant';
    protected $fillable = [
        'payroll_batch_id',
        'user_id',
        'base_salary',
        'total_additions',
        'total_deductions',
        'net_salary',
        'status',
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'total_additions' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'net_salary' => 'decimal:2',
    ];

    public function batch()
    {
        return $this->belongsTo(PayrollBatch::class, 'payroll_batch_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(SalaryItem::class);
    }
}
