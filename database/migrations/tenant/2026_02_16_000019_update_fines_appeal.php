<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fines', function (Blueprint $table) {
            if (!Schema::hasColumn('fines', 'appeal_status')) {
                // Appeal Workflow
                $table->string('appeal_status')->nullable()->after('status'); // pending, approved, rejected
                $table->text('appeal_reason')->nullable()->after('appeal_status');
                $table->date('appeal_date')->nullable()->after('appeal_reason');
                $table->string('appeal_reference')->nullable()->after('appeal_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('fines', function (Blueprint $table) {
            $table->dropColumn(['appeal_status', 'appeal_reason', 'appeal_date', 'appeal_reference']);
        });
    }
};
