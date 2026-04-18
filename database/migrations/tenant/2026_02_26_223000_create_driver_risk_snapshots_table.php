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
        Schema::create('driver_risk_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id');
            $table->integer('score');
            $table->string('risk_level'); // low, medium, high
            $table->json('breakdown_json');
            $table->timestamp('calculated_at')->useCurrent();

            $table->index(['driver_id', 'calculated_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_risk_snapshots');
    }
};
