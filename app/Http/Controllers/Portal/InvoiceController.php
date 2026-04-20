<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Customer;
use App\Models\Rental;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Notifications\CustomerInvoiceNotification;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with('customer')->latest();

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('invoice_number', 'like', "%{$search}%")
                ->orWhereHas('customer', function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('company_name', 'like', "%{$search}%");
                });
        }

        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        $invoices = $query->paginate(15);

        return view('portal.invoices.index', compact('invoices'));
    }

    public function create()
    {
        $customers = Customer::all();
        $rentals = Rental::where('status', 'active')->get();
        $invoiceNumber = Invoice::generateInvoiceNumber();

        return view('portal.invoices.create', compact('customers', 'rentals', 'invoiceNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:tenant.customers,id',
            'rental_id' => 'nullable|exists:tenant.rentals,id',
            'due_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($validated) {
            $subtotal = 0;
            foreach ($validated['items'] as $item) {
                $subtotal += $item['quantity'] * $item['unit_price'];
            }

            $vatRate = 0.05; // 5% UAE VAT
            $vatAmount = round($subtotal * $vatRate, 2);
            $total = $subtotal + $vatAmount;

            $invoice = Invoice::create([
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'customer_id' => $validated['customer_id'],
                'rental_id' => $validated['rental_id'],
                'subtotal' => $subtotal,
                'vat_amount' => $vatAmount,
                'total' => $total,
                'status' => 'draft',
                'due_date' => $validated['due_date'],
                'notes' => $validated['notes'],
            ]);

            // Set QR data
            $invoice->update(['qr_code_data' => $invoice->getUaeQrData()]);

            foreach ($validated['items'] as $item) {
                $itemTotal = $item['quantity'] * $item['unit_price'];
                $itemVat = round($itemTotal * $vatRate, 2);
                
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'vat_rate' => 5.00,
                    'total' => $itemTotal + $itemVat,
                ]);
            }

            return redirect()->route('company.invoices.show', $invoice)
                ->with('success', 'Invoice generated successfully.');
        });
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['customer', 'items', 'rental']);
        return view('portal.invoices.show', compact('invoice'));
    }

    public function downloadPdf(Invoice $invoice)
    {
        $invoice->load(['customer', 'items']);
        $pdf = Pdf::loadView('portal.invoices.pdf', compact('invoice'));
        return $pdf->download("{$invoice->invoice_number}.pdf");
    }

    public function sendEmail(Invoice $invoice)
    {
        if (!$invoice->customer->email) {
            return back()->with('error', 'Customer has no email address.');
        }

        // Logic to send notification
        // $invoice->customer->notify(new CustomerInvoiceNotification($invoice));
        
        $invoice->update(['status' => 'sent']);

        return back()->with('success', 'Invoice sent successfully to ' . $invoice->customer->email);
    }
}
