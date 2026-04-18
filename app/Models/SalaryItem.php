<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryItem extends Model
{
    protected $connection = 'tenant';
    protected $fillable = [
        'salary_slip_id',
        'type',
        'label',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function slip()
    {
        return $this->belongsTo(SalarySlip::class, 'salary_slip_id');
    }
}
