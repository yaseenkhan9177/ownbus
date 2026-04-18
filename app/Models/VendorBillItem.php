<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorBillItem extends Model
{
    protected $connection = 'tenant';
    use HasFactory;

    protected $fillable = [
        'vendor_bill_id',
        'expense_account_id',
        'description',
        'quantity',
        'unit_cost',
        'total_cost',
    ];

    protected $casts = [
        'quantity'   => 'decimal:2',
        'unit_cost'  => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    /**
     * Auto-compute total_cost before saving.
     */
    protected static function booted(): void
    {
        static::saving(function (self $item) {
            $item->total_cost = round(
                (float) $item->quantity * (float) $item->unit_cost,
                2
            );
        });
    }

    public function bill()
    {
        return $this->belongsTo(VendorBill::class, 'vendor_bill_id');
    }

    public function expenseAccount()
    {
        return $this->belongsTo(Account::class, 'expense_account_id');
    }
}
