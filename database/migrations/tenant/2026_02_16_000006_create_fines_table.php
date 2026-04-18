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
        Schema::create('fines', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('vehicle_id');
            $table->foreignId('driver_id')->nullable();
            $table->foreignId('rental_id')->nullable(); // Linked if occured during rental

            $table->string('authority'); // Dubai Police, etc.
            $table->string('reference_number')->unique();
            $table->decimal('amount', 15, 2);
            $table->dateTime('fine_datetime');
            $table->string('status')->default('pending'); // pending, paid, re-invoiced

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fines');
    }
};
