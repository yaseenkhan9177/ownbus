<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contract_invoices', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('contract_id');
            $table->foreignId('customer_id');
            $table->foreignId('journal_entry_id')->nullable();

            $table->string('invoice_number')->unique();
            $table->date('period_start');
            $table->date('period_end');
            $table->date('due_date');

            $table->decimal('subtotal', 15, 2);
            $table->decimal('vat_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 15, 2);

            $table->enum('status', ['draft', 'sent', 'paid', 'overdue', 'cancelled'])->default('draft');
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['status']);
            $table->index(['contract_id', 'period_start']);
            $table->index(['customer_id', 'due_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_invoices');
    }
};
