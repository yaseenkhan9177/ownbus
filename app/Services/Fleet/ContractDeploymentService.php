<?php

namespace App\Services\Fleet;

use App\Models\Rental;
use App\Models\RentalPayment;
use App\Models\Contract;
use App\Models\ContractInvoice;
use App\Models\Vehicle;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Account;
use App\Models\Company;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ContractDeploymentService
{
    /**
     * Deploy a new contract (Atomic operation).
     */
    public function deploy(array $data, Company $company)
    {
        return DB::transaction(function () use ($data, $company) {
            // 1. Create Legal Contract Record
            $contract = $this->createContract($data, $company);

            // 2. Create Operational Rental Record
            $rental = $this->createRental($data, $company);

            // 3. Create Invoice Record
            $invoice = $this->createInvoice($rental, $contract, $data);

            // 4. Update Vehicle Status
            $rental->vehicle->update(['status' => 'rented']);

            // 5. Create Journal Entry
            $this->createJournalEntry($rental, $data);

            // 6. Create Payment Record (if amount paid)
            if (!empty($data['paid_amount']) && $data['paid_amount'] > 0) {
                RentalPayment::create([
                    'rental_id' => $rental->id,
                    'amount' => $data['paid_amount'],
                    'method' => $data['payment_method'],
                    'paid_at' => now(),
                ]);
            }

            return $rental;
        });
    }

    protected function createContract(array $data, Company $company): Contract
    {
        return Contract::on('tenant')->create([
            'branch_id'       => $data['branch_id'],
            'customer_id'     => $data['customer_id'],
            'vehicle_id'      => $data['vehicle_id'],
            'driver_id'       => $data['driver_id'] ?? null,
            'contract_number' => $data['contract_no'],
            'start_date'      => $data['start_date'],
            'end_date'        => $data['end_date'],
            'contract_value'  => $data['total_amount'],
            'monthly_rate'    => $data['rental_type'] === 'monthly' ? $data['base_rent'] : null,
            'billing_cycle'   => 'monthly',
            'status'          => 'active',
        ]);
    }

    protected function createRental(array $data, Company $company): Rental
    {
        return Rental::on('tenant')->create([
            'branch_id'        => $data['branch_id'],
            'customer_id'      => $data['customer_id'],
            'vehicle_id'       => $data['vehicle_id'],
            'driver_id'        => $data['driver_id'] ?? null,
            'contract_no'      => $data['contract_no'],
            'rental_number'    => $data['contract_no'],
            'rental_type'      => $data['rental_type'],
            'rate_type'        => $data['rental_type'],
            'rate_amount'      => $data['base_rent'],
            'start_date'       => $data['start_date'],
            'end_date'         => $data['end_date'],
            'status'           => $data['status'] ?? Rental::STATUS_ACTIVE,
            'security_deposit' => $data['security_deposit'] ?? 0,
            'discount'         => $data['discount'] ?? 0,
            'tax'              => $data['tax_amount'] ?? 0,
            'final_amount'     => $data['total_amount'],
            'created_by'       => Auth::id(),
            'uuid'             => (string) Str::uuid(),
        ]);
    }

    protected function createInvoice(Rental $rental, Contract $contract, array $data)
    {
        return ContractInvoice::on('tenant')->create([
            'contract_id'    => $contract->id,
            'customer_id'    => $rental->customer_id,
            'invoice_number' => 'INV-' . strtoupper(Str::random(8)),
            'period_start'   => $rental->start_date,
            'period_end'     => $rental->end_date,
            'due_date'       => $data['due_date'] ?? now()->addDays(7),
            'subtotal'       => $data['base_rent'] - ($data['discount'] ?? 0),
            'vat_amount'     => $data['tax_amount'] ?? 0,
            'total_amount'   => $data['total_amount'],
            'status'         => 'draft',
        ]);
    }

    protected function createJournalEntry(Rental $rental, array $data)
    {
        $entry = JournalEntry::on('tenant')->create([
            'branch_id'      => $rental->branch_id,
            'vehicle_id'     => $rental->vehicle_id,
            'date'           => now(),
            'description'    => "Rental Contract #{$rental->contract_no} — Customer: {$rental->customer->name}",
            'reference_type' => Rental::class,
            'reference_id'   => $rental->id,
            'is_posted'      => true,
            'posted_at'      => now(),
            'created_by'     => Auth::id(),
        ]);

        // Dr: Accounts Receivable (1013)
        // Cr: Rental Income (4010)
        $arAccount = Account::on('tenant')->where('account_code', '1013')->first();
        $incomeAccount = Account::on('tenant')->where('account_code', '4010')->first();

        if ($arAccount && $incomeAccount) {
            // Debit A/R
            JournalEntryLine::on('tenant')->create([
                'journal_entry_id' => $entry->id,
                'account_id'       => $arAccount->id,
                'debit'            => (float) $data['total_amount'],
                'credit'           => 0,
            ]);

            // Credit Income
            JournalEntryLine::on('tenant')->create([
                'journal_entry_id' => $entry->id,
                'account_id'       => $incomeAccount->id,
                'debit'            => 0,
                'credit'           => (float) ($data['base_rent'] - ($data['discount'] ?? 0)),
            ]);

            // Handle VAT if any
            if ($data['tax_amount'] > 0) {
                $vatAccount = Account::on('tenant')->where('account_code', '2013')->first();
                if ($vatAccount) {
                    JournalEntryLine::on('tenant')->create([
                        'journal_entry_id' => $entry->id,
                        'account_id'       => $vatAccount->id,
                        'debit'            => 0,
                        'credit'           => (float) $data['tax_amount'],
                    ]);
                }
            }
        }
    }
}
