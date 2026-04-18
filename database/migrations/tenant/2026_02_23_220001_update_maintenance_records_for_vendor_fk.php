<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('maintenance_records', function (Blueprint $table) {
            // Drop the old FK pointing to customers table
            // (Laravel creates FK name as: table_column_foreign)
            if (Schema::hasColumn('maintenance_records', 'vendor_id')) {
                // Try to drop the old constraint (may be named differently)
                try {
                    } catch (\Exception $e) {
                    // FK may not have existed (was unsignedBigInteger without constraint)
                }

                // Null out any existing vendor_id values as they were referencing customers
                DB::table('maintenance_records')->update(['vendor_id' => null]);

                // Drop & re-add the column with proper FK to vendors
                $table->dropColumn('vendor_id');
            }
        });

        Schema::table('maintenance_records', function (Blueprint $table) {
            $table->foreignId('vendor_id')
                ->nullable()
                ->after('total_cost')
                
                ;
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_records', function (Blueprint $table) {
            $table->dropColumn('vendor_id');
        });

        Schema::table('maintenance_records', function (Blueprint $table) {
            $table->unsignedBigInteger('vendor_id')->nullable()->after('total_cost');
        });
    }
};
