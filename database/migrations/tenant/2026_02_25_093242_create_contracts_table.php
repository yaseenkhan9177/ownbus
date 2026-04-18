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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();

            
            $table->foreignId('branch_id')->nullable();

            $table->foreignId('customer_id');
            $table->foreignId('vehicle_id');
            $table->foreignId('driver_id')->nullable();

            $table->string('contract_number')->unique();

            $table->date('start_date');
            $table->date('end_date');

            $table->decimal('contract_value', 15, 2);
            $table->decimal('monthly_rate', 15, 2)->nullable();

            $table->enum('billing_cycle', ['monthly', 'quarterly', 'yearly', 'custom'])
                ->default('monthly');

            $table->enum('status', [
                'draft',
                'active',
                'expired',
                'terminated',
                'completed'
            ])->default('draft');

            $table->boolean('auto_renew')->default(false);

            $table->text('terms')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['status']);
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
