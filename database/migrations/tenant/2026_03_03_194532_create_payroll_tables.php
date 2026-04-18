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
        // 1. Payroll Batches (Grouping by month/period)
        Schema::create('payroll_batches', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('branch_id')->nullable();
            $table->string('period_name'); // e.g., "February 2026"
            $table->string('status')->default('draft'); // draft, posted, paid
            $table->decimal('total_net', 15, 2)->default(0);
            $table->foreignId('created_by');
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Salary Slips (Individual records per employee)
        Schema::create('salary_slips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_batch_id');
            $table->foreignId('user_id');
            $table->decimal('base_salary', 15, 2)->default(0);
            $table->decimal('total_additions', 15, 2)->default(0);
            $table->decimal('total_deductions', 15, 2)->default(0);
            $table->decimal('net_salary', 15, 2)->default(0);
            $table->string('status')->default('pending'); // pending, paid
            $table->timestamps();
        });

        // 3. Salary Items (Breakdown of additions/deductions)
        Schema::create('salary_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salary_slip_id');
            $table->enum('type', ['addition', 'deduction']);
            $table->string('label'); // e.g., "Performance Bonus", "Traffic Fine"
            $table->decimal('amount', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_items');
        Schema::dropIfExists('salary_slips');
        Schema::dropIfExists('payroll_batches');
    }
};
