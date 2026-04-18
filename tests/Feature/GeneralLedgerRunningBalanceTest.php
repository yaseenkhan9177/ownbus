<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Company;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\SubscriptionPlan;
use App\Services\FinancialReportService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * GeneralLedgerRunningBalanceTest
 *
 * Tests:
 *  1. Running balance increments correctly: debit → credit → debit sequence
 *  2. Entries are sorted by date (not insertion order)
 *  3. Opening balance is included in running balance computation
 *  4. Credit-normal account has correct running balance direction
 */
class GeneralLedgerRunningBalanceTest extends TestCase
{
    use RefreshDatabase;

    protected Company $company;
    protected FinancialReportService $service;
    protected Account $cashAccount;   // debit-normal (asset)
    protected Account $incomeAccount; // credit-normal (income)

    protected function setUp(): void
    {
        parent::setUp();

        SubscriptionPlan::updateOrCreate(
            ['slug' => 'starter'],
            ['name' => 'Starter Plan', 'price_monthly' => 0, 'price_yearly' => 0, 'trial_days' => 30, 'is_active' => true, 'features' => []]
        );

        $this->company = Company::factory()->create();
        $this->service = new FinancialReportService();

        $this->cashAccount   = Account::where('company_id', $this->company->id)->where('account_code', '1011')->first();
        $this->incomeAccount = Account::where('company_id', $this->company->id)->where('account_code', '4010')->first();

        if (!$this->cashAccount || !$this->incomeAccount) {
            $this->markTestSkipped('Required COA accounts (1011, 4010) not found.');
        }
    }

    /**
     * Post a single line entry to cash account (debit) against income (credit).
     */
    private function postToAccount(string $date, float $debit = 0, float $credit = 0): void
    {
        $je = JournalEntry::create([
            'company_id'  => $this->company->id,
            'date'        => $date,
            'description' => "GL test {$date} DR:{$debit} CR:{$credit}",
            'is_posted'   => true,
        ]);

        // Cash account line
        JournalEntryLine::create(['journal_entry_id' => $je->id, 'account_id' => $this->cashAccount->id, 'debit' => $debit, 'credit' => $credit]);

        // Balancing income line
        JournalEntryLine::create(['journal_entry_id' => $je->id, 'account_id' => $this->incomeAccount->id, 'debit' => $credit, 'credit' => $debit]);
    }

    /** @test */
    public function running_balance_increments_debit_then_credit_then_debit_sequence(): void
    {
        // Sequence: +1000, -400, +250 (all on cash account)
        $this->postToAccount('2026-02-01', debit: 1000);  // balance after: +1000
        $this->postToAccount('2026-02-10', credit: 400);  // balance after:  +600
        $this->postToAccount('2026-02-20', debit: 250);   // balance after:  +850

        $result = $this->service->getGeneralLedger(
            $this->cashAccount,
            Carbon::parse('2026-02-01'),
            Carbon::parse('2026-02-28')
        );

        $this->assertCount(3, $result['entries'], '3 journal lines should appear.');

        $balances = array_column($result['entries'], 'running_balance');

        $this->assertEqualsWithDelta(1000,  $balances[0], 0.01, 'After first debit: 1000');
        $this->assertEqualsWithDelta(600,   $balances[1], 0.01, 'After credit: 1000 - 400 = 600');
        $this->assertEqualsWithDelta(850,   $balances[2], 0.01, 'After last debit: 600 + 250 = 850');
    }

    /** @test */
    public function entries_are_sorted_by_date_not_insertion_order(): void
    {
        // Insert out of order
        $this->postToAccount('2026-02-20', debit: 300);
        $this->postToAccount('2026-02-05', debit: 500);
        $this->postToAccount('2026-02-12', debit: 200);

        $result  = $this->service->getGeneralLedger(
            $this->cashAccount,
            Carbon::parse('2026-02-01'),
            Carbon::parse('2026-02-28')
        );

        $entries = $result['entries'];
        $this->assertEquals('2026-02-05', Carbon::parse($entries[0]['date'])->format('Y-m-d'), 'First entry should be Feb 5.');
        $this->assertEquals('2026-02-12', Carbon::parse($entries[1]['date'])->format('Y-m-d'), 'Second entry should be Feb 12.');
        $this->assertEquals('2026-02-20', Carbon::parse($entries[2]['date'])->format('Y-m-d'), 'Third entry should be Feb 20.');
    }

    /** @test */
    public function opening_balance_from_prior_period_is_included_in_running_balance(): void
    {
        // Post 1000 in January (before the period)
        $this->postToAccount('2026-01-15', debit: 1000);

        // Post 500 in February (within period)
        $this->postToAccount('2026-02-10', debit: 500);

        $result = $this->service->getGeneralLedger(
            $this->cashAccount,
            Carbon::parse('2026-02-01'),
            Carbon::parse('2026-02-28')
        );

        $this->assertEquals(1000.0, $result['opening_balance'], 'Opening balance from January should be 1000.');
        $this->assertCount(1, $result['entries'], 'Only February entry should appear in period.');

        // Running balance after February entry should be 1000 + 500 = 1500
        $this->assertEqualsWithDelta(
            1500,
            $result['entries'][0]['running_balance'],
            0.01,
            'Running balance should start from opening balance of 1000, then add 500.'
        );
    }

    /** @test */
    public function credit_normal_account_running_balance_direction_is_correct(): void
    {
        // Revenue account: credit increases balance, debit decreases it
        $je1 = JournalEntry::create([
            'company_id'  => $this->company->id,
            'date'        => '2026-02-01',
            'description' => 'Revenue posted',
            'is_posted'   => true,
        ]);
        JournalEntryLine::create(['journal_entry_id' => $je1->id, 'account_id' => $this->cashAccount->id,   'debit' => 2000, 'credit' => 0]);
        JournalEntryLine::create(['journal_entry_id' => $je1->id, 'account_id' => $this->incomeAccount->id, 'debit' => 0, 'credit' => 2000]);

        $result = $this->service->getGeneralLedger(
            $this->incomeAccount,
            Carbon::parse('2026-02-01'),
            Carbon::parse('2026-02-28')
        );

        // Income is credit-normal: credit of 2000 → running balance = +2000
        $this->assertEqualsWithDelta(
            2000,
            $result['entries'][0]['running_balance'],
            0.01,
            'Credit-normal account: 2000 credit should produce positive running balance of 2000.'
        );
    }
}
