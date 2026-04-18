<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_bills', function (Blueprint $table) {
            $table->id();

            $table->foreignId('branch_id')->nullable();
            $table->foreignId('vendor_id');

            $table->string('bill_number');
            $table->date('bill_date');
            $table->date('due_date')->nullable();

            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->nullable();

            $table->enum('status', ['draft', 'approved', 'partially_paid', 'paid', 'cancelled'])
                ->default('draft');

            $table->text('description')->nullable();

            $table->foreignId('created_by')->nullable();
            $table->foreignId('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('bill_date');
            $table->index('status');

            // Bill number unique per company
            $table->unique(['bill_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_bills');
    }
};
