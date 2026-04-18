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
        Schema::create('pricing_policies', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('branch_id')->nullable();
            $table->string('name');
            $table->string('rental_type'); // hourly, daily, etc.
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('pricing_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pricing_policy_id');
            $table->string('rule_type'); // min_hours, extra_km_rate, etc.
            $table->decimal('value', 10, 2);
            $table->string('calculation_method')->default('flat'); // flat, percentage
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricing_rules');
        Schema::dropIfExists('pricing_policies');
    }
};
