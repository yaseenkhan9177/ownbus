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
        Schema::create('company_notification_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('whatsapp_number')->nullable();
            $table->boolean('whatsapp_enabled')->default(false);
            $table->boolean('notify_new_rental')->default(true);
            $table->boolean('notify_rental_expiring')->default(true);
            $table->boolean('notify_payment')->default(true);
            $table->boolean('notify_new_fine')->default(true);
            $table->boolean('notify_document_expiring')->default(true);
            $table->boolean('notify_maintenance')->default(true);
            $table->boolean('notify_driver_license')->default(true);
            $table->boolean('notify_subscription')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_notification_settings');
    }
};
