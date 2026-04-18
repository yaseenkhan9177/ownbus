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
        Schema::create('event_logs', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('branch_id')->nullable();

            $table->string('event_type'); // rental_created, fine_paid, etc.
            $table->string('severity')->default('info'); // info, warning, critical

            // Polymorphic relationship (Standard: entity_type, entity_id)
            // No direct DB foreign keys to entities to ensure logs persist after deletion
            $table->morphs('entity');

            $table->string('title'); // Human readable
            $table->json('meta')->nullable(); // Extra structured data

            $table->foreignId('performed_by')->nullable();

            $table->timestamp('occurred_at')->useCurrent();
            $table->timestamps();

            $table->index(['occurred_at']);
            $table->index('severity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_logs');
    }
};
