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
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained('devices')->onDelete('cascade');
            $table->string('alert_type'); // e.g., 'temperature', 'voltage
            $table->string('text');
            $table->enum('severity', ['low', 'medium', 'high','critical'])->default('medium');
            $table->boolean('is_resolved')->default(false);
            $table->timestamp('resolved_at')->nullable();
            $table->decimal('sensor_value', 8, 2)->nullable(); // Value that triggered the alert
            $table->decimal('threshold_value', 8, 2)->nullable(); // Threshold that was exceeded

            $table->timestamps();
            $table->index(['device_id', 'is_resolved']);
            $table->index(['alert_type', 'severity']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};
