<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\Subscription;
use App\Models\Vehicle;
use App\Models\Customer;
use App\Models\Driver;
use App\Models\Rental;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Branch;
use App\Services\TenantService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Database\Seeders\RolesAndPermissionsSeeder;
use Database\Seeders\ChartOfAccountsSeeder;

class SeedDemoData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:seed-demo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed a complete Demo Company with all necessary data for the 9-step demo flow.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Demo Data Seeding...');

        // 1. Create Company in Central DB
        $companyName = 'Demo Fleet LLC';
        $ownerEmail = 'demo@example.com';
        
        $databaseName = 'tenant_' . Str::slug($companyName, '_') . '_' . time();

        $company = Company::create([
            'name'                  => $companyName,
            'trade_license_number'  => 'TL-DEMO-123456',
            'trn_number'            => 'TRN-DEMO-98765',
            'address'               => 'Demo Street, Business Bay',
            'country'               => 'United Arab Emirates',
            'total_vehicles'        => 10,
            'phone'                 => '+971500000000',
            'email'                 => $ownerEmail,
            'owner_name'            => 'Demo Admin',
            'status'                => 'active', // Pre-approved
            'agreed_to_terms'       => true,
            'database_name'         => $databaseName,
        ]);

        $this->info("Created Company: $companyName (ID: $company->id)");

        // 2. Create Owner User
        $user = User::create([
            'name'       => 'Demo Admin',
            'email'      => $ownerEmail,
            'password'   => Hash::make('password'), // Simple password for demo
            'company_id' => $company->id,
            'role'       => 'company_admin',
        ]);

        // 3. Subscription Setup
        $subscriptionPlan = SubscriptionPlan::where('slug', 'enterprise')->first();
        if ($subscriptionPlan) {
            Subscription::create([
                'company_id'           => $company->id,
                'plan_id'              => $subscriptionPlan->id,
                'plan_version'         => $subscriptionPlan->version,
                'status'               => 'active',
                'current_period_start' => now(),
                'current_period_end'   => now()->addYear(),
            ]);
        }

        // 4. Provision Tenant Database
        $this->info("Provisioning Tenant Database: $databaseName...");
        TenantService::createDatabase($databaseName);
        TenantService::migrateDatabase($databaseName);

        // Switch context to tenant
        config(['database.connections.tenant.database' => $databaseName]);
        DB::purge('tenant');
        DB::reconnect('tenant');
        
        // 5. Setup Tenant Defaults (Roles, Permissions, Accounts)
        DB::connection('tenant')->table('branches')->insert([
            'name'           => 'Main Branch',
            'code'           => 'MAIN',
            'is_head_office' => true,
            'is_active'      => true,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);
        
        $branch = Branch::on('tenant')->first();

        setPermissionsTeamId($company->id);
        $roleSeeder = new RolesAndPermissionsSeeder();
        $roleSeeder->run();
        
        $user->assignRole('Owner');

        $chartOfAccountsSeeder = new ChartOfAccountsSeeder();
        $chartOfAccountsSeeder->run($company);

        // 6. Seed Vehicles
        $this->info("Seeding Vehicles...");
        $vehicles = [];
        for ($i = 1; $i <= 5; $i++) {
            $vehicles[] = Vehicle::on('tenant')->create([
                'vehicle_number' => 'DXB-DEMO-' . $i,
                'name' => 'Mercedes Tourismo ' . $i,
                'make' => 'Mercedes-Benz',
                'model' => 'Tourismo',
                'year' => 2024,
                'status' => \App\Models\Vehicle::STATUS_AVAILABLE,
                'purchase_price' => 850000,
                'purchase_date' => now()->subMonths(6),
                'current_odometer' => rand(1000, 50000),
                'type' => '50-Seater Luxury',
                'daily_rate' => 1500.00,
                'company_id' => $company->id,
            ]);
        }

        // 7. Seed Drivers
        $this->info("Seeding Drivers...");
        $drivers = [];
        for ($i = 1; $i <= 3; $i++) {
            $driverUser = User::create([
                'name'       => 'Driver ' . $i,
                'email'      => "driver{$i}@demo.com",
                'password'   => Hash::make('password'),
                'company_id' => $company->id,
                'role'       => 'driver',
            ]);
            $driverUser->assignRole('Driver');

            $drivers[] = Driver::on('tenant')->create([
                'company_id' => $company->id,
                'driver_code' => 'DRV-D' . $i,
                'first_name' => 'Driver',
                'last_name' => (string) $i,
                'phone' => '+97155000000' . $i,
                'email' => "driver{$i}@demo.com",
                'status' => 'active',
                'user_id' => $driverUser->id,
                'salary' => 4500,
                'license_number' => 'LIC-DEMO-'. $i,
                'license_expiry_date' => now()->addYear(),
                'national_id' => '784-1234-567890-' . $i,
                'license_type' => 'bus',
                'hire_date' => now()->subMonths(12),
                'address' => 'Driver Accommodation, Sonapur',
                'city' => 'Dubai',
                'emergency_contact_name' => 'Demo Contact',
                'emergency_contact_phone' => '+97150123000' . $i,
                'created_by' => $user->id,
            ]);
        }

        // 8. Seed Customer
        $this->info("Seeding Customers...");
        $customer = Customer::on('tenant')->create([
            'company_id' => $company->id,
            'first_name' => 'Corporate',
            'last_name' => 'Client Demo',
            'email' => 'client@corporate.com',
            'phone' => '+971501234567',
            'type' => 'corporate',
            'status' => 'active',
        ]);

        // 9. Seed a Completed Booking/Rental (to show Dashboard Stats)
        $this->info("Seeding Completed Booking for Stats...");
        $rental = clone (new Rental());
        $rental->setConnection('tenant');
        $rental = Rental::on('tenant')->create([
            'uuid' => Str::uuid(),
            'company_id' => $company->id,
            'branch_id' => $branch->id,
            'vehicle_id' => $vehicles[0]->id,
            'customer_id' => $customer->id,
            'driver_id' => $driverUser->id,
            'rental_type' => 'daily',
            'rental_number' => 'RENT-DEMO-001',
            'start_date' => now()->subDays(3),
            'end_date' => now()->subDays(1),
            'status' => 'completed',
            'final_amount' => 4500.00,
            'tax' => 225.00,
            'pickup_location' => 'Dubai Marina',
            'dropoff_location' => 'Abu Dhabi Corniche',
            'start_odometer' => 10000,
            'end_odometer' => 10300,
        ]);

        // Create Invoice and Payment for the Rental
        $this->info("Seeding Finances...");
        // This simulates phase 7 and 8 of demo
        $cashAccount = Account::on('tenant')->where('account_type', 'asset')->where('account_name', 'like', '%Cash%')->first();
        $revenueAccount = Account::on('tenant')->where('account_type', 'income')->first();

        if ($cashAccount && $revenueAccount) {
            $journalEntry = JournalEntry::on('tenant')->create([
                'branch_id' => $branch->id,
                'reference_type' => 'App\Models\Rental',
                'reference_id' => $rental->id,
                'date' => now()->subDays(1),
                'description' => 'Payment for Rental ' . $rental->rental_number,
                'is_posted' => true,
                'posted_at' => now(),
            ]);

            JournalEntryLine::on('tenant')->create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $cashAccount->id,
                'branch_id' => $branch->id,
                'debit' => 4725.00,
                'credit' => 0,
            ]);

            JournalEntryLine::on('tenant')->create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $revenueAccount->id,
                'branch_id' => $branch->id,
                'debit' => 0,
                'credit' => 4725.00,
            ]);
        }

        $this->info("Demo Data Seeding Completed Successfully!");
        $this->info("Login Email: $ownerEmail");
        $this->info("Login Password: password");
    }
}
