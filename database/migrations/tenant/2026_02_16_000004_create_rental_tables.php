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
        // 1. Rename bookings to rentals if it has data, or just create new if empty/safe
        // Strategy: Create new 'rentals' table to align with the new Enterprise architecture
        // We will leave 'bookings' for now or drop it if user confirms. 
        // For this plan, we are creating 'rentals'.

        if (!Schema::hasTable('rentals')) {
            Schema::create('rentals', function (Blueprint $table) {
                $table->id();
                
                $table->foreignId('branch_id')->nullable();
                $table->foreignId('customer_id');

                $table->string('rental_type'); // hourly, daily, monthly, distance
                $table->string('contract_number')->unique();
                $table->string('status')->default('draft');

                $table->dateTime('start_datetime');
                $table->dateTime('end_datetime');
                $table->dateTime('actual_start_datetime')->nullable();
                $table->dateTime('actual_end_datetime')->nullable();

                $table->string('pickup_location')->nullable();
                $table->string('dropoff_location')->nullable();

                $table->foreignId('bus_id')->nullable();
                $table->foreignId('driver_id')->nullable();

                $table->integer('odometer_start')->default(0);
                $table->integer('odometer_end')->nullable();

                $table->decimal('total_amount', 15, 2)->default(0);
                $table->decimal('tax_amount', 15, 2)->default(0);
                $table->decimal('grand_total', 15, 2)->default(0);
                $table->string('payment_status')->default('unpaid');

                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('rental_items')) {
            Schema::create('rental_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('rental_id');
                $table->string('item_type');
                $table->string('description');
                $table->decimal('quantity', 10, 2);
                $table->decimal('unit_price', 15, 2);
                $table->decimal('subtotal', 15, 2);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('rental_status_logs')) {
            Schema::create('rental_status_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('rental_id');
                $table->string('from_status')->nullable();
                $table->string('to_status');
                $table->foreignId('changed_by')->nullable();
                $table->string('reason')->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_status_logs');
        Schema::dropIfExists('rental_items');
        Schema::dropIfExists('rentals');
    }
};
