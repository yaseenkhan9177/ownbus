<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'tenant';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::connection('tenant')->hasColumn('rentals', 'pdf_path')) {
            Schema::connection('tenant')->table('rentals', function (Blueprint $table) {
                $table->string('pdf_path')->nullable()->after('notes');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('tenant')->table('rentals', function (Blueprint $table) {
            $table->dropColumn('pdf_path');
        });
    }
};
