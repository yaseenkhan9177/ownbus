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
        Schema::table('rentals', function (Blueprint $table) {
            if (!Schema::hasColumn('rentals', 'contract_no')) {
                $table->string('contract_no')->nullable()->unique()->after('rental_number');
            }
        });

        Schema::create('rental_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rental_id');
            $table->decimal('amount', 15, 2);
            $table->string('method'); // Cash, Bank, Partial
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_payments');
        Schema::table('rentals', function (Blueprint $table) {
            $table->dropColumn('contract_no');
        });
    }
};
