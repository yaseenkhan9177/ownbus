<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionChangeRequest extends Model
{
    protected $connection = 'mysql';

    use HasFactory;

    protected $fillable = [
        'subscription_id',
        'requested_plan_id',
        'scheduled_for',
        'status',
        'notes',
    ];

    protected $casts = [
        'scheduled_for' => 'datetime',
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function requestedPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'requested_plan_id');
    }

    public function approve()
    {
        $this->update(['status' => 'approved']);
    }

    public function complete()
    {
        $this->update(['status' => 'completed']);
    }
}
