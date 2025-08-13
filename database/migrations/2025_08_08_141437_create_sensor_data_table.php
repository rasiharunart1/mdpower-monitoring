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
        Schema::create('sensor_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained('devices')->onDelete('cascade');

            //batre
            $table->decimal('battery_a', 8, 2);
            $table->decimal('battery_b', 8, 2);
            $table->decimal('battery_c', 8, 2);
            $table->decimal('battery_d', 8, 2);

            //temperature
            $table->decimal('temperature_1', 8, 2);
            $table->decimal('temperature_2', 8, 2);


            //PLN
            $table->decimal('pln_volt', 8, 2);
            $table->decimal('pln_current', 8, 2);
            $table->decimal('pln_watt', 8, 2);

            //relay

            $table->boolean('relay_1')->default(false);
            $table->boolean('relay_2')->default(false);


            $table->timestamps();

            $table->index(['device_id', 'created_at']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensor_data');
    }
};
