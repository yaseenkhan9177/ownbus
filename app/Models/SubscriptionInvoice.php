<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionInvoice extends Model
{
    protected $connection = 'mysql';

    use HasFactory;

    protected $fillable = [
        'subscription_id',
        'company_id',
        'amount',
        'currency',
        'status',
        'stripe_invoice_id',
        'due_date',
        'paid_at',
        'attempt_count',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function markAsPaid()
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);
    }

    public function incrementAttempt()
    {
        $this->increment('attempt_count');
    }
}
