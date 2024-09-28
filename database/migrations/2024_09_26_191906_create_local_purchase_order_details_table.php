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
        Schema::create('local_purchase_order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('local_purchase_order_id')->constrained('local_purchase_order');
            $table->foreignId('item_id')->constrained('items');
            $table->integer('quantity');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('local_purchase_order_details');
    }
};
