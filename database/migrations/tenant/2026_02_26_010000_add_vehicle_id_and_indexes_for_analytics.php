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
        Schema::table('journal_entries', function (Blueprint $table) {
            if (!Schema::hasColumn('journal_entries', 'vehicle_id')) {
                $table->foreignId('vehicle_id')->after('branch_id')->nullable();
            }

            // Re-adding or ensuring indexes for analytics performance
            $table->index(['vehicle_id', 'date'], 'idx_journals_vehicle_date');
        });

        Schema::table('fuel_logs', function (Blueprint $table) {
            $table->index(['vehicle_id', 'created_at'], 'idx_fuel_logs_vehicle_created');
        });

        Schema::table('rentals', function (Blueprint $table) {
            $table->index(['vehicle_id', 'start_date', 'end_date'], 'idx_rentals_vehicle_period');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->dropIndex('idx_rentals_vehicle_period');
        });

        Schema::table('fuel_logs', function (Blueprint $table) {
            $table->dropIndex('idx_fuel_logs_vehicle_created');
        });

        Schema::table('journal_entries', function (Blueprint $table) {
            $table->dropIndex('idx_journals_vehicle_date');
            $table->dropColumn('vehicle_id');
        });
    }
};
