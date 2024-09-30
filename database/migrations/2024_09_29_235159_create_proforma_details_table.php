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
        Schema::create('proforma_details', function (Blueprint $table) {
            $table->id();  // Primary key
            $table->unsignedBigInteger('proforma_id');  // Proforma ID
            $table->foreign('proforma_id')->references('id')->on('proformas')->onDelete('cascade');  // Foreign key referencing 'cashes'

            $table->unsignedBigInteger('item_id');  // Define item_id first
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');  // Foreign key referencing 'items'

            $table->unsignedBigInteger('unit_id');  // Define unit_id first
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');  // Foreign key referencing 'units'

            $table->integer('quantity');
            $table->decimal('price', 15, 2);
            $table->decimal('total', 15, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proforma_details');
    }
};
