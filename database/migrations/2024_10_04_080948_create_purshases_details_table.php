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
        Schema::create('purshases_details', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('purchase_id');  // Proforma ID
            $table->foreign('purchase_id')->references('id')->on('purshases')->onDelete('cascade');  // Foreign key referencing 'cashes'

            $table->foreignId('item_id')->constrained('items')->onDelete('cascade'); // Reference to Item model

            $table->integer('quantity');
            $table->integer('cost');
            $table->integer('total');
            $table->foreignId('unit_id')->constrained('units')->onDelete('cascade'); // Reference to Unit mode

            $table->foreignId('currency_id')->constrained('currencies')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purshases_details');
    }
};
