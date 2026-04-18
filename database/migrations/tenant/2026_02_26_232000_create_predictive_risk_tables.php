<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_risk_predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id');
            $table->integer('risk_score');
            $table->float('probability_30_days');
            $table->string('risk_level'); // high, medium, low
            $table->json('signals_json');
            $table->timestamp('predicted_at');
            $table->timestamps();

            $table->index(['vehicle_id', 'predicted_at']);
        });

        Schema::create('driver_risk_predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id');
            $table->integer('risk_score');
            $table->float('probability_60_days');
            $table->string('risk_level'); // high, medium, low
            $table->json('signals_json');
            $table->timestamp('predicted_at');
            $table->timestamps();

            $table->index(['driver_id', 'predicted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_risk_predictions');
        Schema::dropIfExists('vehicle_risk_predictions');
    }
};
