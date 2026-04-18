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
        Schema::create('vehicle_maintenance_predictions', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('vehicle_id');
            $table->date('predicted_service_date');
            $table->string('risk_level'); // high, medium, low
            $table->decimal('avg_km_per_day', 10, 2)->default(0);
            $table->decimal('cost_growth_percentage', 8, 2)->nullable();
            $table->integer('interval_km')->nullable();
            $table->timestamp('calculated_at');
            $table->timestamps();

            $table->index(['risk_level']);
            $table->index('predicted_service_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_maintenance_predictions');
    }
};
