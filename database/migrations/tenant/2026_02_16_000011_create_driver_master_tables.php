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
        // Driver Profiles (Extension of User)
        Schema::create('driver_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('branch_id')->nullable();

            // HR / Employment
            $table->string('employment_type')->default('full_time'); // full_time, part_time, vendor
            $table->string('salary_type')->default('monthly'); // monthly, trip_based
            $table->decimal('base_salary', 10, 2)->nullable();

            // Status override specific to driver duties
            $table->string('status')->default('available'); // available, on_trip, leave, suspended

            $table->timestamps();
            $table->unique('user_id');
        });

        // Driver Documents (Compliance)
        Schema::create('driver_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_profile_id');
            $table->string('document_type'); // license, visa, id_proof, rta_card
            $table->string('document_number')->nullable();
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('file_path')->nullable(); // Uploaded file
            $table->boolean('is_verified')->default(false);
            $table->timestamps();

            $table->index('expiry_date'); // for alerts
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_documents');
        Schema::dropIfExists('driver_profiles');
    }
};
