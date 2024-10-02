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
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->string('action');           // Store the action performed
            $table->string('user_name')->nullable(); // Store the name of the user
            $table->timestamp('action_time');   // Store the time of the action
            $table->text('payload')->nullable(); // Store the payload (e.g., request data)
            $table->string('ip_address')->nullable(); // Store the IP address of the user
            $table->string('location')->nullable();   // Store the location (city, country)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};
