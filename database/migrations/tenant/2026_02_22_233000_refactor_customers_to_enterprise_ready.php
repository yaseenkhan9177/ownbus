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
        Schema::table('customers', function (Blueprint $table) {
            // Drop old columns if they exist or just rename/update
            // Based on previous research: name, email, phone, address, types, trn_number, company_name, is_blacklisted, blacklist_reason exist.

            // 1. Structural Changes & New Fields
            if (!Schema::hasColumn('customers', 'branch_id')) {
                $table->foreignId('branch_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('customers', 'customer_code')) {
                $table->string('customer_code', 50)->nullable()->after('branch_id');
            }
            if (!Schema::hasColumn('customers', 'type')) {
                $table->string('type', 20)->default('individual')->after('customer_code');
            }

            // Handle existing 'types' to 'type' if needed
            if (Schema::hasColumn('customers', 'types')) {
                // Migration to move data if any, but we checked and count is 0
                $table->dropColumn('types');
            }

            if (!Schema::hasColumn('customers', 'first_name')) {
                $table->string('first_name')->nullable()->after('type');
            }
            if (!Schema::hasColumn('customers', 'last_name')) {
                $table->string('last_name')->nullable()->after('first_name');
            }

            // Cleanup old name if it exists (since we use first/last)
            if (Schema::hasColumn('customers', 'name')) {
                $table->dropColumn('name');
            }

            if (!Schema::hasColumn('customers', 'alternate_phone')) {
                $table->string('alternate_phone')->nullable()->after('phone');
            }

            if (!Schema::hasColumn('customers', 'national_id')) {
                $table->string('national_id')->nullable()->after('alternate_phone');
            }
            if (!Schema::hasColumn('customers', 'driving_license_no')) {
                $table->string('driving_license_no')->nullable()->after('national_id');
            }
            if (!Schema::hasColumn('customers', 'driving_license_expiry')) {
                $table->date('driving_license_expiry')->nullable()->after('driving_license_no');
            }

            if (!Schema::hasColumn('customers', 'city')) {
                $table->string('city')->nullable()->after('address');
            }
            if (!Schema::hasColumn('customers', 'country')) {
                $table->string('country')->nullable()->after('city');
            }

            // 2. Financial Safety (15,2 precision)
            if (!Schema::hasColumn('customers', 'credit_limit')) {
                $table->decimal('credit_limit', 15, 2)->default(0)->after('country');
            } else {
                $table->decimal('credit_limit', 15, 2)->default(0)->change();
            }

            if (!Schema::hasColumn('customers', 'current_balance')) {
                $table->decimal('current_balance', 15, 2)->default(0)->after('credit_limit');
            } else {
                $table->decimal('current_balance', 15, 2)->default(0)->change();
            }

            if (!Schema::hasColumn('customers', 'status')) {
                $table->string('status', 20)->default('active')->after('current_balance');
            }

            // Cleanup old blacklist fields
            if (Schema::hasColumn('customers', 'is_blacklisted')) {
                $table->dropColumn('is_blacklisted');
            }
            if (Schema::hasColumn('customers', 'blacklist_reason')) {
                if (!Schema::hasColumn('customers', 'notes')) {
                    $table->renameColumn('blacklist_reason', 'notes');
                } else {
                    $table->dropColumn('blacklist_reason');
                }
            } elseif (!Schema::hasColumn('customers', 'notes')) {
                $table->text('notes')->nullable()->after('status');
            }

            if (!Schema::hasColumn('customers', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('notes');
            }

            // 3. Performance & Integrity
            $table->unique(['customer_code'], 'idx_customer_company_code');
            // Check if phone needs unique per company
            // $table->unique(['phone'], 'idx_customer_company_phone'); 

            $table->index(['status'], 'idx_customer_company_status');
            $table->index('phone', 'idx_customer_phone');
            $table->index('email', 'idx_customer_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropUnique('idx_customer_company_code');
            $table->dropIndex('idx_customer_company_status');
            $table->dropIndex('idx_customer_phone');
            $table->dropIndex('idx_customer_email');

            $table->dropColumn([
                'branch_id',
                'customer_code',
                'type',
                'first_name',
                'last_name',
                'alternate_phone',
                'national_id',
                'driving_license_no',
                'driving_license_expiry',
                'city',
                'country',
                'credit_limit',
                'current_balance',
                'status',
                'notes',
                'created_by'
            ]);

            // Note: Reverting drops of old pillars (name, types, etc.) is complex without full rollback
            // But since this is a refactor of a clean state, it's acceptable.
        });
    }
};
