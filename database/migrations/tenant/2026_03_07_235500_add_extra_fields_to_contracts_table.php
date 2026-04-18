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
        Schema::table('contracts', function (Blueprint $table) {
            $table->time('start_time')->nullable()->after('start_date');
            $table->time('end_time')->nullable()->after('end_date');
            $table->decimal('extra_charges', 15, 2)->default(0)->after('monthly_rate');
            $table->decimal('discount', 15, 2)->default(0)->after('extra_charges');
            $table->text('payment_terms')->nullable()->after('auto_renew');
            $table->date('payment_due_date')->nullable()->after('payment_terms');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn([
                'start_time',
                'end_time',
                'extra_charges',
                'discount',
                'payment_terms',
                'payment_due_date'
            ]);
        });
    }
};
