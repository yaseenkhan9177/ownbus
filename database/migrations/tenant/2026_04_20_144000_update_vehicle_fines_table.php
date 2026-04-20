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
            // Unify authority and violation type
            if (!Schema::hasColumn('vehicle_fines', 'fine_type')) {
                $table->string('fine_type')->nullable()->after('authority');
            }
            
            // Modify status to include new types. 
            // Using string instead of enum for better flexibility on Windows/MySQL environments during dev
            $table->string('status')->default('unpaid')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_fines', function (Blueprint $table) {
            $table->enum('status', ['pending', 'paid', 'disputed', 'cancelled'])->default('pending')->change();
        });
    }
};
