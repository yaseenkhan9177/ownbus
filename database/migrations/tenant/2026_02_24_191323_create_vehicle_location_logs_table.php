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
        Schema::create('vehicle_location_logs', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('vehicle_id');

            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->decimal('speed', 5, 2)->default(0);
            $table->boolean('ignition_status')->default(false);

            $table->timestamp('timestamp');
            $table->timestamp('created_at')->useCurrent();

            // Critical: Indexed for fast historical retrieval
            $table->index(['vehicle_id', 'timestamp']);
            $table->index('timestamp'); // For pruning job
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_location_logs');
    }
};
