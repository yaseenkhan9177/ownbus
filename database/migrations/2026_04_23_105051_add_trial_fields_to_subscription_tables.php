<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'trial_ends_at')) {
                $table->timestamp('trial_ends_at')->nullable();
            }
            if (!Schema::hasColumn('companies', 'subscription_status')) {
                $table->string('subscription_status')->default('trial');
            }
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('subscriptions', 'trial_starts_at')) {
                $table->timestamp('trial_starts_at')->nullable();
            }
            if (!Schema::hasColumn('subscriptions', 'trial_used')) {
                $table->boolean('trial_used')->default(false);
            }
        });

        // Add 'trial' and 'expired' to existing enum 'status' in 'subscriptions' table
        DB::statement("ALTER TABLE subscriptions MODIFY COLUMN status ENUM('trialing', 'active', 'past_due', 'grace', 'canceled', 'suspended', 'incomplete', 'trial', 'expired') DEFAULT 'trial'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['trial_ends_at', 'subscription_status']);
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn(['trial_starts_at', 'trial_used']);
        });

        // Optional: Revert the ENUM
        DB::statement("ALTER TABLE subscriptions MODIFY COLUMN status ENUM('trialing', 'active', 'past_due', 'grace', 'canceled', 'suspended', 'incomplete') DEFAULT 'trialing'");
    }
};
