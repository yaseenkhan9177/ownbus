<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $permissions = [
            ['name' => 'close_accounting_period', 'group' => 'Accounting', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'reverse_journal', 'group' => 'Accounting', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'view_financial_reports', 'group' => 'Accounting', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'approve_vendor_bill', 'group' => 'Accounting', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'record_payment', 'group' => 'Accounting', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('permissions')->insertOrIgnore($permissions);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('permissions')->whereIn('name', [
            'close_accounting_period',
            'reverse_journal',
            'view_financial_reports',
            'approve_vendor_bill',
            'record_payment',
        ])->delete();
    }
};
