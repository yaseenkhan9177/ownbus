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
        // 1. Core Maintenance Records Table
        Schema::create('maintenance_records', function (Blueprint $table) {
            $table->id();

            $table->foreignId('branch_id')->nullable();
            $table->foreignId('vehicle_id');

            $table->string('maintenance_number');
            $table->enum('type', ['preventive', 'corrective', 'accident', 'inspection', 'insurance']);
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled');

            $table->date('scheduled_date')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('completed_date')->nullable();
            $table->date('next_due_date')->nullable();

            $table->integer('odometer_reading')->nullable(); // reading at time of maintenance
            $table->decimal('total_cost', 15, 2)->default(0.00);

            $table->foreignId('vendor_id')->nullable(); // Assuming vendors might relate to customers table or a separate vendors table. Using customers for now as system has it. Wait, the system has a Supplier ledger but I'm not sure if there's a vendors table. Let's make it unsignedBigInteger for now without foreign constraint if unsure.
            $table->text('description')->nullable();
            $table->text('notes')->nullable();

            $table->foreignId('created_by');
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index('vehicle_id');
            $table->index('status');
            $table->index('next_due_date');
            $table->index('scheduled_date');

            // Unique composite
            $table->unique(['maintenance_number']);
        });

        // 2. Maintenance Items (Cost Breakdown)
        Schema::create('maintenance_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_record_id');
            $table->enum('item_type', ['part', 'labor', 'service']);
            $table->string('description');
            $table->decimal('quantity', 10, 2)->default(1.00);
            $table->decimal('unit_cost', 15, 2)->default(0.00);
            $table->decimal('total_cost', 15, 2)->default(0.00);
            $table->timestamps();

            $table->index('maintenance_record_id');
        });

        // 3. Vehicle Service Intervals (Preventive Engine)
        Schema::create('vehicle_service_intervals', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vehicle_id');

            $table->string('service_type'); // e.g., oil_change, brake_check, etc.

            $table->integer('interval_km')->nullable();
            $table->integer('interval_days')->nullable();

            $table->integer('last_service_odometer')->nullable();
            $table->date('last_service_date')->nullable();

            $table->integer('next_due_odometer')->nullable();
            $table->date('next_due_date')->nullable();

            $table->timestamps();

            $table->index('vehicle_id');
            $table->index('next_due_date');

            // A vehicle should only have one active configuration per service type
            $table->unique(['vehicle_id', 'service_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_service_intervals');
        Schema::dropIfExists('maintenance_items');
        Schema::dropIfExists('maintenance_records');
    }
};
