<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('branch_id');
            $table->foreignId('vehicle_id')->nullable();
            $table->foreignId('vendor_id')->nullable();

            $table->string('category'); // fuel, repair, maintenance, salary, rent, salik, parking, insurance, utility, other
            $table->string('description')->nullable();

            $table->decimal('amount_ex_vat', 12, 2);
            $table->decimal('vat_percent', 5, 2)->default(5.00);
            $table->decimal('vat_amount', 12, 2);
            $table->decimal('total_amount', 12, 2);

            $table->date('expense_date');
            $table->string('payment_method'); // cash, bank, credit_card, payable
            $table->string('reference_no')->nullable();
            $table->string('invoice_path')->nullable();

            $table->foreignId('created_by')->nullable();
            $table->timestamps();

            $table->index(['category']);
            $table->index(['vehicle_id', 'expense_date']);
            $table->index(['branch_id', 'expense_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
