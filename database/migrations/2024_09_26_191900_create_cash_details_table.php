<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashDetailsTable extends Migration
{
    public function up()
    {
        Schema::create('cash_details', function (Blueprint $table) {
            $table->id();  // Primary key
            $table->unsignedBigInteger('cash_id');  // Cash ID
            $table->foreign('cash_id')->references('id')->on('cashes')->onDelete('cascade');  // Foreign key referencing 'cashes'

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

    public function down()
    {
        Schema::dropIfExists('cash_details');
    }
}
