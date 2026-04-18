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
        Schema::create('branch_benchmark_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id');
            $table->integer('score');
            $table->json('breakdown_json');
            $table->timestamp('calculated_at')->useCurrent();

            $table->index(['branch_id', 'calculated_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_benchmark_snapshots');
    }
};
