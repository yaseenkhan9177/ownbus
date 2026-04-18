<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Add performance indexes required for financial report queries.
     * These are critical once a company accumulates thousands of journal entries.
     *
     * Note: Uses try/catch per index to be idempotent across MySQL and SQLite.
     */
    public function up(): void
    {
        Schema::table('journal_entries', function (Blueprint $table) {
            try {
                $table->index(
                    ['branch_id', 'date', 'is_posted'],
                    'je_company_branch_date_posted'
                );
            } catch (\Exception $e) {
                // Index already exists — safe to ignore on re-run
            }
        });

        Schema::table('journal_entry_lines', function (Blueprint $table) {
            try {
                $table->index(
                    ['account_id', 'journal_entry_id'],
                    'jel_account_entry'
                );
            } catch (\Exception $e) {
                // Index already exists — safe to ignore on re-run
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journal_entries', function (Blueprint $table) {
            $table->dropIndex('je_company_branch_date_posted');
        });

        Schema::table('journal_entry_lines', function (Blueprint $table) {
            $table->dropIndex('jel_account_entry');
        });
    }
};
