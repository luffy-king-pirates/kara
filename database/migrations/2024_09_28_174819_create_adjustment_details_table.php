<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdjustmentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adjustment_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('adjustment_id')->constrained()->onDelete('cascade');
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade'); // Reference to Item model
            $table->foreignId('stock_type_id')->constrained('stock_types')->onDelete('cascade'); // Reference to StockType model
            $table->integer('godown')->nullable(); // Godown as a number, default null
            $table->integer('shop')->nullable(); // Shop as a number, default null
            $table->integer('quantity');
            $table->foreignId('unit_id')->constrained('units')->onDelete('cascade'); // Reference to Unit model
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('adjustment_details');
    }
}
