<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'is_credit_blocked')) {
                $table->boolean('is_credit_blocked')->default(false)->after('credit_limit')
                    ->comment('When true, no new rentals can be created for this customer');
            }
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('is_credit_blocked');
        });
    }
};
