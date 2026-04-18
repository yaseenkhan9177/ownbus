<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalEntryLine extends Model
{
    protected $connection = 'tenant';
    use HasFactory;

    protected $fillable = [
        'journal_entry_id',
        'account_id',
        'debit',
        'credit',
    ];

    protected $casts = [
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
    ];

    /**
     * Get the header entry.
     */
    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }

    /**
     * Legacy Alias for backward compatibility.
     * TransactionRepository expects a 'transaction' relationship.
     */
    public function transaction()
    {
        return $this->journalEntry();
    }

    /**
     * Get the associated account.
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
