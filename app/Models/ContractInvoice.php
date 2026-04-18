<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractInvoice extends Model
{
    protected $connection = 'tenant';
    use HasFactory, Auditable, SoftDeletes;

    protected $fillable = [
        'contract_id',
        'customer_id',
        'journal_entry_id',
        'invoice_number',
        'period_start',
        'period_end',
        'due_date',
        'subtotal',
        'vat_amount',
        'total_amount',
        'status',
        'notes',
    ];

    protected $casts = [
        'period_start'   => 'date',
        'period_end'     => 'date',
        'due_date'       => 'date',
        'subtotal'       => 'decimal:2',
        'vat_amount'     => 'decimal:2',
        'total_amount'   => 'decimal:2',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function isOverdue(): bool
    {
        return $this->status === 'draft' && $this->due_date?->isPast();
    }
}
