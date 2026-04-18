<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Dedicated trips table — records each rental's operational trip leg.
     * A rental may have 1 trip (standard) or multiple (recurrent contract).
     */
    public function up(): void
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('rental_id')->constrained('rentals')->cascadeOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->nullOnDelete();
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();

            // Status lifecycle: pending → in_progress → completed | cancelled
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');

            // Scheduling
            $table->dateTime('scheduled_start')->nullable();
            $table->dateTime('scheduled_end')->nullable();

            // Actuals — recorded on start/complete
            $table->dateTime('actual_start')->nullable();
            $table->dateTime('actual_end')->nullable();
            $table->unsignedInteger('duration_minutes')->nullable(); // computed on complete

            // Odometer
            $table->unsignedInteger('odometer_start')->nullable();
            $table->unsignedInteger('odometer_end')->nullable();
            $table->unsignedInteger('distance_km')->nullable(); // = end - start

            // Pickup / Dropoff
            $table->string('pickup_location')->nullable();
            $table->string('dropoff_location')->nullable();

            // GPS snapshot at start/end (lat, lng stored as decimal)
            $table->decimal('start_lat', 10, 7)->nullable();
            $table->decimal('start_lng', 10, 7)->nullable();
            $table->decimal('end_lat', 10, 7)->nullable();
            $table->decimal('end_lng', 10, 7)->nullable();

            // Driver feedback / notes
            $table->text('driver_notes')->nullable();
            $table->tinyInteger('driver_rating')->nullable(); // 1–5 star

            // Fuel used on this trip (optional, from fuel logs)
            $table->decimal('fuel_used_liters', 8, 2)->nullable();

            // Audit
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('rental_id');
            $table->index('driver_id');
            $table->index('vehicle_id');
            $table->index('status');
            $table->index('actual_start');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
