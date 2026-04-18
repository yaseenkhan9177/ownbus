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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_number')->unique(); // e.g., DXB-101
            $table->string('name'); // We can keep this or map 'make' + 'model' to it
            $table->string('make');
            $table->string('model');
            $table->integer('year');
            $table->string('type'); // 50-Seater Luxury, etc.
            $table->decimal('daily_rate', 10, 2);
            $table->string('status')->default('active'); // active, maintenance
            $table->decimal('purchase_price', 15, 2)->nullable();
            $table->date('purchase_date')->nullable();
            $table->integer('current_odometer')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
