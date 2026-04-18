<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_predictions', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('vehicle_id');
            $table->string('prediction_type'); // 'mileage', 'time', 'utilization'
            $table->date('predicted_date');
            $table->integer('confidence_score')->default(50); // 0-100
            $table->text('reason')->nullable();
            $table->string('status')->default('pending'); // pending, acknowledged, scheduled
            $table->timestamps();

            $table->index(['status']);
            $table->index('predicted_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_predictions');
    }
};
