<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $connection = 'tenant';
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'vehicle_id',
        'vendor_id',
        'category',
        'description',
        'amount_ex_vat',
        'vat_percent',
        'vat_amount',
        'total_amount',
        'expense_date',
        'payment_method',
        'reference_no',
        'invoice_path',
        'is_posted',
        'posted_at',
        'created_by',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount_ex_vat' => 'decimal:2',
        'vat_percent' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get journal entries linked to this expense.
     */
    public function journalEntries()
    {
        return $this->morphMany(JournalEntry::class, 'reference');
    }
}
