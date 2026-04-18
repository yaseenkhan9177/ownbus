<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            if (Schema::hasColumn('rentals', 'status') && Schema::hasColumn('rentals', 'start_datetime') && Schema::hasColumn('rentals', 'end_datetime')) {
                if (!Schema::hasIndex('rentals', 'idx_rentals_composite_search')) {
                    $table->index(['status', 'start_datetime', 'end_datetime'], 'idx_rentals_composite_search');
                }
            }
            if (Schema::hasColumn('rentals', 'bus_id') && Schema::hasColumn('rentals', 'start_datetime') && Schema::hasColumn('rentals', 'end_datetime')) {
                if (!Schema::hasIndex('rentals', 'idx_rentals_bus_conflict')) {
                    $table->index(['bus_id', 'start_datetime', 'end_datetime'], 'idx_rentals_bus_conflict');
                }
            }
            if (Schema::hasColumn('rentals', 'payment_status')) {
                if (!Schema::hasIndex('rentals', 'idx_rentals_payment_status')) {
                    $table->index(['payment_status'], 'idx_rentals_payment_status');
                }
            }
        });

        Schema::table('vehicles', function (Blueprint $table) {
            if (Schema::hasColumn('vehicles', 'status')) {
                if (!Schema::hasIndex('vehicles', 'idx_vehicles_company_status')) {
                    $table->index(['status'], 'idx_vehicles_company_status');
                }
            }
        });

        if (Schema::hasTable('bus_utilization_logs')) {
            Schema::table('bus_utilization_logs', function (Blueprint $table) {
                if (Schema::hasColumn('bus_utilization_logs', 'date')) {
                    if (!Schema::hasIndex('bus_utilization_logs', 'idx_bus_util_company_date')) {
                        $table->index(['date'], 'idx_bus_util_company_date');
                    }
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->dropIndex('idx_rentals_composite_search');
            $table->dropIndex('idx_rentals_bus_conflict');
            $table->dropIndex('idx_rentals_payment_status');
        });

        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropIndex('idx_vehicles_company_status');
        });

        if (Schema::hasTable('bus_utilization_logs')) {
            Schema::table('bus_utilization_logs', function (Blueprint $table) {
                $table->dropIndex('idx_bus_util_company_date');
            });
        }
    }
};
