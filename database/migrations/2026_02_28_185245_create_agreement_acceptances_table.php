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
        Schema::create('agreement_acceptances', function (Blueprint $table) {
            $table->id();
            
            $table->string('version');
            $table->foreign('version')->references('version')->on('agreement_versions')->onDelete('cascade');
            $table->foreignId('signed_by')->constrained('users')->onDelete('cascade');
            $table->string('ip_address')->nullable();
            $table->string('content_hash');
            $table->timestamp('signed_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agreement_acceptances');
    }
};
