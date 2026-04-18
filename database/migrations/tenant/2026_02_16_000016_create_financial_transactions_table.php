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
        // 1. Create financial_transactions table if it doesn't exist
        if (!Schema::hasTable('financial_transactions')) {
            Schema::create('financial_transactions', function (Blueprint $table) {
                $table->id();
                

                // Polymorphic reference to the source (e.g., Rental, Fine)
                $table->nullableMorphs('reference');

                $table->date('transaction_date');
                $table->string('description')->nullable();

                // Adding branch_id directly here as per Phase 4 requirement
                $table->foreignId('branch_id')->nullable();

                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 2. Update journal_entries to link to financial_transactions
        if (Schema::hasTable('journal_entries')) {
            Schema::table('journal_entries', function (Blueprint $table) {
                if (!Schema::hasColumn('journal_entries', 'transaction_id')) {
                    $table->foreignId('transaction_id')->nullable()->after('id');
                }

                // Existing columns 'reference_id' and 'reference_type' on journal_entries might be redundant if we use financial_transactions.
                // We'll keep them for backward compatibility if needed, or drop them later.
                // For now, let's assume new logic uses transaction_id.
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('journal_entries')) {
            Schema::table('journal_entries', function (Blueprint $table) {
                $table->dropColumn('transaction_id');
            });
        }

        Schema::dropIfExists('financial_transactions');
    }
};
