<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverProfile extends Model
{
    protected $connection = 'tenant';
    use HasFactory;

    protected $fillable = [
        'user_id',
        'branch_id',
        'employment_type',
        'salary_type',
        'base_salary',
        'status',
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function documents()
    {
        return $this->hasMany(DriverDocument::class);
    }
}
