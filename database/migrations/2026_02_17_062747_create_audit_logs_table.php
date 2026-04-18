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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');


            // Action details
            $table->string('action'); // e.g., 'rental_created', 'pricing_updated', 'subscription_changed'
            $table->string('entity_type')->nullable(); // e.g., 'Rental', 'DynamicPricingRule', 'Subscription'
            $table->unsignedBigInteger('entity_id')->nullable(); // ID of the affected entity

            // Change tracking
            $table->json('old_values')->nullable(); // Previous state
            $table->json('new_values')->nullable(); // New state
            $table->json('metadata')->nullable(); // Additional context

            // Request tracking
            $table->string('ip_address', 45)->nullable(); // IPv4 or IPv6
            $table->string('user_agent')->nullable();
            $table->string('url')->nullable();
            $table->string('http_method', 10)->nullable(); // GET, POST, PUT, DELETE

            $table->timestamps();

            // Indexes for performance
            $table->index('user_id');
            $table->index('action');
            $table->index('entity_type');
            $table->index(['entity_type', 'entity_id']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
