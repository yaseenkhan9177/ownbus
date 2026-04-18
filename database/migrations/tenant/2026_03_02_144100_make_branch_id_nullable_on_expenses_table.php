<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            // Drop the non-nullable FK first, then re-add it as nullable.
            // branch_id should be optional — some companies are single-branch
            // or use vehicles/expenses without a branch assignment.
            $table->foreignId('branch_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable(false)->change();
        });
    }
};
