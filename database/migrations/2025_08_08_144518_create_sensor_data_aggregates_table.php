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
        Schema::create('sensor_data_aggregates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained('devices')->onDelete('cascade');
            $table->enum('aggregate_type', ['daily', 'weekly', 'monthly', 'yearly']);
            $table->dateTime('period_start');
            $table->dateTime('period_end');
            $table->json('data');
            $table->timestamps();
            $table->index(['device_id', 'aggregate_type', 'period_start'], 'sda_dev_id_type_start_idx');
            $table->index(['device_id', 'aggregate_type', 'period_end'], 'sda_dev_id_type_end_idx');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensor_data_aggregates');
    }
};
