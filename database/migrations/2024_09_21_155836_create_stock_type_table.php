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
        Schema::create('stock_type', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('stock_type_name'); // Column for unit name
            $table->unsignedBigInteger('created_by'); // Column for user who created the unit
            $table->unsignedBigInteger('updated_by'); // Column for user who updated the unit
            $table->boolean('is_deleted')->default(false);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_type');
    }
};
