<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add performance indexes for ERP-phase tables that were missing coverage.
     * Targets: drivers, journal_entries, journal_entry_lines, customers,
     *          fuel_logs, maintenance_records, rentals (new columns), vehicles (new columns).
     */
    public function up(): void
    {
        // ── Drivers ─────────────────────────────────────────────────────────────
        if (Schema::hasTable('drivers')) {
            Schema::table('drivers', function (Blueprint $table) {
                if (!Schema::hasIndex('drivers', 'idx_drivers_status'))
                    $table->index('status', 'idx_drivers_status');
                if (Schema::hasColumn('drivers', 'branch_id') && !Schema::hasIndex('drivers', 'idx_drivers_branch'))
                    $table->index('branch_id', 'idx_drivers_branch');
                if (Schema::hasColumn('drivers', 'license_expiry') && !Schema::hasIndex('drivers', 'idx_drivers_license_expiry'))
                    $table->index('license_expiry', 'idx_drivers_license_expiry');
            });
        }

        // ── Journal Entries (header) ─────────────────────────────────────────────
        if (Schema::hasTable('journal_entries')) {
            Schema::table('journal_entries', function (Blueprint $table) {
                if (Schema::hasColumn('journal_entries', 'date') && !Schema::hasIndex('journal_entries', 'idx_je_date'))
                    $table->index('date', 'idx_je_date');
                if (Schema::hasColumn('journal_entries', 'is_posted') && !Schema::hasIndex('journal_entries', 'idx_je_posted'))
                    $table->index('is_posted', 'idx_je_posted');
                if (Schema::hasColumn('journal_entries', 'branch_id') && !Schema::hasIndex('journal_entries', 'idx_je_branch'))
                    $table->index('branch_id', 'idx_je_branch');
                if (Schema::hasColumn('journal_entries', 'vehicle_id') && !Schema::hasIndex('journal_entries', 'idx_je_vehicle'))
                    $table->index('vehicle_id', 'idx_je_vehicle');
                if (Schema::hasColumn('journal_entries', 'is_posted') && Schema::hasColumn('journal_entries', 'date') &&
                    !Schema::hasIndex('journal_entries', 'idx_je_posted_date'))
                    $table->index(['is_posted', 'date'], 'idx_je_posted_date');
            });
        }

        // ── Journal Entry Lines (detail) ─────────────────────────────────────────
        if (Schema::hasTable('journal_entry_lines')) {
            Schema::table('journal_entry_lines', function (Blueprint $table) {
                if (Schema::hasColumn('journal_entry_lines', 'account_id') && !Schema::hasIndex('journal_entry_lines', 'idx_jel_account'))
                    $table->index('account_id', 'idx_jel_account');
                if (Schema::hasColumn('journal_entry_lines', 'journal_entry_id') && !Schema::hasIndex('journal_entry_lines', 'idx_jel_entry'))
                    $table->index('journal_entry_id', 'idx_jel_entry');
            });
        }

        // ── Customers ────────────────────────────────────────────────────────────
        if (Schema::hasTable('customers')) {
            Schema::table('customers', function (Blueprint $table) {
                if (Schema::hasColumn('customers', 'is_credit_blocked') && !Schema::hasIndex('customers', 'idx_customers_credit_blocked'))
                    $table->index('is_credit_blocked', 'idx_customers_credit_blocked');
                if (Schema::hasColumn('customers', 'branch_id') && !Schema::hasIndex('customers', 'idx_customers_branch'))
                    $table->index('branch_id', 'idx_customers_branch');
                if (Schema::hasColumn('customers', 'customer_type') && !Schema::hasIndex('customers', 'idx_customers_type'))
                    $table->index('customer_type', 'idx_customers_type');
            });
        }

        // ── Fuel Logs ────────────────────────────────────────────────────────────
        if (Schema::hasTable('fuel_logs')) {
            Schema::table('fuel_logs', function (Blueprint $table) {
                if (Schema::hasColumn('fuel_logs', 'vehicle_id') && !Schema::hasIndex('fuel_logs', 'idx_fuel_vehicle'))
                    $table->index('vehicle_id', 'idx_fuel_vehicle');
                if (Schema::hasColumn('fuel_logs', 'log_date') && !Schema::hasIndex('fuel_logs', 'idx_fuel_date'))
                    $table->index('log_date', 'idx_fuel_date');
                if (Schema::hasColumn('fuel_logs', 'driver_id') && !Schema::hasIndex('fuel_logs', 'idx_fuel_driver'))
                    $table->index('driver_id', 'idx_fuel_driver');
            });
        }

        // ── Maintenance Records ──────────────────────────────────────────────────
        if (Schema::hasTable('maintenance_records')) {
            Schema::table('maintenance_records', function (Blueprint $table) {
                if (Schema::hasColumn('maintenance_records', 'vehicle_id') && !Schema::hasIndex('maintenance_records', 'idx_maint_vehicle'))
                    $table->index('vehicle_id', 'idx_maint_vehicle');
                if (Schema::hasColumn('maintenance_records', 'status') && !Schema::hasIndex('maintenance_records', 'idx_maint_status'))
                    $table->index('status', 'idx_maint_status');
                if (Schema::hasColumn('maintenance_records', 'scheduled_date') && !Schema::hasIndex('maintenance_records', 'idx_maint_scheduled'))
                    $table->index('scheduled_date', 'idx_maint_scheduled');
            });
        }

        // ── Rentals (new ERP columns) ────────────────────────────────────────────
        Schema::table('rentals', function (Blueprint $table) {
            if (Schema::hasColumn('rentals', 'driver_id') && !Schema::hasIndex('rentals', 'idx_rentals_driver'))
                $table->index('driver_id', 'idx_rentals_driver');
            if (Schema::hasColumn('rentals', 'customer_id') && !Schema::hasIndex('rentals', 'idx_rentals_customer'))
                $table->index('customer_id', 'idx_rentals_customer');
            if (Schema::hasColumn('rentals', 'vehicle_id') && !Schema::hasIndex('rentals', 'idx_rentals_vehicle'))
                $table->index('vehicle_id', 'idx_rentals_vehicle');
            if (Schema::hasColumn('rentals', 'branch_id') && !Schema::hasIndex('rentals', 'idx_rentals_branch'))
                $table->index('branch_id', 'idx_rentals_branch');
            if (Schema::hasColumn('rentals', 'start_date') && !Schema::hasIndex('rentals', 'idx_rentals_start_date'))
                $table->index('start_date', 'idx_rentals_start_date');
            if (Schema::hasColumn('rentals', 'end_date') && !Schema::hasIndex('rentals', 'idx_rentals_end_date'))
                $table->index('end_date', 'idx_rentals_end_date');
        });

        // ── Vehicles (document expiry columns) ──────────────────────────────────
        Schema::table('vehicles', function (Blueprint $table) {
            if (Schema::hasColumn('vehicles', 'registration_expiry') && !Schema::hasIndex('vehicles', 'idx_vehicles_reg_expiry'))
                $table->index('registration_expiry', 'idx_vehicles_reg_expiry');
            if (Schema::hasColumn('vehicles', 'insurance_expiry') && !Schema::hasIndex('vehicles', 'idx_vehicles_ins_expiry'))
                $table->index('insurance_expiry', 'idx_vehicles_ins_expiry');
            if (Schema::hasColumn('vehicles', 'inspection_expiry_date') && !Schema::hasIndex('vehicles', 'idx_vehicles_insp_expiry'))
                $table->index('inspection_expiry_date', 'idx_vehicles_insp_expiry');
            if (Schema::hasColumn('vehicles', 'branch_id') && !Schema::hasIndex('vehicles', 'idx_vehicles_branch'))
                $table->index('branch_id', 'idx_vehicles_branch');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('drivers')) {
            Schema::table('drivers', function (Blueprint $table) {
                $table->dropIndexIfExists('idx_drivers_status');
                $table->dropIndexIfExists('idx_drivers_branch');
                $table->dropIndexIfExists('idx_drivers_license_expiry');
            });
        }

        if (Schema::hasTable('journal_entries')) {
            Schema::table('journal_entries', function (Blueprint $table) {
                $table->dropIndexIfExists('idx_je_date');
                $table->dropIndexIfExists('idx_je_posted');
                $table->dropIndexIfExists('idx_je_branch');
                $table->dropIndexIfExists('idx_je_vehicle');
                $table->dropIndexIfExists('idx_je_posted_date');
            });
        }

        if (Schema::hasTable('journal_entry_lines')) {
            Schema::table('journal_entry_lines', function (Blueprint $table) {
                $table->dropIndexIfExists('idx_jel_account');
                $table->dropIndexIfExists('idx_jel_entry');
            });
        }

        if (Schema::hasTable('customers')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->dropIndexIfExists('idx_customers_credit_blocked');
                $table->dropIndexIfExists('idx_customers_branch');
                $table->dropIndexIfExists('idx_customers_type');
            });
        }

        if (Schema::hasTable('fuel_logs')) {
            Schema::table('fuel_logs', function (Blueprint $table) {
                $table->dropIndexIfExists('idx_fuel_vehicle');
                $table->dropIndexIfExists('idx_fuel_date');
                $table->dropIndexIfExists('idx_fuel_driver');
            });
        }

        if (Schema::hasTable('maintenance_records')) {
            Schema::table('maintenance_records', function (Blueprint $table) {
                $table->dropIndexIfExists('idx_maint_vehicle');
                $table->dropIndexIfExists('idx_maint_status');
                $table->dropIndexIfExists('idx_maint_scheduled');
            });
        }

        Schema::table('rentals', function (Blueprint $table) {
            $table->dropIndexIfExists('idx_rentals_driver');
            $table->dropIndexIfExists('idx_rentals_customer');
            $table->dropIndexIfExists('idx_rentals_vehicle');
            $table->dropIndexIfExists('idx_rentals_branch');
            $table->dropIndexIfExists('idx_rentals_start_date');
            $table->dropIndexIfExists('idx_rentals_end_date');
        });

        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropIndexIfExists('idx_vehicles_reg_expiry');
            $table->dropIndexIfExists('idx_vehicles_ins_expiry');
            $table->dropIndexIfExists('idx_vehicles_insp_expiry');
            $table->dropIndexIfExists('idx_vehicles_branch');
        });
    }
};
