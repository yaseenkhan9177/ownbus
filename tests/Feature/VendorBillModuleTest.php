<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Company;
use App\Models\JournalEntry;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorBill;
use App\Models\VendorBillItem;
use App\Services\Accounting\VendorBillService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VendorBillModuleTest extends TestCase
{
    use RefreshDatabase;

    protected $company;
    protected $admin;
    protected $billService;

    protected function setUp(): void
    {
        parent::setUp();

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

        $this->billService = app(VendorBillService::class);

        // Ensure system accounts
        Account::firstOrCreate(['company_id' => $this->company->id, 'account_code' => '2011'], ['account_name' => 'AP', 'account_type' => 'liability', 'is_active' => true]);
        Account::firstOrCreate(['company_id' => $this->company->id, 'account_code' => '1011'], ['account_name' => 'Cash', 'account_type' => 'asset', 'is_active' => true]);
    }

    /**
     * Test Complete Bill Flow: Creation -> Approval -> Payment.
     */
    public function test_vendor_bill_lifecycle()
    {
        $this->actingAs($this->admin);

        $vendor = Vendor::create([
            'company_id' => $this->company->id,
            'vendor_code' => 'V-FLOW',
            'name' => 'Lifecycle Vendor',
            'status' => 'active'
        ]);

        $expenseAcc = Account::factory()->create([
            'company_id' => $this->company->id,
            'account_type' => 'expense',
            'account_code' => '5100'
        ]);

        // 1. Create Draft
        $billData = [
            'company_id' => $this->company->id,
            'vendor_id' => $vendor->id,
            'bill_number' => 'BILL-XYZ',
            'bill_date' => now()->toDateString(),
            'created_by' => $this->admin->id,
        ];
        $items = [
            [
                'expense_account_id' => $expenseAcc->id,
                'description' => 'Tire Replacement',
                'quantity' => 4,
                'unit_cost' => 250,
            ]
        ];

        $bill = $this->billService->createDraft($billData, $items);

        $this->assertEquals(1000, $bill->total_amount);
        $this->assertEquals('draft', $bill->status);
        $this->assertCount(1, $bill->items);

        // 2. Approve Bill
        $this->billService->approveBill($bill);
        $bill->refresh();

        $this->assertEquals('approved', $bill->status);

        // Verfiy Journal Entry (DR Expense 1000, CR AP 1000)
        $je = JournalEntry::where('reference_id', $bill->id)->where('reference_type', VendorBill::class)->first();
        $this->assertNotNull($je);
        $this->assertEquals(1000, $je->lines()->where('account_id', $expenseAcc->id)->first()->debit);
        $this->assertEquals(1000, $je->lines()->where('account_id', Account::where('account_code', '2011')->where('company_id', $this->company->id)->first()->id)->first()->credit);

        // 3. Record Payment (Partial)
        $this->billService->recordPayment($bill, 400, 'cash');
        $bill->refresh();

        $this->assertEquals('partially_paid', $bill->status);
        $this->assertEquals(400, $bill->paidAmount());
        $this->assertEquals(600, $bill->remainingAmount());

        // 4. Record Final Payment
        $this->billService->recordPayment($bill, 600, 'cash');
        $bill->refresh();

        $this->assertEquals('paid', $bill->status);
        $this->assertEquals(1000, $bill->paidAmount());
        $this->assertEquals(0, $bill->remainingAmount());
    }

    /**
     * Test Cancellation Logic.
     */
    public function test_bill_cancellation_reverses_journal_entries()
    {
        $this->actingAs($this->admin);

        $vendor = Vendor::create(['company_id' => $this->company->id, 'vendor_code' => 'V-CANCEL', 'name' => 'Cancel Vendor', 'status' => 'active']);
        $expenseAcc = Account::factory()->create(['company_id' => $this->company->id, 'account_type' => 'expense', 'account_code' => '5200']);

        $bill = $this->billService->createDraft([
            'company_id' => $this->company->id,
            'vendor_id' => $vendor->id,
            'bill_number' => 'B-VOID',
            'bill_date' => now()->toDateString(),
        ], [['expense_account_id' => $expenseAcc->id, 'description' => 'Test', 'quantity' => 1, 'unit_cost' => 500]]);

        $this->billService->approveBill($bill);

        // Should have 1 entry
        $this->assertCount(1, JournalEntry::where('reference_id', $bill->id)->where('reference_type', VendorBill::class)->get());

        // Cancel
        $this->billService->cancel($bill, "Mistake");
        $bill->refresh();

        $this->assertEquals('cancelled', $bill->status);

        // Should have 2 entries (Original + Reversal)
        $jes = JournalEntry::where('reference_id', $bill->id)->where('reference_type', VendorBill::class)->get();
        $this->assertCount(2, $jes);

        $reversal = $jes->sortByDesc('id')->first();
        $this->assertStringContainsString('REVERSAL', $reversal->description);
    }
}
