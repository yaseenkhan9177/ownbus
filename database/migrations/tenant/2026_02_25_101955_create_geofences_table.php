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
        Schema::create('geofences', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->geometry('area'); // MySQL Spatial Data Type            $table->text('description')->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Standard indices
            // Spatial Index requires MyISAM/InnoDB (MySQL 5.7+), InnoDB supported now
            // Cannot be added directly using simple $table->index in all Laravel versions, 
            // but spatialIndex() is available.
            if (config('database.default') !== 'sqlite' && config('database.default') !== 'sqlite_testing') {
                $table->spatialIndex('area');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('geofences');
    }
};
