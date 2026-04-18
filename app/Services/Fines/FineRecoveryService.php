<?php

namespace App\Services\Fines;

use App\Models\Account;
use App\Models\Company;
use App\Models\VehicleFine;
use App\Services\AccountingService;
use App\Traits\LogsEvents;
use App\Services\ExpenseService;
use App\Services\EventLoggerService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Exception;

/**
 * Fine Recovery Service — Production Grade
 *
 * Business Flow:
 *  1. Company pays fine to traffic authority        → DR Traffic Fine Expense / CR Cash
 *  2. Fine marked as "paid" + customer_responsible  → THIS SERVICE FIRES
 *  3. System recovers amount from customer           → DR Accounts Receivable / CR Fine Recovery Income
 *
 * Design Guarantees:
 *  ✅ Idempotent        — skips if journal_entry_id already set
 *  ✅ No VAT            — traffic fines are government penalties, not taxable supplies
 *  ✅ Ledger-truth      — every recovery has a balanced journal entry
 *  ✅ Audit trail       — reference_type/reference_id links journal to fine
 *  ✅ DB transactional  — fine update + journal create in one atomic unit
 */
class FineRecoveryService
{
    use LogsEvents;

    protected AccountingService $accounting;

    // Chart of Accounts codes
    const ACCOUNT_AR            = '1013'; // Accounts Receivable
    const ACCOUNT_FINE_RECOVERY = '4099'; // Fine Recovery Income (no VAT)

    public function __construct(AccountingService $accounting)
    {
        $this->accounting = $accounting;
    }

    // ─────────────────────────────────────────────────────────────
    // Primary method — called by VehicleFineObserver::updated()
    // ─────────────────────────────────────────────────────────────

    /**
     * Process customer recovery for a fine where:
     *   - customer_responsible = true
     *   - status = 'paid' (company paid the authority, now recover from customer)
     *
     * Idempotent: safe to call multiple times.
     */
    public function processCustomerRecovery(VehicleFine $fine): void
    {
        // ── GUARD 1: Must be customer-responsible ────────────────
        if (!$fine->customer_responsible) {
            return;
        }

        // ── GUARD 2: Must have a customer linked ─────────────────
        if (!$fine->customer_id) {
            Log::warning("FineRecovery: Fine #{$fine->fine_number} has no customer linked. Cannot recover.");
            return;
        }

        // ── GUARD 3: Idempotency — already recovered ─────────────
        if ($fine->journal_entry_id) {
            Log::info("FineRecovery: Fine #{$fine->fine_number} already recovered (JE #{$fine->journal_entry_id}). Skipping.");
            return;
        }

        // ── GUARD 4: Status must be paid or pending ───────────────
        if (!in_array($fine->status, ['paid', 'pending'])) {
            Log::info("FineRecovery: Fine #{$fine->fine_number} has status '{$fine->status}'. Skipping.");
            return;
        }

        $this->postRecovery($fine);
    }

    // ─────────────────────────────────────────────────────────────
    // Core Recovery Logic
    // ─────────────────────────────────────────────────────────────

    protected function postRecovery(VehicleFine $fine): void
    {
        DB::transaction(function () use ($fine) {
            $fine->load('customer', 'vehicle');

            $amount   = (float) $fine->amount;
            $customer = $fine->customer;
            $vehicle  = $fine->vehicle;

            // Resolve accounts
            $arAccount  = $this->getAccount(self::ACCOUNT_AR);
            $recAccount = $this->getAccount(self::ACCOUNT_FINE_RECOVERY);

            // DR  Accounts Receivable   1,000   ← owed by customer
            // CR  Fine Recovery Income  1,000   ← income recognised
            //
            $journal = $this->accounting->createJournalEntry(
                [
                    'date'           => now()->toDateString(),
                    'description'    => sprintf(
                        'Fine Recovery — %s | Fine #%s | %s | Customer: %s',
                        $vehicle?->vehicle_number ?? 'N/A',
                        $fine->fine_number,
                        $fine->authority,
                        $customer->name
                    ),
                    'reference_type' => VehicleFine::class,
                    'reference_id'   => $fine->id,
                ],
                [
                    [
                        'account_id'  => $arAccount->id,
                        'debit'       => $amount,
                        'credit'      => 0,
                        'description' => "Fine recovery — AR: {$customer->name}",
                    ],
                    [
                        'account_id'  => $recAccount->id,
                        'debit'       => 0,
                        'credit'      => $amount,
                        'description' => "Fine Recovery Income — {$fine->authority} | {$fine->fine_number}",
                    ],
                ]
            );

            // Link journal back to fine + mark recovered
            $fine->update([
                'journal_entry_id' => $journal->id,
                'status'           => 'recovered',
            ]);

            // Update customer AR balance
            $customer->increment('current_balance', $amount);

            Log::info("FineRecovery: ✅ Recovered AED {$amount} from {$customer->name} | Journal #{$journal->id} | Fine #{$fine->fine_number}");

            $this->logEvent(
                Company::find($fine->company_id),
                EventLoggerService::FINE_PAID, // Reusing paid or should I add RECOVERED?
                $fine,
                "Fine #{$fine->fine_number} recovered from " . $customer->company_name,
                ['recovered_amount' => $amount, 'customer_id' => $customer->id],
                EventLoggerService::SEVERITY_INFO
            );
        });
    }

    // ─────────────────────────────────────────────────────────────
    // Batch Recovery — artisan command / cron safety net
    // ─────────────────────────────────────────────────────────────

    /**
     * Recover all unrecovered customer-responsible fines for a company.
     * Covers both paid and pending fines (catch-up batch).
     */
    public function recoverAllPending(Company $company): int
    {
        $recovered = 0;

        VehicleFine::where('customer_responsible', true)
            ->whereNull('journal_entry_id')
            ->whereNotNull('customer_id')
            ->whereIn('status', ['paid', 'pending'])
            ->cursor()
            ->each(function (VehicleFine $fine) use (&$recovered) {
                try {
                    $this->processCustomerRecovery($fine);
                    $recovered++;
                } catch (\Throwable $e) {
                    Log::error("FineRecovery: Failed for fine #{$fine->id} — {$e->getMessage()}");
                }
            });

        return $recovered;
    }

    // ─────────────────────────────────────────────────────────────
    // Dashboard Metrics
    // ─────────────────────────────────────────────────────────────

    /**
     * Fine recovery KPIs for the executive dashboard.
     */
    public function getRecoveryMetrics(): array
    {
        $year  = now()->year;
        $month = now()->month;

        // All customer-responsible fines
        $allFines = VehicleFine::where('customer_responsible', true)
            ->whereNotNull('customer_id')
            ->get(['id', 'amount', 'status', 'journal_entry_id']);

        $total       = $allFines->sum('amount');
        $recovered   = $allFines->where('status', 'recovered')->sum('amount');
        $outstanding = $allFines->whereNull('journal_entry_id')->sum('amount');

        // Recovered this month
        $recoveredThisMonth = VehicleFine::where('status', 'recovered')
            ->whereYear('updated_at', $year)
            ->whereMonth('updated_at', $month)
            ->sum('amount');

        $recoveryRate = $total > 0 ? round(($recovered / $total) * 100, 1) : 0;

        return [
            'total_recoverable'       => $total,
            'recovered_total'         => $recovered,
            'recovered_this_month'    => $recoveredThisMonth,
            'outstanding_recoveries'  => $outstanding,
            'recovery_rate_pct'       => $recoveryRate,
            'rag'                     => $recoveryRate >= 80 ? 'green' : ($recoveryRate >= 50 ? 'amber' : 'red'),
        ];
    }

    // ─────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────

    protected function getAccount(string $code): Account
    {
        $account = Account::where('account_code', $code)
            ->first();

        if (!$account) {
            throw new \RuntimeException("Account '{$code}' not found. Check COA seeder.");
        }

        return $account;
    }
}
