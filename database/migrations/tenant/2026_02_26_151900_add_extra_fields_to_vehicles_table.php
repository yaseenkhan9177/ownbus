<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            if (!Schema::hasColumn('vehicles', 'seating_capacity')) {
                $table->unsignedSmallInteger('seating_capacity')->nullable()->after('type');
            }
            if (!Schema::hasColumn('vehicles', 'fuel_type')) {
                $table->string('fuel_type', 20)->nullable()->after('seating_capacity');
            }
            if (!Schema::hasColumn('vehicles', 'transmission')) {
                $table->string('transmission', 20)->nullable()->after('fuel_type');
            }
            if (!Schema::hasColumn('vehicles', 'image_path')) {
                $table->string('image_path')->nullable()->after('daily_rate');
            }
            if (!Schema::hasColumn('vehicles', 'notes')) {
                $table->text('notes')->nullable()->after('image_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn(['seating_capacity', 'fuel_type', 'transmission', 'image_path', 'notes']);
        });
    }
};
