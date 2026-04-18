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
        Schema::table('audit_logs', function (Blueprint $table) {
            // Add company_id if not exists (important for SaaS)
            if (!Schema::hasColumn('audit_logs', 'company_id')) {
                $table->foreignId('company_id')->nullable()->after('user_id')->constrained('companies')->onDelete('cascade');
            }

            // Rename columns to exactly match user's enterprise spec
            $table->renameColumn('entity_type', 'module');
            $table->renameColumn('entity_id', 'reference_id');
            $table->renameColumn('old_values', 'old_data');
            $table->renameColumn('new_values', 'new_data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            if (Schema::hasColumn('audit_logs', 'company_id')) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            }

            $table->renameColumn('module', 'entity_type');
            $table->renameColumn('reference_id', 'entity_id');
            $table->renameColumn('old_data', 'old_values');
            $table->renameColumn('new_data', 'new_values');
        });
    }
};
