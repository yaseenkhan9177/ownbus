<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    protected $connection = 'tenant';
    use HasFactory, SoftDeletes;

    // ─── Status Constants ──────────────────────────────────────────────────────
    public const STATUS_ACTIVE    = 'active';
    public const STATUS_SUSPENDED = 'suspended';

    // ─── Balance Direction Constants ───────────────────────────────────────────
    public const DIRECTION_PAYABLE    = 'payable';
    public const DIRECTION_RECEIVABLE = 'receivable';

    // ─── Boot ──────────────────────────────────────────────────────────────────
    protected static function booted(): void
    {
        static::observe(\App\Observers\VendorObserver::class);
    }

    // ─── Fillable ─────────────────────────────────────────────────────────────
    protected $fillable = [
        'branch_id',
        'vendor_code',
        'name',
        'contact_person',
        'phone',
        'email',
        'tax_number',
        'address',
        'city',
        'opening_balance',
        'balance_direction',
        'status',
        'created_by',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
    ];

    // ─── Relationships ─────────────────────────────────────────────────────────

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function bills()
    {
        return $this->hasMany(VendorBill::class);
    }

    /**
     * Payment journal entries (DR AP / CR Cash) linked to this vendor's bills.
     * We proxy through bills for payments — direct journal query used for balance.
     */
    public function maintenanceRecords()
    {
        return $this->hasMany(MaintenanceRecord::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    // ─── Business Logic ────────────────────────────────────────────────────────

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isSuspended(): bool
    {
        return $this->status === self::STATUS_SUSPENDED;
    }

    /**
     * Enterprise rule: Calculate outstanding balance from journal entries.
     * We NEVER rely on a stored balance column. opening_balance is allowed as seed.
     *
     * Outstanding = AP credits (bills approved) − AP debits (payments made)
     * We compute this from journal lines linked to this vendor's bills.
     */
    public function calculateOutstandingBalance(): float
    {
        $billIds = $this->bills()->pluck('id');

        if ($billIds->isEmpty()) {
            return (float) $this->opening_balance;
        }

        // Get the AP account lines for entries referencing this vendor's bills
        $journalEntries = JournalEntry::whereIn('reference_id', $billIds)
            ->where('reference_type', VendorBill::class)
            ->pluck('id');

        // AP account code is 2011
        $apAccountId = Account::where('account_code', '2011')
            ->value('id');

        if (!$apAccountId) {
            return (float) $this->opening_balance;
        }

        $credits = JournalEntryLine::whereIn('journal_entry_id', $journalEntries)
            ->where('account_id', $apAccountId)
            ->sum('credit');

        $debits = JournalEntryLine::whereIn('journal_entry_id', $journalEntries)
            ->where('account_id', $apAccountId)
            ->sum('debit');

        return (float) $this->opening_balance + ($credits - $debits);
    }
}
