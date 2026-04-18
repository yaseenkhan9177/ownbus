<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds cash_flow_category to accounts table for enterprise Cash Flow Statement classification.
     * Values: operating | investing | financing | none
     */
    public function up(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->enum('cash_flow_category', ['operating', 'investing', 'financing', 'none'])
                ->default('none')
                ->after('account_type')
                ->comment('Used by Cash Flow Statement to classify account activity');
        });

        // Seed sensible defaults for existing COA accounts based on account type
        // Income & Expense → operating
        DB::table('accounts')->whereIn('account_type', ['income', 'expense'])->update(['cash_flow_category' => 'operating']);

        // Assets (current): cash/bank/AR → operating  — fixed assets → investing
        // We classify by account_code prefix:
        //   10xx = current assets (cash, bank, AR)    → operating
        //   15xx = fixed/non-current assets (vehicles) → investing
        DB::table('accounts')
            ->where('account_type', 'asset')
            ->where('account_code', 'like', '10%')
            ->update(['cash_flow_category' => 'operating']);

        DB::table('accounts')
            ->where('account_type', 'asset')
            ->where('account_code', 'like', '15%')
            ->update(['cash_flow_category' => 'investing']);

        // Liabilities: AP & accruals → operating; Loans → financing
        DB::table('accounts')
            ->where('account_type', 'liability')
            ->where('account_code', 'like', '20%')
            ->update(['cash_flow_category' => 'operating']);

        DB::table('accounts')
            ->where('account_type', 'liability')
            ->where('account_code', 'like', '21%')
            ->update(['cash_flow_category' => 'financing']);

        // Equity → financing (owner investments, retained earnings)
        DB::table('accounts')
            ->where('account_type', 'equity')
            ->update(['cash_flow_category' => 'financing']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn('cash_flow_category');
        });
    }
};
