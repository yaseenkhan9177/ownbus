<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Ticket extends Model
{
    protected $connection = 'mysql';
    protected $fillable = [
        'uuid',
        'user_id',
        'subject',
        'status',
        'priority',
        'last_activity_at',
    ];

    protected $casts = [
        'last_activity_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid()->toString();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(TicketReply::class);
    }

    public function company()
    {
        return $this->hasOneThrough(
            Company::class,
            User::class,
            'id', // Foreign key on users table...
            'id', // Foreign key on companies table...
            'user_id', // Local key on tickets table...
            'company_id' // Local key on users table...
        );
    }
}
