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
        if (Schema::hasTable('vehicles')) {
            Schema::table('vehicles', function (Blueprint $table) {});
        }

        // Bus Utilization Logs - high frequency analytic data
        if (Schema::hasTable('bus_utilization_logs')) {
            Schema::table('bus_utilization_logs', function (Blueprint $table) {

                if (Schema::hasColumn('bus_utilization_logs', 'date') && !Schema::hasIndex('bus_utilization_logs', 'idx_util_date')) {
                    $table->index('date', 'idx_util_date');
                }
                if (Schema::hasColumn('bus_utilization_logs', 'bus_id') && Schema::hasColumn('bus_utilization_logs', 'date') && !Schema::hasIndex('bus_utilization_logs', 'idx_util_bus_date')) {
                    $table->index(['bus_id', 'date'], 'idx_util_bus_date');
                }
            });
        }

        // Bus Profitability Metrics - ROI calculation data
        if (Schema::hasTable('bus_profitability_metrics')) {
            Schema::table('bus_profitability_metrics', function (Blueprint $table) {
                if (!Schema::hasIndex('bus_profitability_metrics', 'idx_profit_vehicle')) $table->index('vehicle_id', 'idx_profit_vehicle');
                if (!Schema::hasIndex('bus_profitability_metrics', 'idx_profit_created')) $table->index('created_at', 'idx_profit_created');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            // Index removed
        });

        Schema::table('bus_utilization_logs', function (Blueprint $table) {
            // Index removed
            $table->dropIndex('idx_util_date');
            $table->dropIndex('idx_util_bus_date');
        });

        Schema::table('bus_profitability_metrics', function (Blueprint $table) {
            $table->dropIndex('idx_profit_vehicle');
            $table->dropIndex('idx_profit_created');
        });
    }
};
