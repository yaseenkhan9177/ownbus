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
        Schema::table('rentals', function (Blueprint $table) {
            $table->uuid('uuid')->unique()->after('id');
            // We already have contract_number, but plan mentions "UUID public IDs". 
            // contract_number is usually human readable (RENT-2024-001). 
            // uuid is for URLs.

            // Add indexes requested in v2.2
            $table->index(['branch_id', 'status']);
            $table->index('start_datetime');

            // Add timestamps for status tracking (optional but good for performant reporting)
            // status_logs has history, but these help quick queries
            // $table->timestamp('confirmed_at')->nullable();
            // $table->timestamp('completed_at')->nullable(); 
            // Let's stick to status_logs for history to avoid column bloat, 
            // but Indexing status is key.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->dropIndex(['branch_id', 'status']);
            $table->dropIndex('start_datetime');
            $table->dropColumn('uuid');
        });
    }
};
