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
            // Rename to more generic 'vehicles' (already done in previous migration, just ensuring fields)
            // Rename table if needed? No, 'vehicles' is fine, but we call them buses in domain.


            $table->foreignId('branch_id')->nullable()->after('id');

            $table->enum('ownership_type', ['own', 'vendor'])->default('own')->after('status');
            $table->string('vendor_name')->nullable()->after('ownership_type'); // Simple string for now or vendor_id if we had vendors table
            $table->decimal('asset_value', 15, 2)->nullable()->after('purchase_price');
            $table->string('depreciation_method')->nullable()->after('asset_value');
            $table->date('insurance_expiry')->nullable()->after('current_odometer');
            $table->date('registration_expiry')->nullable()->after('insurance_expiry');

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {

            $table->dropColumn([
                'branch_id',
                'ownership_type',
                'vendor_name',
                'asset_value',
                'depreciation_method',
                'insurance_expiry',
                'registration_expiry',
                'deleted_at'
            ]);
        });
    }
};
