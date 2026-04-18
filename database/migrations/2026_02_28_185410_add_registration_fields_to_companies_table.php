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
        Schema::table('companies', function (Blueprint $table) {
            $table->string('trade_license_number')->nullable()->after('trn_number');
            $table->integer('total_vehicles')->default(0)->after('trade_license_number');
            $table->string('country')->nullable();
            $table->string('registration_source')->nullable()->after('country');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'trade_license_number',
                'total_vehicles',
                'country',
                'registration_source',
            ]);
        });
    }
};
