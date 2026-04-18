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
        // Add branch_id to accounts (formerly ledgers - assuming refactor done or using accounts table)
        // Wait, Task 1.2 said "Refactor to accounts". Let's check if 'accounts' table exists or if we are still on 'ledgers'.
        // The migration reviewed (003) creates 'ledgers'. 
        // But task 1.2 "Refactor to accounts" was marked done. 
        // I need to check if there is another migration for 'accounts'.
        // If 'accounts' exists, I add to it. If not, I refrain and check.

        // Let's assume the earlier task completion meant the 'accounts' table serves as the core.
        // Actually, looking at GenerateRentalInvoice listener (read previously), it uses 'Account' model. 
        // So 'accounts' table must exist.

        if (Schema::hasTable('accounts')) {
            Schema::table('accounts', function (Blueprint $table) {
                if (!Schema::hasColumn('accounts', 'branch_id')) {
                    $table->foreignId('branch_id')->nullable()->after('id');
                }
            });
        }

        if (Schema::hasTable('journal_entry_items') && !Schema::hasColumn('journal_entry_items', 'branch_id')) {
            Schema::table('journal_entry_items', function (Blueprint $table) {
                $table->foreignId('branch_id')->nullable()->after('id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('financial_transactions')) {
            Schema::table('financial_transactions', function (Blueprint $table) {
                $table->dropColumn('branch_id');
            });
        }

        if (Schema::hasTable('accounts')) {
            Schema::table('accounts', function (Blueprint $table) {
                $table->dropColumn('branch_id');
            });
        }
    }
};
