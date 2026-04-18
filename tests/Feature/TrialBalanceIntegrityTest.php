<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Branch;
use App\Models\Company;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\SubscriptionPlan;
use App\Services\FinancialReportService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * TrialBalanceIntegrityTest
 *
 * Tests:
 *  1. Total debits = total credits in a balanced system
 *  2. Opening balance is carried forward correctly into period
 *  3. Mismatch is detected when present
 *  4. Branch filter isolates data correctly
 */
class TrialBalanceIntegrityTest extends TestCase
{
    use RefreshDatabase;

    protected Company $company;
    protected FinancialReportService $service;
    protected Account $cashAccount;
    protected Account $incomeAccount;

    protected function setUp(): void
    {
        parent::setUp();

        SubscriptionPlan::updateOrCreate(
            ['slug' => 'starter'],
            ['name' => 'Starter Plan', 'price_monthly' => 0, 'price_yearly' => 0, 'trial_days' => 30, 'is_active' => true, 'features' => []]
        );

        $this->company = Company::factory()->create();
        $this->service = new FinancialReportService();

        // Resolve two leaf accounts from the auto-seeded COA
        $this->cashAccount   = Account::where('company_id', $this->company->id)->where('account_code', '1011')->first();
        $this->incomeAccount = Account::where('company_id', $this->company->id)->where('account_code', '4010')->first();

        if (!$this->cashAccount || !$this->incomeAccount) {
            $this->markTestSkipped('Required COA accounts (1011, 4010) not found. Ensure CompanyObserver seeds them.');
        }
    }

    /**
     * Helper: post a balanced journal entry to the company.
     */
    private function postEntry(string $date, float $amount, ?int $branchId = null): void
    {
        $je = JournalEntry::create([
            'company_id'   => $this->company->id,
            'branch_id'    => $branchId,
            'date'         => $date,
            'description'  => "Test entry {$amount}",
            'is_posted'    => true,
            'posted_at'    => now(),
        ]);

        JournalEntryLine::create(['journal_entry_id' => $je->id, 'account_id' => $this->cashAccount->id,   'debit' => $amount, 'credit' => 0]);
        JournalEntryLine::create(['journal_entry_id' => $je->id, 'account_id' => $this->incomeAccount->id, 'debit' => 0, 'credit' => $amount]);
    }

    /** @test */
    public function trial_balance_totals_debits_equal_credits(): void
    {
        $this->postEntry('2026-02-01', 1000);
        $this->postEntry('2026-02-10', 2500);

        $result = $this->service->getTrialBalance(
            $this->company->id,
            Carbon::parse('2026-02-01'),
            Carbon::parse('2026-02-28')
        );

        $this->assertEquals(
            $result['total_debit'],
            $result['total_credit'],
            'Trial balance closing totals must be equal (DR = CR).'
        );
        $this->assertTrue($result['is_balanced'], 'is_balanced must be true for correctly posted entries.');
        $this->assertLessThan(0.01, $result['difference']);
    }

    /** @test */
    public function trial_balance_carries_opening_balance_from_prior_period(): void
    {
        // Post in January (before the report period)
        $this->postEntry('2026-01-15', 500);

        // Report for February only
        $result = $this->service->getTrialBalance(
            $this->company->id,
            Carbon::parse('2026-02-01'),
            Carbon::parse('2026-02-28')
        );

        // The cash account row should have opening_balance = 500 (debit)
        $cashRow = collect($result['rows'])->firstWhere('account_code', '1011');

        $this->assertNotNull($cashRow, 'Cash account should appear in trial balance.');
        $this->assertEquals(
            500,
            $cashRow['opening_balance'],
            'Opening balance should reflect January transaction.'
        );
    }

    /** @test */
    public function trial_balance_detects_mismatch_when_system_is_unbalanced(): void
    {
        // Inject an unbalanced raw line (bypassing the AccountingService guard)
        $je = JournalEntry::create([
            'company_id'  => $this->company->id,
            'date'        => '2026-02-01',
            'description' => 'Corrupt entry',
            'is_posted'   => true,
        ]);

        JournalEntryLine::create(['journal_entry_id' => $je->id, 'account_id' => $this->cashAccount->id, 'debit' => 1000, 'credit' => 0]);
        // Deliberately post only 999 on the credit side
        JournalEntryLine::create(['journal_entry_id' => $je->id, 'account_id' => $this->incomeAccount->id, 'debit' => 0, 'credit' => 999]);

        $result = $this->service->getTrialBalance(
            $this->company->id,
            Carbon::parse('2026-02-01'),
            Carbon::parse('2026-02-28')
        );

        $this->assertFalse($result['is_balanced'], 'Trial balance must flag mismatch.');
        $this->assertGreaterThanOrEqual(0.01, $result['difference']);
    }

    /** @test */
    public function trial_balance_branch_filter_isolates_data(): void
    {
        $branch = Branch::create([
            'company_id' => $this->company->id,
            'name'       => 'Branch A',
            'is_active'  => true,
        ]);

        // 1000 with branch, 500 without branch
        $this->postEntry('2026-02-01', 1000, $branch->id);
        $this->postEntry('2026-02-01', 500,  null);

        $resultAll    = $this->service->getTrialBalance($this->company->id, Carbon::parse('2026-02-01'), Carbon::parse('2026-02-28'));
        $resultBranch = $this->service->getTrialBalance($this->company->id, Carbon::parse('2026-02-01'), Carbon::parse('2026-02-28'), $branch->id);

        // Branch-isolated should have exactly the 1000 entry
        $cashRowAll    = collect($resultAll['rows'])->firstWhere('account_code', '1011');
        $cashRowBranch = collect($resultBranch['rows'])->firstWhere('account_code', '1011');

        $this->assertEquals(1500, $cashRowAll['period_debit'],    'All-branch should sum both entries.');
        $this->assertEquals(1000, $cashRowBranch['period_debit'], 'Branch filter should isolate 1000 entry only.');
    }
}
