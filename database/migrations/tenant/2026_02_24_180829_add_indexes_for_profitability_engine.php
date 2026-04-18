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
        Schema::table('journal_entries', function (Blueprint $table) {
            $table->index(['reference_type', 'reference_id'], 'idx_journals_reference');
            $table->index(['date', 'is_posted'], 'idx_journals_date_posted');
        });

        Schema::table('journal_entry_lines', function (Blueprint $table) {
            $table->index('account_id', 'idx_journal_lines_account');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journal_entries', function (Blueprint $table) {
            $table->dropIndex('idx_journals_reference');
            $table->dropIndex('idx_journals_date_posted');
        });

        Schema::table('journal_entry_lines', function (Blueprint $table) {
            $table->dropIndex('idx_journal_lines_account');
        });
    }
};
