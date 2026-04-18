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
        Schema::table('admin_broadcasts', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            $table->index(['company_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_broadcasts', function (Blueprint $table) {
            $table->dropForeign(['admin_broadcasts_company_id_foreign']);
            $table->dropIndex(['company_id', 'is_active']);
            $table->dropColumn('company_id');
        });
    }
};
