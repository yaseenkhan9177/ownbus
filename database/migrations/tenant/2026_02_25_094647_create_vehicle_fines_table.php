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
        Schema::create('vehicle_fines', function (Blueprint $table) {
            $table->id();

            
            $table->foreignId('branch_id')->nullable();

            $table->foreignId('vehicle_id');
            $table->foreignId('driver_id')->nullable();
            $table->foreignId('customer_id')->nullable();

            $table->string('authority'); // Dubai, Abu Dhabi, etc.
            $table->string('fine_number')->nullable();

            $table->date('fine_date');
            $table->date('due_date')->nullable();

            $table->decimal('amount', 12, 2);

            $table->enum('status', [
                'pending',
                'paid',
                'disputed',
                'cancelled'
            ])->default('pending');

            $table->boolean('customer_responsible')->default(true);

            $table->text('description')->nullable();

            $table->timestamps();

            $table->index(['status']);
            $table->index(['vehicle_id', 'fine_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_fines');
    }
};
