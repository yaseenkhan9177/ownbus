<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Company;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorBill;
use App\Services\Accounting\VendorBillService;
use App\Services\VendorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VendorModuleTest extends TestCase
{
    use RefreshDatabase;

    protected $company;
    protected $admin;
    protected $vendorService;
    protected $billService;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed subscription plans (REQUIRED by CompanyObserver)
        SubscriptionPlan::updateOrCreate(
            ['slug' => 'starter'],
            [
                'name' => 'Starter Plan',
                'price_monthly' => 0,
                'price_yearly' => 0,
                'trial_days' => 30,
                'is_active' => true,
                'features' => []
            ]
        );

        $this->company = Company::factory()->create();
        $this->admin = User::factory()->create([
            'company_id' => $this->company->id,
            'role' => 'company_admin'
        ]);

        $this->vendorService = app(VendorService::class);
        $this->billService = app(VendorBillService::class);

        // Ensure equity account exists
        Account::firstOrCreate(
            ['company_id' => $this->company->id, 'account_code' => '3999'],
            ['account_name' => 'Opening Balance Equity', 'account_type' => 'equity', 'is_active' => true]
        );
        Account::firstOrCreate(
            ['company_id' => $this->company->id, 'account_code' => '2011'],
            ['account_name' => 'Accounts Payable', 'account_type' => 'liability', 'is_active' => true]
        );
    }

    /**
     * Test Vendor Creation with Opening Balance.
     */
    public function test_vendor_creation_with_opening_balance_triggers_journal_entry()
    {
        $this->actingAs($this->admin);

        $vendorData = [
            'company_id' => $this->company->id,
            'vendor_code' => 'V-001',
            'name' => 'Globex Corp',
            'opening_balance' => 1000,
            'balance_direction' => 'payable',
            'status' => 'active',
            'created_by' => $this->admin->id,
        ];

        $vendor = $this->vendorService->createVendor($vendorData);

        $this->assertDatabaseHas('vendors', ['name' => 'Globex Corp']);

        // Check Journal Entry
        $je = JournalEntry::where('reference_id', $vendor->id)
            ->where('reference_type', Vendor::class)
            ->first();

        $this->assertNotNull($je);
        $this->assertEquals(1000, $je->lines()->where('account_id', Account::where('account_code', '2011')->where('company_id', $this->company->id)->first()->id)->first()->credit);
        $this->assertEquals(1000, $je->lines()->where('account_id', Account::where('account_code', '3999')->where('company_id', $this->company->id)->first()->id)->first()->debit);
    }

    /**
     * Test Outstanding Balance Calculation.
     */
    public function test_vendor_outstanding_balance_calculation()
    {
        $vendor = Vendor::create([
            'company_id' => $this->company->id,
            'vendor_code' => 'V-TEST',
            'name' => 'Test Vendor',
            'opening_balance' => 0,
            'status' => 'active'
        ]);

        // Mock 2 bills approved (1000 each)
        // Manual JE creation to simulate bill approval (DR Expense, CR AP)
        $apAccount = Account::where('company_id', $this->company->id)->where('account_code', '2011')->first();
        $expense = Account::factory()->create(['company_id' => $this->company->id, 'account_type' => 'expense']);

        // Bill 1
        $bill1 = VendorBill::create([
            'company_id' => $this->company->id,
            'vendor_id' => $vendor->id,
            'bill_number' => 'B-1',
            'bill_date' => now(),
            'total_amount' => 1000,
            'status' => 'approved'
        ]);

        $je1 = JournalEntry::create([
            'company_id' => $this->company->id,
            'date' => now(),
            'description' => 'Bill Approved',
            'reference_id' => $bill1->id,
            'reference_type' => VendorBill::class
        ]);
        $je1->lines()->create(['account_id' => $expense->id, 'debit' => 1000, 'credit' => 0]);
        $je1->lines()->create(['account_id' => $apAccount->id, 'debit' => 0, 'credit' => 1000]);

        // Bill 2
        $bill2 = VendorBill::create([
            'company_id' => $this->company->id,
            'vendor_id' => $vendor->id,
            'bill_number' => 'B-2',
            'bill_date' => now(),
            'total_amount' => 500,
            'status' => 'approved'
        ]);

        $je2 = JournalEntry::create([
            'company_id' => $this->company->id,
            'date' => now(),
            'description' => 'Bill Approved',
            'reference_id' => $bill2->id,
            'reference_type' => VendorBill::class
        ]);
        $je2->lines()->create(['account_id' => $expense->id, 'debit' => 500, 'credit' => 0]);
        $je2->lines()->create(['account_id' => $apAccount->id, 'debit' => 0, 'credit' => 500]);

        // Balance should be 1500
        $this->assertEquals(1500, $vendor->calculateOutstandingBalance());

        // Record partial payment 300 (DR AP, CR Cash)
        $cash = Account::where('company_id', $this->company->id)->where('account_code', '1011')->first();
        $je3 = JournalEntry::create([
            'company_id' => $this->company->id,
            'date' => now(),
            'description' => 'Payment recorded',
            'reference_id' => $bill1->id,
            'reference_type' => VendorBill::class
        ]);
        $je3->lines()->create(['account_id' => $apAccount->id, 'debit' => 300, 'credit' => 0]);
        $je3->lines()->create(['account_id' => $cash->id, 'debit' => 0, 'credit' => 300]);

        // Balance should be 1500 - 300 = 1200
        $this->assertEquals(1200, $vendor->calculateOutstandingBalance());
    }

    /**
     * Test Deletion Guard.
     */
    public function test_cannot_delete_vendor_with_bills()
    {
        $vendor = Vendor::create([
            'company_id' => $this->company->id,
            'vendor_code' => 'V-DEL',
            'name' => 'Delete Me',
            'status' => 'active'
        ]);

        $this->assertTrue($this->vendorService->canDelete($vendor));

        // Create a bill
        VendorBill::create([
            'company_id' => $this->company->id,
            'vendor_id' => $vendor->id,
            'bill_number' => 'B-DEL',
            'bill_date' => now(),
            'total_amount' => 100,
            'status' => 'draft'
        ]);

        $this->assertFalse($this->vendorService->canDelete($vendor));
    }
}
