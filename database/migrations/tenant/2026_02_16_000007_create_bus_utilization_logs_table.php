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
        Schema::create('bus_utilization_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bus_id');
            $table->foreignId('rental_id')->nullable();
            $table->decimal('hours_used', 8, 2)->default(0);
            $table->decimal('km_used', 10, 2)->default(0);
            $table->decimal('fuel_consumed', 8, 2)->nullable();
            $table->date('date');
            $table->timestamps();

            // Indexes for analytics
            $table->index(['bus_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bus_utilization_logs');
    }
};
