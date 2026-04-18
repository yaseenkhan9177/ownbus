<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dynamic_pricing_rules', function (Blueprint $table) {
            $table->id();
            
            $table->string('name');
            $table->string('rule_type'); // 'seasonal', 'weekend', 'vip', 'event'
            $table->json('conditions')->nullable(); // e.g. {"days": ["Fri", "Sat"], "start_date": "2024-06-01", "end_date": "2024-08-31"}
            $table->string('adjustment_type')->default('percentage'); // 'percentage', 'fixed'
            $table->decimal('adjustment_value', 10, 2); // e.g. 10.00 (%), -5.00 ($)
            $table->integer('priority')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dynamic_pricing_rules');
    }
};
