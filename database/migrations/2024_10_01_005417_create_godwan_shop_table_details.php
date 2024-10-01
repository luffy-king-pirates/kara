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
        Schema::create('godwan_shop_details', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('godwan_shop_id');  // Proforma ID
            $table->foreign('godwan_shop_id')->references('id')->on('godwan_shops')->onDelete('cascade');  // Foreign key referencing 'cashes'

            $table->foreignId('item_id')->constrained('items')->onDelete('cascade'); // Reference to Item model
            $table->integer('godown')->nullable(); // Godown as a number, default null
            $table->integer('shop')->nullable(); // Shop as a number, default null
            $table->integer('quantity');
            $table->foreignId('unit_id')->constrained('units')->onDelete('cascade'); // Reference to Unit mode
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('godwan_shop_table_details');
    }
};
