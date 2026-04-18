<?php

namespace Tests\Feature;

use App\Models\Rental;
use App\Models\User;
use App\Models\Company;
use App\Models\Vehicle;
use App\Models\Branch;
use App\Models\Account;
use App\Models\FinancialTransaction;
use App\Models\JournalEntry;
use App\Models\BusProfitabilityMetric;
use App\Services\DashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class DashboardDataTest extends TestCase
{
    use RefreshDatabase;

    protected $service;
    protected $company;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(DashboardService::class);
        $this->company = Company::factory()->create();
    }

    #[Test]
    public function revenue_today_is_correctly_aggregated()
    {
        // 2 rentals today
        Rental::factory()->create([
            'company_id' => $this->company->id,
            'grand_total' => 500,
            'created_at' => now(),
        ]);
        Rental::factory()->create([
            'company_id' => $this->company->id,
            'grand_total' => 250,
            'created_at' => now(),
        ]);
        // 1 rental yesterday
        Rental::factory()->create([
            'company_id' => $this->company->id,
            'grand_total' => 1000,
            'created_at' => now()->subDay(),
        ]);

        $stats = $this->service->getCompanyStats($this->company);
        $this->assertEquals(750.0, $stats['revenue_today']);
    }

    #[Test]
    public function fleet_utilization_is_calculated_correctly()
    {
        // 10 buses total
        Vehicle::factory()->count(10)->create(['company_id' => $this->company->id]);

        // 4 active rentals
        Rental::factory()->count(4)->create([
            'company_id' => $this->company->id,
            'status' => 'active',
            'bus_id' => Vehicle::factory() // unique buses per factory
        ]);

        $stats = $this->service->getCompanyStats($this->company);
        $this->assertEquals(40.0, $stats['fleet_utilization']);
    }

    #[Test]
    public function receivables_are_summed_correctly()
    {
        $arAccount = Account::firstOrCreate([
            'company_id' => $this->company->id,
            'account_code' => '1013',
        ], [
            'account_name' => 'Accounts Receivable',
            'account_type' => 'asset',
            'is_system' => true,
        ]);

        $tx = FinancialTransaction::factory()->create(['company_id' => $this->company->id]);

        // Dr 1000, Cr 200 => 800 balance
        \App\Models\JournalEntry::create([
            'transaction_id' => $tx->id,
            'account_id' => $arAccount->id,
            'debit' => 1000,
            'credit' => 0
        ]);
        \App\Models\JournalEntry::create([
            'transaction_id' => $tx->id,
            'account_id' => $arAccount->id,
            'debit' => 0,
            'credit' => 200
        ]);

        $stats = $this->service->getCompanyStats($this->company);
        $this->assertEquals(800.0, $stats['receivables']);
    }

    #[Test]
    public function data_is_isolated_by_company()
    {
        $otherCompany = Company::factory()->create();

        Rental::factory()->create([
            'company_id' => $this->company->id,
            'grand_total' => 500,
            'created_at' => now(),
        ]);
        Rental::factory()->create([
            'company_id' => $otherCompany->id,
            'grand_total' => 9999, // Should be ignored
            'created_at' => now(),
        ]);

        $stats = $this->service->getCompanyStats($this->company);
        $this->assertEquals(500.0, $stats['revenue_today']);
    }

    #[Test]
    public function performance_ranking_returns_top_and_worst()
    {
        $buses = Vehicle::factory()->count(10)->create(['company_id' => $this->company->id]);

        foreach ($buses as $bus) {
            BusProfitabilityMetric::factory()->create([
                'vehicle_id' => $bus->id,
                'net_profit' => rand(-500, 2000),
                'month_year' => now()->format('Y-m')
            ]);
        }

        $perf = $this->service->getFleetPerformance($this->company);

        $this->assertCount(5, $perf['top_performing']);
        $this->assertCount(5, $perf['worst_performing']);
        $this->assertGreaterThanOrEqual($perf['top_performing']->last()->net_profit, $perf['top_performing']->first()->net_profit);
    }
}
