<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Vehicle GPS tracking status
        Schema::table('vehicles', function (Blueprint $table) {
            $table->enum('tracking_status', ['live', 'offline', 'unknown'])->default('unknown')->after('telematics_device_id');
            $table->timestamp('last_gps_ping_at')->nullable()->after('tracking_status');
        });

        // Live GPS locations (hot store)
        Schema::create('vehicle_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id');
            
            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);
            $table->decimal('speed', 6, 2)->nullable()->comment('km/h');
            $table->smallInteger('heading')->nullable()->comment('0-360 degrees');
            $table->decimal('accuracy', 8, 2)->nullable()->comment('metres');
            $table->string('source')->default('device')->comment('device|manual|api');
            $table->timestamp('recorded_at');
            $table->timestamps();

            $table->index(['vehicle_id', 'recorded_at']);
            $table->index(['recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_locations');
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn(['tracking_status', 'last_gps_ping_at']);
        });
    }
};
