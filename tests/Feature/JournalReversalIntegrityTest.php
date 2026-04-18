<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Company;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Services\AccountingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JournalReversalIntegrityTest extends TestCase
{
    use RefreshDatabase;

    protected AccountingService $accounting;
    protected Company $company;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\SubscriptionPlanSeeder::class);
        $this->accounting = app(AccountingService::class);
        $this->company = Company::factory()->create();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_reverses_entries_and_links_them_correctly()
    {
        // 1. Get system accounts (already seeded by CompanyObserver)
        $acc1 = Account::where('company_id', $this->company->id)->where('account_code', '1011')->first();
        $acc2 = Account::where('company_id', $this->company->id)->where('account_code', '4010')->first();

        // 2. Create a posted journal entry
        $journal = $this->accounting->createJournalEntry([
            'company_id' => $this->company->id,
            'date' => now()->toDateString(),
            'description' => 'Original Sale',
            'is_posted' => true,
        ], [
            ['account_id' => $acc1->id, 'debit' => 100, 'credit' => 0],
            ['account_id' => $acc2->id, 'debit' => 0, 'credit' => 100],
        ]);

        // 3. Reverse it
        $reversal = $this->accounting->reverseEntry($journal, 'Correction');

        // 4. Verify linking
        $this->assertEquals($reversal->id, $journal->fresh()->reversed_by);
        $this->assertEquals($journal->id, $reversal->reversal_of);

        // 5. Verify swaps
        $line1 = $reversal->lines()->where('account_id', $acc1->id)->first();
        $line2 = $reversal->lines()->where('account_id', $acc2->id)->first();

        $this->assertEquals(0, (float)$line1->debit);
        $this->assertEquals(100, (float)$line1->credit);
        $this->assertEquals(100, (float)$line2->debit);
        $this->assertEquals(0, (float)$line2->credit);
    }
}
