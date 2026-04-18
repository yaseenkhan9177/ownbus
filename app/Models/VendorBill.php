<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorBill extends Model
{
    protected $connection = 'tenant';
    use HasFactory, SoftDeletes;

    // ─── Status Constants ──────────────────────────────────────────────────────
    public const STATUS_DRAFT          = 'draft';
    public const STATUS_APPROVED       = 'approved';
    public const STATUS_PARTIALLY_PAID = 'partially_paid';
    public const STATUS_PAID           = 'paid';
    public const STATUS_CANCELLED      = 'cancelled';

    protected $fillable = [
        'branch_id',
        'vendor_id',
        'bill_number',
        'bill_date',
        'due_date',
        'total_amount',
        'tax_amount',
        'status',
        'description',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'bill_date'   => 'date',
        'due_date'    => 'date',
        'approved_at' => 'datetime',
        'total_amount' => 'decimal:2',
        'tax_amount'   => 'decimal:2',
    ];

    // ─── Relationships ─────────────────────────────────────────────────────────

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function items()
    {
        return $this->hasMany(VendorBillItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get journal entries linked to this bill (via polymorphic reference).
     */
    public function journalEntries()
    {
        return $this->morphMany(JournalEntry::class, 'reference');
    }

    /**
     * Get the primary approval journal entry (DR Expense / CR AP).
     */
    public function approvalJournalEntry()
    {
        return $this->morphOne(JournalEntry::class, 'reference')
            ->where('description', 'like', 'Bill Approved%');
    }

    // ─── Business Logic ────────────────────────────────────────────────────────

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isApproved(): bool
    {
        return in_array($this->status, [
            self::STATUS_APPROVED,
            self::STATUS_PARTIALLY_PAID,
        ]);
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function canBeEdited(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function canBeApproved(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function canBePaid(): bool
    {
        return in_array($this->status, [
            self::STATUS_APPROVED,
            self::STATUS_PARTIALLY_PAID,
        ]);
    }

    public function canBeCancelled(): bool
    {
        return !in_array($this->status, [
            self::STATUS_PAID,
            self::STATUS_CANCELLED,
        ]);
    }

    /**
     * Compute how much has been paid (sum of AP debit lines in payment journals).
     */
    public function paidAmount(): float
    {
        $apAccount = Account::where('account_code', '2011')
            ->value('id');

        if (!$apAccount) {
            return 0;
        }

        $journalIds = $this->journalEntries()
            ->where('description', 'like', 'Payment%')
            ->pluck('id');

        return (float) JournalEntryLine::whereIn('journal_entry_id', $journalIds)
            ->where('account_id', $apAccount)
            ->sum('debit');
    }

    public function remainingAmount(): float
    {
        return max(0, (float) $this->total_amount - $this->paidAmount());
    }

    /**
     * Status badge color for UI.
     */
    public function statusColor(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT          => 'secondary',
            self::STATUS_APPROVED       => 'primary',
            self::STATUS_PARTIALLY_PAID => 'warning',
            self::STATUS_PAID           => 'success',
            self::STATUS_CANCELLED      => 'danger',
            default                     => 'secondary',
        };
    }
}
