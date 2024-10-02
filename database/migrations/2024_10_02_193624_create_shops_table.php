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
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id'); // Foreign key to Item
            $table->unsignedBigInteger('unit_id'); // Foreign key to Unit
            $table->integer('quantity'); // Quantity available in godown
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};
