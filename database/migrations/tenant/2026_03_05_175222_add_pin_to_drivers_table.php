<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->string('pin', 4)->nullable()->after('phone');   // 4-digit login PIN (stored hashed)
            $table->string('pin_hash', 255)->nullable()->after('pin'); // bcrypt hash of PIN
            $table->timestamp('last_login_at')->nullable()->after('pin_hash');
        });
    }

    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropColumn(['pin', 'pin_hash', 'last_login_at']);
        });
    }
};
