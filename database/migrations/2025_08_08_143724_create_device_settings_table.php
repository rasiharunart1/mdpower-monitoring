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
        Schema::create('device_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained('devices')->onDelete('cascade');
            $table->decimal('temp1_threshold', 8,2)->default(0.00);
            $table->decimal('temp2_threshold', 8,2)->default(0.00);
            $table->boolean('alert_enabled')->default(true);
            $table->boolean('email_notification')->default(true);
            $table->integer('data_collection_interval')->default(5); // in minutes
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_settings');
    }
};
