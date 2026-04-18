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
        Schema::table('companies', function (Blueprint $table) {
            $table->text('address')->nullable()->after('phone');
            $table->string('currency', 3)->default('AED')->after('address');
            $table->decimal('tax_rate', 5, 2)->default(0)->after('currency');
            $table->string('invoice_prefix')->default('INV-')->after('tax_rate');
            $table->string('logo_url')->nullable()->after('logo_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['address', 'currency', 'tax_rate', 'invoice_prefix', 'logo_url']);
        });
    }
};
