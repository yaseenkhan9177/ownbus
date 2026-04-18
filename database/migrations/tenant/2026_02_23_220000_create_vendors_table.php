<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();

            $table->foreignId('branch_id')->nullable();

            $table->string('vendor_code');
            $table->string('name');
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable(); // NOT unique — vendors may share emails
            $table->string('tax_number')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();

            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->enum('balance_direction', ['payable', 'receivable'])->nullable();

            $table->enum('status', ['active', 'suspended'])->default('active');

            $table->foreignId('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('status');

            // Business Rule: vendor_code must be unique per company
            $table->unique(['vendor_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
