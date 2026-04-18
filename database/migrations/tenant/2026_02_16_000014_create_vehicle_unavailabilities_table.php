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
        if (!Schema::hasTable('vehicle_unavailabilities')) {
            Schema::create('vehicle_unavailabilities', function (Blueprint $table) {
                $table->id();
                $table->foreignId('vehicle_id');
                $table->dateTime('start_datetime');
                $table->dateTime('end_datetime');
                $table->string('reason_type')->default('maintenance');
                $table->text('description')->nullable();
                $table->foreignId('created_by')->nullable();
                $table->timestamps();

                // Index for quick overlap checks
                $table->index(['vehicle_id', 'start_datetime', 'end_datetime'], 'veh_unavail_range_idx');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_unavailabilities');
    }
};
