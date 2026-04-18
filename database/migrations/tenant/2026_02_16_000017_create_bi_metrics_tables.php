<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Daily Branch Metrics
        Schema::create('daily_branch_metrics', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('branch_id')->nullable();
            $table->date('date');

            $table->decimal('total_revenue', 15, 2)->default(0);
            $table->decimal('total_expenses', 15, 2)->default(0);
            $table->integer('rentals_count')->default(0);
            $table->integer('active_vehicles_count')->default(0);

            $table->unique(['branch_id', 'date']);
            $table->timestamps();
        });

        // 2. Bus Profitability Metrics (Aggregated by Month)
        Schema::create('bus_profitability_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id');
            $table->string('month_year'); // YYYY-MM

            $table->decimal('total_revenue', 15, 2)->default(0);
            $table->decimal('fuel_cost', 15, 2)->default(0);
            $table->decimal('maintenance_cost', 15, 2)->default(0);
            $table->decimal('net_profit', 15, 2)->default(0);
            $table->integer('days_rented')->default(0);
            $table->decimal('total_km', 10, 2)->default(0);

            $table->unique(['vehicle_id', 'month_year']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bus_profitability_metrics');
        Schema::dropIfExists('daily_branch_metrics');
    }
};
