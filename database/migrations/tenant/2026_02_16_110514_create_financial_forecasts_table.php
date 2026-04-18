<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_forecasts', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('branch_id')->nullable();
            $table->date('forecast_date');
            $table->string('metric_type'); // 'revenue', 'expense'
            $table->decimal('predicted_value', 15, 2);
            $table->decimal('confidence_score', 5, 2)->nullable(); // 0.00 to 1.00
            $table->timestamps();

            $table->index(['metric_type', 'forecast_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_forecasts');
    }
};
