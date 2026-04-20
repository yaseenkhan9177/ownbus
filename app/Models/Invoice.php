<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    protected $connection = 'tenant';
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'customer_id',
        'rental_id',
        'subtotal',
        'vat_amount',
        'total',
        'status',
        'due_date',
        'paid_at',
        'notes',
        'qr_code_data',
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function rental()
    {
        return $this->belongsTo(Rental::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments()
    {
        return $this->hasMany(RentalPayment::class, 'rental_id', 'rental_id');
    }

    /**
     * Generate Invoice Number (INV-2026-0001 format)
     */
    public static function generateInvoiceNumber()
    {
        $year = date('Y');
        $lastInvoice = self::where('invoice_number', 'like', "INV-{$year}-%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastInvoice) {
            $lastNumber = explode('-', $lastInvoice->invoice_number);
            $number = intval(end($lastNumber)) + 1;
        } else {
            $number = 1;
        }

        return "INV-{$year}-" . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate UAE VAT QR Code Data (Simplified TLV-like or just URL for now)
     * Real UAE e-invoicing requires Base64 of TLV (Tag-Length-Value)
     */
    public function getUaeQrData()
    {
        // For demonstration, we'll store basic info. 
        // In reality, this would be a Base64 encoded string of TLV.
        $companyName = config('app.name');
        $trn = '123456789012345'; // Placeholder
        
        return base64_encode(
            chr(1) . chr(strlen($companyName)) . $companyName .
            chr(2) . chr(strlen($trn)) . $trn .
            chr(3) . chr(strlen($this->created_at->toIso8601String())) . $this->created_at->toIso8601String() .
            chr(4) . chr(strlen($this->total)) . $this->total .
            chr(5) . chr(strlen($this->vat_amount)) . $this->vat_amount
        );
    }
}
