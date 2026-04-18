<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_bill_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_bill_id');
            $table->foreignId('expense_account_id')->restrictOnDelete();

            $table->string('description');
            $table->decimal('quantity', 10, 2)->default(1.00);
            $table->decimal('unit_cost', 15, 2)->default(0.00);
            $table->decimal('total_cost', 15, 2)->default(0.00);

            $table->timestamps();

            $table->index('vendor_bill_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_bill_items');
    }
};
