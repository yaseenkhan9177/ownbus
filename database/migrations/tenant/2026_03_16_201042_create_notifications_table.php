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
        Schema::create('user_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index(); // No strict FK as users table is in central DB
            $table->string('type'); // e.g., 'fine', 'expiry', 'geofence'
            $table->string('title');
            $table->text('message');
            $table->nullableMorphs('notifiable'); // Link to Fine, Vehicle, Rental etc.
            $table->boolean('is_read')->default(false);
            $table->enum('urgency', ['info', 'warning', 'critical'])->default('info');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_notifications');
    }
};
