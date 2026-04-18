<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = ['vehicles', 'rentals', 'customers', 'contracts', 'contract_invoices'];
        foreach ($tables as $t) {
            Schema::table($t, function (Blueprint $table) use ($t) {
                if (!Schema::hasColumn($t, 'deleted_at')) {
                    $table->softDeletes();
                }
            });
        }
    }

    public function down(): void
    {
        $tables = ['vehicles', 'rentals', 'customers', 'contracts', 'contract_invoices'];
        foreach ($tables as $t) {
            Schema::table($t, function (Blueprint $table) use ($t) {
                if (Schema::hasColumn($t, 'deleted_at')) {
                    $table->dropSoftDeletes();
                }
            });
        }
    }
};
