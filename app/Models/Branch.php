<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    protected $connection = 'tenant';
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'location',
        'phone',
        'email',
        'currency',
        'is_main',
    ];

    protected $casts = [
        'is_main' => 'boolean',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'branch_user')
            ->withPivot('role_id', 'is_active', 'assigned_at')
            ->withTimestamps();
    }
}
