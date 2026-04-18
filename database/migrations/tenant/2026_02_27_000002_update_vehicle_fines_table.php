<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicle_fines', function (Blueprint $table) {
            $table->string('fine_type')->nullable()->after('authority'); // speed, parking, signal jump, etc.
            $table->renameColumn('authority', 'source'); // Abu Dhabi Police, RTA, etc.
            $table->integer('black_points')->default(0)->after('amount');
            $table->timestamp('paid_at')->nullable()->after('status');
            $table->string('payment_reference')->nullable()->after('paid_at');
            $table->foreignId('created_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('vehicle_fines', function (Blueprint $table) {
            $table->renameColumn('source', 'authority');
            $table->dropColumn(['fine_type', 'black_points', 'paid_at', 'payment_reference']);
            });
    }
};
