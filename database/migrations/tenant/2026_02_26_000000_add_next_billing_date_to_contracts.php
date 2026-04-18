<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 7A — Production billing tracking fields.
 * Adds next_billing_date (canonical field name) alongside existing last_billed_at.
 * If contracts table already has last_billed_at from prior migration, we only add the new column.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            // Canonical billing date — when next invoice is due
            if (!Schema::hasColumn('contracts', 'next_billing_date')) {
                $table->date('next_billing_date')->nullable()->after('last_billed_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            if (Schema::hasColumn('contracts', 'next_billing_date')) {
                $table->dropColumn('next_billing_date');
            }
        });
    }
};
