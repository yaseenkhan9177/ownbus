<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('driver_trip_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id');
            $table->foreignId('rental_id')->nullable();
            

            $table->enum('type', ['fuel_upload', 'breakdown_report', 'trip_status'])->index();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'acknowledged'])->default('pending');

            $table->text('notes')->nullable();
            $table->string('photo_path')->nullable();
            $table->json('metadata')->nullable(); // fuel liters, odometer, location etc.
            $table->timestamp('reported_at')->useCurrent();
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_trip_reports');
    }
};
