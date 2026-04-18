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
            // Enterprise Asset Management
            if (!Schema::hasColumn('vehicles', 'asset_value')) {
                $table->decimal('asset_value', 15, 2)->nullable()->after('status');
                $table->string('depreciation_method')->default('straight_line')->after('asset_value');
                $table->decimal('depreciation_rate', 5, 2)->nullable()->after('depreciation_method');
            }

            // Vendor Management
            if (!Schema::hasColumn('vehicles', 'vendor_id')) {
                $table->foreignId('vendor_id')->nullable()->after('id');
            }

            if (!Schema::hasColumn('vehicles', 'ownership_type')) {
                $table->string('ownership_type')->default('own')->after('status');
            }

            // IoT
            if (!Schema::hasColumn('vehicles', 'telematics_device_id')) {
                $table->string('telematics_device_id')->nullable()->unique()->after('vehicle_number');
            }

            // Performance Indexes
            // Index creation might fail if exists, try catch or check
            try {
                $table->index(['branch_id', 'status']);
            } catch (\Exception $e) {
                // index likely exists
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropIndex(['branch_id', 'status']);
            $table->dropColumn(['telematics_device_id', 'ownership_type', 'vendor_id', 'asset_value', 'depreciation_method', 'depreciation_rate']);
        });
    }
};
