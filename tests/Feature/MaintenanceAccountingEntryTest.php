<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Vehicle;
use App\Models\MaintenanceRecord;
use App\Models\SubscriptionPlan;
use App\Models\Account;
use App\Services\Fleet\MaintenanceService;
use Database\Seeders\ChartOfAccountsSeeder;

class MaintenanceAccountingEntryTest extends TestCase
{
    use RefreshDatabase;

    protected $company;
    protected $user;
    protected $vehicle;

    protected function setUp(): void
    {
        parent::setUp();

        SubscriptionPlan::firstOrCreate(
            ['slug' => 'starter'],
            ['name' => 'Starter Plan', 'price_monthly' => 0, 'price_yearly' => 0, 'features' => [], 'is_active' => true]
        );

        $this->company = Company::factory()->create();
        $this->user = User::factory()->create([
            'company_id' => $this->company->id,
            'role' => 'company_admin',
        ]);
        $this->vehicle = Vehicle::factory()->create([
            'company_id' => $this->company->id,
            'status' => 'available',
        ]);

        // Seed COA for tests so accounting can generate entries
        $seeder = new ChartOfAccountsSeeder();
        $seeder->run($this->company);
    }

    public function test_journal_entries_generated_upon_completion_with_cost()
    {
        // Arrange
        $record = MaintenanceRecord::create([
            'company_id' => $this->company->id,
            'vehicle_id' => $this->vehicle->id,
            'maintenance_number' => 'MNT-000003',
            'type' => 'preventive',
            'status' => 'in_progress',
            'total_cost' => 1500.50,
            'created_by' => $this->user->id,
        ]);

        // Act
        $service = app(MaintenanceService::class);
        $service->completeRecord($record, [
            'odometer_reading' => 55000,
            'completed_date' => now(),
        ]);

        // Assert
        $this->assertDatabaseHas('financial_transactions', [
            'company_id' => $this->company->id,
            'reference_type' => MaintenanceRecord::class,
            'reference_id' => $record->id,
        ]);

        $transaction = \App\Models\FinancialTransaction::where('reference_id', $record->id)->first();

        $expenseAccount = Account::where('company_id', $this->company->id)->where('account_code', '5012')->first();
        $cashAccount = Account::where('company_id', $this->company->id)->where('account_code', '1011')->first();

        $this->assertDatabaseHas('journal_entries', [
            'transaction_id' => $transaction->id,
            'account_id' => $expenseAccount->id,
            'debit' => 1500.50,
            'credit' => 0,
        ]);

        $this->assertDatabaseHas('journal_entries', [
            'transaction_id' => $transaction->id,
            'account_id' => $cashAccount->id,
            'debit' => 0,
            'credit' => 1500.50,
        ]);
    }
}
