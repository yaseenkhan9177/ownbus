<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Subscriptions table - critical for access checks
        // No-op: subscriptions is a central table
        

        // Rentals table - high-volume transactional data
        Schema::table('rentals', function (Blueprint $table) {
            if (!Schema::hasIndex('rentals', 'idx_rentals_status')) $table->index('status', 'idx_rentals_status');
            if (!Schema::hasIndex('rentals', 'idx_rentals_start')) $table->index('start_datetime', 'idx_rentals_start');
            if (!Schema::hasIndex('rentals', 'idx_rentals_end')) $table->index('end_datetime', 'idx_rentals_end');
            if (!Schema::hasIndex('rentals', 'idx_rentals_company_status')) $table->index(['status'], 'idx_rentals_company_status');
            if (!Schema::hasIndex('rentals', 'idx_rentals_company_start')) $table->index(['start_datetime'], 'idx_rentals_company_start');
            if (!Schema::hasIndex('rentals', 'idx_rentals_status_start')) $table->index(['status', 'start_datetime'], 'idx_rentals_status_start');
        });

        // Vehicles table - fleet availability queries
        if (Schema::hasTable('vehicles')) {
            Schema::table('vehicles', function (Blueprint $table) {
                if (!Schema::hasIndex('vehicles', 'idx_vehicles_status')) $table->index('status', 'idx_vehicles_status');
                if (!Schema::hasIndex('vehicles', 'idx_vehicles_created')) $table->index('created_at', 'idx_vehicles_created');
                if (Schema::hasColumn('vehicles', 'type') && !Schema::hasIndex('vehicles', 'idx_vehicles_status_type')) {
                    $table->index(['status', 'type'], 'idx_vehicles_status_type');
                }
            });
        }

        // Subscription invoices - billing queries
        // No-op: subscription_invoices is a central table
        

        // Subscription events - audit trail lookups
        // No-op: subscription_events is a central table
        

        // Users table - authentication and company lookups
        // No-op: users is a central table
        

        // Companies table - multi-tenant queries
        // No-op: companies is a central table
        

        // Financial transactions - accounting queries (optional)
        if (Schema::hasTable('financial_transactions')) {
            Schema::table('financial_transactions', function (Blueprint $table) {
                if (Schema::hasColumn('financial_transactions', 'type') && !Schema::hasIndex('financial_transactions', 'idx_transactions_type')) {
                    $table->index('type', 'idx_transactions_type');
                }
                if (!Schema::hasIndex('financial_transactions', 'idx_transactions_date')) $table->index('transaction_date', 'idx_transactions_date');
                if (!Schema::hasIndex('financial_transactions', 'idx_transactions_company_date')) $table->index(['transaction_date'], 'idx_transactions_company_date');
            });
        }

        // Bookings table - rental pipeline (optional)
        if (Schema::hasTable('bookings')) {
            Schema::table('bookings', function (Blueprint $table) {
                if (!Schema::hasIndex('bookings', 'idx_bookings_status')) $table->index('status', 'idx_bookings_status');
                if (!Schema::hasIndex('bookings', 'idx_bookings_created')) $table->index('created_at', 'idx_bookings_created');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op: subscriptions is a central table
        

        Schema::table('rentals', function (Blueprint $table) {
            $table->dropIndex('idx_rentals_status');
            $table->dropIndex('idx_rentals_start');
            $table->dropIndex('idx_rentals_end');
            $table->dropIndex('idx_rentals_company_status');
            $table->dropIndex('idx_rentals_company_start');
            $table->dropIndex('idx_rentals_status_start');
        });

        if (Schema::hasTable('vehicles')) {
            Schema::table('vehicles', function (Blueprint $table) {
                $table->dropIndex('idx_vehicles_status');
                $table->dropIndex('idx_vehicles_created');
                $table->dropIndex('idx_vehicles_status_type');
            });
        }

        // No-op: subscription_invoices is a central table
        

        // No-op: subscription_events is a central table
        

        // No-op: users is a central table
        

        // No-op: companies is a central table
        

        if (Schema::hasTable('financial_transactions')) {
            Schema::table('financial_transactions', function (Blueprint $table) {
                $table->dropIndex('idx_transactions_type');
                $table->dropIndex('idx_transactions_date');
                $table->dropIndex('idx_transactions_company_date');
            });
        }

        if (Schema::hasTable('bookings')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->dropIndex('idx_bookings_status');
                $table->dropIndex('idx_bookings_created');
            });
        }
    }
};
