<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Rename financial_transactions to journal_entries (Header)
        if (Schema::hasTable('financial_transactions') && !Schema::hasTable('journal_entries_new')) {
            Schema::rename('financial_transactions', 'journal_entries_new');
        }

        // 2. Rename old journal_entries to journal_entry_lines (Details)
        // Wait, 'journal_entries' currently exists as the lines table.
        // We need to move it to 'journal_entry_lines'.
        if (Schema::hasTable('journal_entries') && !Schema::hasTable('journal_entry_lines')) {
            Schema::rename('journal_entries', 'journal_entry_lines');
        }

        // Now rename the placeholder back to intended name
        if (Schema::hasTable('journal_entries_new')) {
            Schema::rename('journal_entries_new', 'journal_entries');
        }

        // 3. Update journal_entries (Header) with control columns
        Schema::table('journal_entries', function (Blueprint $table) {
            if (!Schema::hasColumn('journal_entries', 'is_posted')) {
                $table->boolean('is_posted')->default(false)->after('description');
            }
            if (!Schema::hasColumn('journal_entries', 'posted_at')) {
                $table->timestamp('posted_at')->nullable()->after('is_posted');
            }
            if (!Schema::hasColumn('journal_entries', 'created_by')) {
                $table->foreignId('created_by')->nullable();
            }
            if (!Schema::hasColumn('journal_entries', 'date')) {
                // If it was renamed from financial_transactions, it likely has transaction_date
                if (Schema::hasColumn('journal_entries', 'transaction_date')) {
                    $table->renameColumn('transaction_date', 'date');
                } else {
                    $table->date('date')->after('id')->nullable();
                }
            }

            // Indexes for reporting performance
            $table->index(['date']);
            $table->index(['reference_type', 'reference_id']);
        });

        // 4. Update journal_entry_lines (Details)
        Schema::table('journal_entry_lines', function (Blueprint $table) {
            // Ensure foreign key to journal_entries (header) is correct
            // It was probably transaction_id
            if (Schema::hasColumn('journal_entry_lines', 'transaction_id')) {
                $table->renameColumn('transaction_id', 'journal_entry_id');
            }
        });

        // 5. Create accounting_periods table
        Schema::create('accounting_periods', function (Blueprint $table) {
            $table->id();

            $table->string('name'); // e.g., "Feb 2026"
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_closed')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounting_periods');

        // Note: Reversing renames is complex in a generic script, 
        // but for safety we'll at least restore table names if needed.
        if (Schema::hasTable('journal_entries') && !Schema::hasTable('financial_transactions')) {
            Schema::rename('journal_entries', 'financial_transactions');
        }
        if (Schema::hasTable('journal_entry_lines') && !Schema::hasTable('journal_entries')) {
            Schema::rename('journal_entry_lines', 'journal_entries');
        }
    }
};
