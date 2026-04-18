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
        Schema::create('global_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->comment('The programmatic identifier');
            $table->text('value')->nullable()->comment('The stored configuration payload');
            $table->string('type')->default('string')->comment('string, boolean, integer, json, text');
            $table->string('group')->default('general')->comment('UI grouping logic (general, mail, security)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('global_settings');
    }
};
