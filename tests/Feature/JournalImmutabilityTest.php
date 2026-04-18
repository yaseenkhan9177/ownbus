<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\JournalEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Exception;

class JournalImmutabilityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\SubscriptionPlanSeeder::class);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_prevents_updating_a_posted_journal_entry()
    {
        $company = Company::factory()->create();
        $journal = JournalEntry::create([
            'company_id' => $company->id,
            'description' => 'Original',
            'is_posted' => true,
            'date' => now()->toDateString(),
        ]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Cannot modify a posted journal entry');

        $journal->update(['description' => 'Tampered']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_prevents_deleting_a_posted_journal_entry()
    {
        $company = Company::factory()->create();
        $journal = JournalEntry::create([
            'company_id' => $company->id,
            'description' => 'Original',
            'is_posted' => true,
            'date' => now()->toDateString(),
        ]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Cannot delete a posted journal entry');

        $journal->delete();
    }
}
