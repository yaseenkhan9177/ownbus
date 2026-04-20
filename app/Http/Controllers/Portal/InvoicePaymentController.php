<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\RentalPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoicePaymentController extends Controller
{
    public function store(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01|max:' . ($invoice->total - $invoice->payments->sum('amount')),
            'payment_method' => 'required|string',
            'reference' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($validated, $invoice) {
            // If the invoice is linked to a rental, we should ideally record it as a rental payment too
            // But for simplicity and direct invoice payment:
            
            // This app seems to use RentalPayment model for customer payments
            RentalPayment::create([
                'rental_id' => $invoice->rental_id ?? null,
                'customer_id' => $invoice->customer_id,
                'amount' => $validated['amount'],
                'payment_date' => $validated['payment_date'],
                'payment_method' => $validated['payment_method'],
                'reference_number' => $validated['reference'],
                'status' => 'completed',
                'notes' => 'Payment for Invoice: ' . $invoice->invoice_number,
            ]);

            $totalPaid = $invoice->payments()->sum('amount') + $validated['amount'];
            
            if ($totalPaid >= $invoice->total) {
                $invoice->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                ]);
            }

            return back()->with('success', 'Payment recorded successfully.');
        });
    }
}
