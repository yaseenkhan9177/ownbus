<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            if (!Schema::hasColumn('accounts', 'vat_applicable')) {
                $table->boolean('vat_applicable')->default(false)->after('is_active')
                    ->comment('Whether UAE 5% VAT applies to this account');
            }
            if (!Schema::hasColumn('accounts', 'vat_rate')) {
                $table->decimal('vat_rate', 5, 2)->default(5.00)->after('vat_applicable')
                    ->comment('VAT rate percentage (UAE standard is 5%)');
            }
        });
    }

    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn(['vat_applicable', 'vat_rate']);
        });
    }
};
