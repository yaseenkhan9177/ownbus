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
        Schema::table('rentals', function (Blueprint $table) {
            // Renames
            if (Schema::hasColumn('rentals', 'contract_number') && !Schema::hasColumn('rentals', 'rental_number')) {
                $table->renameColumn('contract_number', 'rental_number');
            }

            if (Schema::hasColumn('rentals', 'bus_id')) {
                // Skip dropForeign since SHOW CREATE TABLE didn't show a formal constraint
                $table->renameColumn('bus_id', 'vehicle_id');
            }

            if (Schema::hasColumn('rentals', 'start_datetime')) {
                $table->renameColumn('start_datetime', 'start_date');
            }
            if (Schema::hasColumn('rentals', 'end_datetime')) {
                $table->renameColumn('end_datetime', 'end_date');
            }
            if (Schema::hasColumn('rentals', 'actual_end_datetime')) {
                $table->renameColumn('actual_end_datetime', 'actual_return_date');
            }
            if (Schema::hasColumn('rentals', 'grand_total')) {
                $table->renameColumn('grand_total', 'final_amount');
            }
            if (Schema::hasColumn('rentals', 'tax_amount')) {
                $table->renameColumn('tax_amount', 'tax');
            }

            // Additions
            if (!Schema::hasColumn('rentals', 'rate_type')) {
                $table->string('rate_type')->after('rental_type')->default('daily'); // daily, weekly, monthly
            }
            if (!Schema::hasColumn('rentals', 'rate_amount')) {
                $table->decimal('rate_amount', 15, 2)->after('rate_type')->default(0);
            }
            if (!Schema::hasColumn('rentals', 'discount')) {
                $table->decimal('discount', 15, 2)->after('final_amount')->default(0);
            }
            if (!Schema::hasColumn('rentals', 'security_deposit')) {
                $table->decimal('security_deposit', 15, 2)->after('discount')->default(0);
            }
            if (!Schema::hasColumn('rentals', 'notes')) {
                $table->text('notes')->nullable()->after('security_deposit');
            }
            if (!Schema::hasColumn('rentals', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('notes');
            }

            // Re-add Foreign key for vehicle_id
            if (Schema::hasColumn('rentals', 'vehicle_id')) {
                }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            if (Schema::hasColumn('rentals', 'created_by')) {
                try {
                    } catch (\Exception $e) {
                }
            }
            if (Schema::hasColumn('rentals', 'vehicle_id')) {
                try {
                    } catch (\Exception $e) {
                }
                $table->renameColumn('vehicle_id', 'bus_id');
            }

            if (Schema::hasColumn('rentals', 'rental_number') && !Schema::hasColumn('rentals', 'contract_number')) {
                $table->renameColumn('rental_number', 'contract_number');
            }
            if (Schema::hasColumn('rentals', 'start_date')) {
                $table->renameColumn('start_date', 'start_datetime');
            }
            if (Schema::hasColumn('rentals', 'end_date')) {
                $table->renameColumn('end_date', 'end_datetime');
            }
            if (Schema::hasColumn('rentals', 'actual_return_date')) {
                $table->renameColumn('actual_return_date', 'actual_end_datetime');
            }
            if (Schema::hasColumn('rentals', 'final_amount')) {
                $table->renameColumn('final_amount', 'grand_total');
            }
            if (Schema::hasColumn('rentals', 'tax')) {
                $table->renameColumn('tax', 'tax_amount');
            }

            $colsToDrop = [];
            foreach (['rate_type', 'rate_amount', 'discount', 'security_deposit', 'notes', 'created_by'] as $col) {
                if (Schema::hasColumn('rentals', $col)) {
                    $colsToDrop[] = $col;
                }
            }
            if (!empty($colsToDrop)) {
                $table->dropColumn($colsToDrop);
            }
        });
    }
};
