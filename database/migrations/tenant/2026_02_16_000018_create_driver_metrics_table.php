 <?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('driver_performance_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id'); // Driver User ID
            $table->string('month_year'); // YYYY-MM

            $table->integer('trips_completed')->default(0);
            $table->decimal('total_km_driven', 10, 2)->default(0);
            $table->decimal('total_hours_driven', 10, 2)->default(0);
            $table->integer('fines_count')->default(0);
            $table->integer('safety_incidents')->default(0); // Harsh braking, speeding from telematics
            $table->decimal('safety_score', 5, 2)->default(100.00); // Start at 100, deduct points

            $table->unique(['user_id', 'month_year']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_performance_metrics');
    }
};
