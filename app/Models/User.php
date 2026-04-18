<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Traits\BelongsToCompany;
use App\Models\Traits\ScopedByCompany;
use Laravel\Sanctum\HasApiTokens;


use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    protected $connection = 'mysql';

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, BelongsToCompany, HasRoles; // Removed LogsActivity to prevent infinite loop

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'company_id',
        'role',
    ];

    protected static function booted()
    {
        static::deleting(function ($user) {
            if ($user->isSuperAdmin() && static::where('role', 'super_admin')->count() <= 1) {
                throw new \Exception('System Security: Cannot delete the last Super Admin.');
            }
        });

        static::updating(function ($user) {
            if ($user->isDirty('role') && $user->getOriginal('role') === 'super_admin' && $user->role !== 'super_admin') {
                if (static::where('role', 'super_admin')->count() <= 1) {
                    throw new \Exception('System Security: Cannot remove the Super Admin role from the last Super Admin.');
                }
            }
        });
    }

    public function company()
    {
        /** @var \App\Models\Company */
        return $this->belongsTo(Company::class);
    }

    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin()
    {
        return in_array($this->role, ['company_admin', 'admin']);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function driverProfile()
    {
        return $this->hasOne(DriverProfile::class);
    }

    public function user_notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
