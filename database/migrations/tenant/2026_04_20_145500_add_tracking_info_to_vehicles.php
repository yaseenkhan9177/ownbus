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
        Schema::table('vehicles', function (Blueprint $table) {
            if (!Schema::hasColumn('vehicles', 'tracking_status')) {
                $table->string('tracking_status')->default('offline')->after('status');
            }
            if (!Schema::hasColumn('vehicles', 'last_gps_ping_at')) {
                $table->timestamp('last_gps_ping_at')->nullable()->after('tracking_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn(['tracking_status', 'last_gps_ping_at']);
        });
    }
};
