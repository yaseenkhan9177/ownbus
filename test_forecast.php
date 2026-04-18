<?php

use App\Models\Company;
use App\Models\Account;
use App\Models\FinancialTransaction;
use App\Models\JournalEntry;
use App\Models\FinancialForecast;
use App\Models\Currency;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// 1. Setup Data
Schema::disableForeignKeyConstraints();
FinancialForecast::truncate();
JournalEntry::truncate();
FinancialTransaction::truncate();
Account::truncate();
// Company::truncate(); // Keep company if possible, or create new.
Schema::enableForeignKeyConstraints();

$company = Company::first();
if (!$company) {
    $company = Company::create(['name' => 'TestCo', 'status' => 'active', 'currency_code' => 'USD']);
}
// Currency check removed

// Create Accounts
$revAccount = Account::create([
    'company_id' => $company->id,
    'account_code' => '4000',
    'account_name' => 'Sales Revenue',
    'account_type' => 'income',
    'is_active' => true
]);

$expAccount = Account::create([
    'company_id' => $company->id,
    'account_code' => '5000',
    'account_name' => 'Office Expense',
    'account_type' => 'expense',
    'is_active' => true
]);

// Seed 12 Months Data
// Revenue Trend: 10,000 + (1,000 * MonthIndex)
// Expense Trend: 5,000 (Flat)

$startDate = Carbon::now()->subMonths(12)->startOfMonth();

echo "Seeding Data...\n";
for ($i = 0; $i < 12; $i++) {
    $date = $startDate->copy()->addMonths($i);
    $revenueAmount = 10000 + ($i * 1000);
    $expenseAmount = 5000;

    // Revenue Transaction
    $revTx = FinancialTransaction::create([
        'company_id' => $company->id,
        'transaction_date' => $date,
        'description' => "Sales for " . $date->format('M Y'),
        'reference_type' => 'Manual',
        'reference_id' => 0
    ]);

    // Journal: Debit Cash/AR (skipped), Credit Revenue
    JournalEntry::create([
        'transaction_id' => $revTx->id,
        'account_id' => $revAccount->id,
        'debit' => 0,
        'credit' => $revenueAmount
    ]);

    // Expense Transaction
    $expTx = FinancialTransaction::create([
        'company_id' => $company->id,
        'transaction_date' => $date,
        'description' => "Rent for " . $date->format('M Y'),
        'reference_type' => 'Manual',
        'reference_id' => 0
    ]);

    // Journal: Debit Expense, Credit Cash (skipped)
    JournalEntry::create([
        'transaction_id' => $expTx->id,
        'account_id' => $expAccount->id,
        'debit' => $expenseAmount,
        'credit' => 0
    ]);
}

// 2. Run Command
echo "Running Forecast...\n";
\Illuminate\Support\Facades\Artisan::call('finance:forecast', ['--company_id' => $company->id]);
echo \Illuminate\Support\Facades\Artisan::output();

// 3. Verify
$forecasts = FinancialForecast::where('company_id', $company->id)->orderBy('forecast_date')->get();

echo "Forecast Results:\n";
foreach ($forecasts as $f) {
    echo "{$f->forecast_date->format('Y-m-d')} - {$f->metric_type}: {$f->predicted_value} (Conf: {$f->confidence_score})\n";
}

// Assertions
$firstRevForecast = $forecasts->where('metric_type', 'revenue')->first();
// Month 12 (Index 12) prediction should be ~ 10000 + 12*1000 = 22000?
// Wait, regression on 0..11.
// y = 1000x + 10000.
// Next month (index 12): 1000(12) + 10000 = 22000.
// Let's check logic.

if ($firstRevForecast && abs($firstRevForecast->predicted_value - 22000) < 500) {
    echo "PASS: Revenue forecast is accurate.\n";
} else {
    echo "FAIL: Revenue forecast mismatch. Expected ~22000. Got " . ($firstRevForecast->predicted_value ?? 'NULL') . "\n";
}
