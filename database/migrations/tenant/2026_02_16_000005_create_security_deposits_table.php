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
        Schema::create('security_deposits', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('rental_id');
            $table->decimal('amount', 15, 2);
            $table->date('received_date');
            $table->date('refund_date')->nullable();
            $table->string('status')->default('held'); // held, refunded, deducted
            $table->string('transaction_ref')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_deposits');
    }
};
