<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $connection = 'tenant';
    protected $table = 'user_notifications';

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'notifiable_type',
        'notifiable_id',
        'is_read',
        'urgency',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function user()
    {
        // Points back to central DB User
        return $this->belongsTo(\App\Models\User::class);
    }

    public function notifiable()
    {
        return $this->morphTo();
    }
}
