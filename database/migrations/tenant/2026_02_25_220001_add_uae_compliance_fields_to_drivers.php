<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            if (!Schema::hasColumn('drivers', 'rta_permit_expiry')) {
                $table->date('rta_permit_expiry')->nullable()->after('license_expiry_date')
                    ->comment('UAE RTA Road Transport Authority permit expiry');
            }
            if (!Schema::hasColumn('drivers', 'visa_expiry')) {
                $table->date('visa_expiry')->nullable()->after('rta_permit_expiry')
                    ->comment('UAE Residence visa expiry date');
            }
            if (!Schema::hasColumn('drivers', 'emirates_id_expiry')) {
                $table->date('emirates_id_expiry')->nullable()->after('visa_expiry')
                    ->comment('UAE Emirates ID card expiry date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropColumn(['rta_permit_expiry', 'visa_expiry', 'emirates_id_expiry']);
        });
    }
};
