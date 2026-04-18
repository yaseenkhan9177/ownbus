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
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('branch_id')->nullable();
            $table->string('driver_code');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('national_id'); // CNIC / Passport
            $table->string('license_number');
            $table->date('license_expiry_date');
            $table->string('license_type'); // light, heavy, bus, etc.
            $table->date('hire_date');
            $table->decimal('salary', 15, 2)->nullable();
            $table->decimal('commission_rate', 5, 2)->nullable();
            $table->enum('status', ['active', 'suspended', 'inactive'])->default('active');
            $table->text('address');
            $table->string('city');
            $table->string('emergency_contact_name');
            $table->string('emergency_contact_phone');
            $table->text('notes')->nullable();
            $table->foreignId('created_by');
            $table->timestamps();
            $table->softDeletes();

            // Indexes

            $table->index('status');
            $table->index('license_expiry_date');

            // Composite Uniques
            $table->unique(['driver_code']);
            $table->unique(['license_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
