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
        Schema::create('gps_devices', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('vehicle_id')->nullable();

            $table->string('imei_number')->unique();
            $table->string('provider')->nullable(); // e.g., Teltonika
            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status']);
            $table->index('imei_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gps_devices');
    }
};
