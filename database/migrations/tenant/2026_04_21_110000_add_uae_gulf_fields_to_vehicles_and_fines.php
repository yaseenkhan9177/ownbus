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
            $table->string('registration_emirate')->nullable()->after('status');
            $table->string('registration_code')->nullable()->after('registration_emirate');
            $table->string('plate_number')->nullable()->after('registration_code');
            $table->string('plate_category')->nullable()->after('plate_number');
            $table->string('vehicle_code')->nullable()->unique()->after('plate_category');
        });

        Schema::table('vehicle_fines', function (Blueprint $table) {
            $table->timestamp('last_checked_at')->nullable()->after('status');
            $table->string('external_reference')->nullable()->after('last_checked_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_fines', function (Blueprint $table) {
            $table->dropColumn(['last_checked_at', 'external_reference']);
        });

        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn([
                'registration_emirate',
                'registration_code',
                'plate_number',
                'plate_category',
                'vehicle_code'
            ]);
        });
    }
};
