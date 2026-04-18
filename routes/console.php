<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\CheckDocumentExpiries;
use App\Console\Commands\CheckTrafficFines;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command(CheckDocumentExpiries::class)->dailyAt('00:00');
Schedule::command(CheckTrafficFines::class)->dailyAt('04:30');

// Telematics Infrastructure
Schedule::job(new \App\Jobs\ArchiveTelematicsData)
    ->everyFiveMinutes()
    ->onOneServer();

Schedule::job(new \App\Jobs\PruneOldTelematicsData)
    ->daily()
    ->onOneServer();

// ── Phase 7: Enterprise Automation Layer ─────────────────────────────────────

// 7A: Contract Billing Engine — generates recurring invoices at 02:00
Schedule::command('contracts:generate-invoices')
    ->dailyAt('02:00')
    ->onOneServer()
    ->withoutOverlapping()
    ->runInBackground();

// 7N: Predictive Fleet Risk — calculates breakdown and accident probabilities
Schedule::command('fleet:predict-risk')
    ->dailyAt('02:30')
    ->onOneServer()
    ->runInBackground();

// 7D: GPS Offline Detection — marks stale pings as offline every 5 minutes
Schedule::job(new \App\Jobs\GPSOfflineDetectionJob)
    ->everyFiveMinutes()
    ->onOneServer();

// 7B: UAE Document Expiry Alerts — notifies at 07:00 UAE time
Schedule::command('fleet:expiry-alerts')
    ->dailyAt('07:00')
    ->onOneServer()
    ->withoutOverlapping();

// 7H: Driver Risk Scoring — calculates daily safety scores at 03:30
Schedule::command('drivers:calculate-risk')
    ->dailyAt('03:30')
    ->onOneServer()
    ->withoutOverlapping();

// 8B: Fine Recovery Safety Net — catches any fines that slipped through the queue
Schedule::command('fines:recover-pending')
    ->dailyAt('04:00')
    ->onOneServer()
    ->withoutOverlapping();

// 8C: GPS Location Pruning — keeps vehicle_locations table lean
Schedule::command('fleet:prune-locations')
    ->dailyAt('03:00')
    ->onOneServer()
    ->withoutOverlapping();

// 7I: Fleet Replacement AI — evaluates asset health weekly
Schedule::command('fleet:evaluate-replacement')
    ->weekly()
    ->onOneServer()
    ->withoutOverlapping();

// 7J: Multi-Branch Benchmark — ranks branches weekly on Mondays
Schedule::command('branches:benchmark')
    ->weeklyOn(1, '04:00')
    ->onOneServer()
    ->withoutOverlapping();

// ─────────────────────────────────────────────────────────────────────────────

Artisan::command('pricing:test', function () {
    $company = \App\Models\Company::first();
    $vehicle = \App\Models\Vehicle::first();

    if (!$company) {
        $company = \App\Models\Company::create(['name' => 'Demo Transport', 'status' => 'active', 'domain' => 'demo', 'subdomain' => 'demo']);
    }

    if (!$vehicle) {
        $vehicle = \App\Models\Vehicle::create([
            'company_id' => $company->id,
            'name' => 'Test Bus 001',
            'vehicle_number' => 'V-TEST-001',
            'make' => 'TestMake',
            'model' => 'TestModel',
            'daily_rate' => 100,
            'type' => 'bus',
            'year' => 2024,
            'status' => 'active',
            'ownership_type' => 'own',
            'current_odometer' => 1000,
            'purchase_price' => 50000,
            'purchase_date' => now(),
        ]);
    }

    // Create Pricing Policy
    $policy = \App\Models\PricingPolicy::firstOrCreate(
        ['company_id' => $company->id, 'name' => 'Standard Daily'],
        [
            'rental_type' => 'daily',
            'is_active' => true,
        ]
    );

    // Add Rules to Policy (Base Rate)
    // We need to see how rules are stored. 
    // PricingPolicy hasMany PricingRule (the OLD one, now we have DynamicPricingRule)
    // Wait, I didn't rename the OLD table. I renamed my NEW table.
    // So the old 'pricing_rules' table still exists and is used by Policy.
    // I need to add a rule there for base_rate.

    DB::table('pricing_rules')->updateOrInsert(
        ['pricing_policy_id' => $policy->id, 'rule_type' => 'base_rate'],
        ['value' => 100, 'calculation_method' => 'flat']
    );

    // Create Dynamic Rule
    \App\Models\DynamicPricingRule::updateOrCreate(
        ['name' => 'Weekend Surge Test', 'company_id' => $company->id],
        [
            'rule_type' => 'weekend',
            'conditions' => ['days' => ['Sat', 'Sun']],
            'adjustment_type' => 'percentage',
            'adjustment_value' => 10.00,
            'priority' => 1,
            'is_active' => true
        ]
    );

    $rental = new \App\Models\Rental();
    $rental->company_id = $company->id;
    $rental->vehicle_id = $vehicle->id;
    $rental->rental_type = 'daily';
    $rental->status = 'active';
    // Force Saturday
    $rental->start_datetime = \Carbon\Carbon::parse('next Saturday 10:00:00');
    $rental->end_datetime = \Carbon\Carbon::parse('next Sunday 10:00:00');

    $calc = new \App\Services\RentalPriceCalculator();
    $result = $calc->calculate($rental);

    $this->info("Base Rent: " . $result->base_amount);
    $dynamicAdjustments = array_sum(array_map(fn($adj) => $adj->calculated_amount, $result->adjustments));
    $this->info("Dynamic Adjustments: " . $dynamicAdjustments);
    $this->info("Total: " . $result->final_amount);

    if ($dynamicAdjustments > 0) {
        $this->info("SUCCESS: Dynamic pricing applied!");
    } else {
        $this->warn("WARNING: No dynamic adjustment.");
    }
});
