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
        // Drop old tables if they exist (cleanup v2.1)
        Schema::dropIfExists('journal_entry_items');
        Schema::dropIfExists('journal_entries');
        Schema::dropIfExists('ledgers');

        // 1. Chart of Accounts
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('parent_id')->nullable();
            $table->string('account_code')->nullable();
            $table->string('account_name');
            $table->enum('account_type', ['asset', 'liability', 'equity', 'income', 'expense']);
            $table->boolean('is_system')->default(false); // If true, cannot be deleted (e.g., Accounts Receivable)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['account_code']);
        });

        // 2. Financial Transactions (Header)
        Schema::create('financial_transactions', function (Blueprint $table) {
            $table->id();

            $table->string('reference_type')->nullable(); // Rental, Fine, Payment
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->date('transaction_date');
            $table->string('description')->nullable();
            $table->foreignId('branch_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['reference_type', 'reference_id']);
        });

        // 3. Journal Entries (Lines) - Reusing name 'journal_entries' but new structure
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id');
            $table->foreignId('account_id');
            $table->foreignId('branch_id')->nullable();
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
        Schema::dropIfExists('financial_transactions');
        Schema::dropIfExists('accounts');
    }
};
