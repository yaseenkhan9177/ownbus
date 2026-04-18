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
        Schema::table('vehicle_location_logs', function (Blueprint $table) {
            $table->decimal('heading', 5, 2)->nullable()->after('longitude');
            $table->decimal('fuel_level', 5, 2)->nullable()->after('heading'); // Percentage or volume
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_location_logs', function (Blueprint $table) {
            $table->dropColumn(['heading', 'fuel_level']);
        });
    }
};
