<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SecurityDeposit extends Model
{
    protected $connection = 'tenant';
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'rental_id',
        'amount',
        'received_date',
        'refund_date',
        'status',
        'transaction_ref',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'received_date' => 'date',
        'refund_date' => 'date',
    ];

    public function rental()
    {
        return $this->belongsTo(Rental::class);
    }
}
