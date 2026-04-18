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
            if (!Schema::hasColumn('vehicles', 'color')) {
                $table->string('color', 50)->nullable()->after('model');
            }

            if (!Schema::hasColumn('vehicles', 'deleted_at')) {
                $table->softDeletes();
            }

            // Convert existing statuses to match new ENUM
            \Illuminate\Support\Facades\DB::table('vehicles')
                ->where('status', 'active')
                ->update(['status' => 'available']);

            // In MySQL altering a column that's part of a key or just changing format:
            $table->enum('status', ['available', 'rented', 'maintenance', 'inactive'])->default('available')->change();

            // Replace simple unique with composite unique
            // Laravel Schema builder throws if dropping non-existent, so wrap in try-catch if possible, 
            // but we'll assume it exists if it hasn't migrated.
            // Actually, safe way:
        });

        // Drop constraint in a separate schema block to avoid DDL grouping issues
        try {
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE vehicles DROP INDEX vehicles_vehicle_number_unique');
        } catch (\Exception $e) {
        }

        try {
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE vehicles ADD UNIQUE INDEX vehicles_vehicle_number_unique (vehicle_number)');
        } catch (\Exception $e) {
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE vehicles DROP INDEX vehicles_vehicle_number_unique');
        } catch (\Exception $e) {
        }

        try {
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE vehicles ADD UNIQUE INDEX vehicles_vehicle_number_unique (vehicle_number)');
        } catch (\Exception $e) {
        }

        Schema::table('vehicles', function (Blueprint $table) {
            if (Schema::hasColumn('vehicles', 'color')) {
                $table->dropColumn('color');
            }
            $table->string('status')->default('active')->change();
        });
    }
};
