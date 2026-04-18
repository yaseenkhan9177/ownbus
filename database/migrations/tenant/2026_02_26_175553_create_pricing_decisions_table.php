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
        Schema::create('pricing_decisions', function (Blueprint $table) {
            $table->id();
            $table->uuid('rental_uuid')->nullable();
            $table->foreignId('vehicle_id');
            $table->foreignId('branch_id');
            $table->foreignId('customer_id');
            $table->decimal('base_rate', 10, 2);
            $table->decimal('optimized_rate', 10, 2);
            $table->json('multipliers_json');
            $table->boolean('was_accepted')->default(false);
            $table->decimal('final_margin', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricing_decisions');
    }
};
