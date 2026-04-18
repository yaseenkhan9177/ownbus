<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anomalies', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('branch_id')->nullable();

            $table->string('type'); // e.g., 'financial_spike', 'utilization_drop'
            $table->string('severity')->default('medium'); // low, medium, high, critical
            $table->text('description')->nullable();

            $table->decimal('detected_value', 15, 2)->nullable();
            $table->decimal('expected_value', 15, 2)->nullable();

            // Polymorphic relation to what caused the anomaly (e.g., Transaction, Vehicle)
            $table->nullableMorphs('related_model');

            $table->string('status')->default('open'); // open, resolved, ignored
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('resolved_by')->nullable();

            $table->timestamps();

            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anomalies');
    }
};
