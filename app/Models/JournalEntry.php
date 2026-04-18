<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JournalEntry extends Model
{
    protected $connection = 'tenant';
    use HasFactory, SoftDeletes;

    protected static function booted()
    {
        static::updating(function ($journal) {
            if ($journal->getOriginal('is_posted')) {
                // Allow updating reversed_by for linkage
                if ($journal->isDirty('reversed_by') && count($journal->getDirty()) === 1) {
                    return;
                }
                throw new \Exception("Cannot modify a posted journal entry. Please use the reversal system.");
            }
        });

        static::deleting(function ($journal) {
            if ($journal->is_posted && !$journal->isForceDeleting()) {
                throw new \Exception("Cannot delete a posted journal entry. Please use the reversal system.");
            }
        });
    }

    protected $fillable = [
        'branch_id',
        'vehicle_id',
        'date',
        'description',
        'reference_type',
        'reference_id',
        'is_posted',
        'posted_at',
        'created_by',
        'reversed_by',
        'reversal_of',
    ];

    protected $casts = [
        'date' => 'date',
        'is_posted' => 'boolean',
        'posted_at' => 'datetime',
    ];

    /**
     * Get the lines for this journal entry.
     */
    public function lines()
    {
        return $this->hasMany(JournalEntryLine::class, 'journal_entry_id');
    }

    /**
     * Associated vehicle for cost/revenue tagging.
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Polymorphic relationship to the source business transaction.
     */
    public function reference()
    {
        return $this->morphTo();
    }

    /**
     * Get the user who created this entry.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the branch this entry belongs to.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the reversal entry for this entry.
     */
    public function reversal()
    {
        return $this->belongsTo(JournalEntry::class, 'reversed_by');
    }

    /**
     * Get the original entry this is reversing.
     */
    public function originalEntry()
    {
        return $this->belongsTo(JournalEntry::class, 'reversal_of');
    }
}
