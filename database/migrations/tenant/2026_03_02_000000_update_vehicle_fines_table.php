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
        Schema::table('vehicle_fines', function (Blueprint $table) {
            if (!Schema::hasColumn('vehicle_fines', 'responsible_type')) {
                $table->enum('responsible_type', ['driver', 'customer', 'both', 'company'])->default('driver')->after('customer_id');
            }
            if (!Schema::hasColumn('vehicle_fines', 'rental_id')) {
                $table->foreignId('rental_id')->nullable()->after('customer_id');
            }
            if (!Schema::hasColumn('vehicle_fines', 'attachment_path')) {
                $table->string('attachment_path')->nullable()->after('description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_fines', function (Blueprint $table) {
            $table->dropColumn(['responsible_type', 'rental_id', 'attachment_path']);
        });
    }
};
