<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'tenant';

    protected $fillable = [
        'parent_id',
        'account_code',
        'account_name',
        'account_type',
        'is_system',
        'is_active',
        'vat_applicable',
        'vat_rate',
    ];

    protected $casts = [
        'is_system' => 'boolean',
        'is_active' => 'boolean',
        'vat_applicable' => 'boolean',
        'vat_rate' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::deleting(function ($account) {
            if ($account->is_system) {
                throw new \Exception("System account '{$account->account_name}' cannot be deleted.");
            }

            if ($account->lines()->exists()) {
                throw new \Exception("Account '{$account->account_name}' cannot be deleted because it has journal entries.");
            }
        });
    }

    public function parent()
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Account::class, 'parent_id');
    }

    /**
     * Check if this is a leaf account (no children).
     * Enterprise Rule: Can only post to leaf accounts.
     */
    public function isLeaf(): bool
    {
        return !Account::where('parent_id', $this->id)->exists();
    }

    public function lines()
    {
        return $this->hasMany(JournalEntryLine::class, 'account_id');
    }
}
