<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentalStatusLog extends Model
{
    protected $connection = 'tenant';
    use HasFactory;

    // No timestamps needed if we only rely on created_at?
    // Migration has 'created_at' and no updated_at usually
    public $timestamps = false; // Check migration: table->timestamp('created_at')->useCurrent();

    protected $fillable = [
        'rental_id',
        'from_status',
        'to_status',
        'changed_by',
        'reason',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function rental()
    {
        return $this->belongsTo(Rental::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
