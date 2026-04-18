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
        Schema::create('fuel_logs', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('branch_id')->nullable();
            $table->foreignId('vehicle_id');
            $table->foreignId('vendor_id')->nullable();
            $table->decimal('odometer_reading', 15, 2);
            $table->decimal('liters', 10, 2);
            $table->decimal('cost_per_liter', 10, 2);
            $table->decimal('total_amount', 15, 2);
            $table->date('date');
            $table->foreignId('created_by');
            $table->timestamps();

            // Optimization for Profitability Engine
            $table->index(['vehicle_id', 'date']);
            $table->index(['date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fuel_logs');
    }
};
