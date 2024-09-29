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
        Schema::create('items', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('item_code', 255)->nullable();
            $table->string('item_name', 255);
            $table->unsignedBigInteger('item_category'); // This must match the 'id' type in 'item_groups'
            $table->unsignedBigInteger('item_brand'); // Assuming a similar foreign key
            $table->unsignedBigInteger('item_unit');
            $table->string('item_size', 255)->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->boolean('is_active')->nullable();
            $table->boolean('is_deleted')->nullable();

            // Foreign key constraints
            $table->foreign('item_category')->references('id')->on('categories')->onDelete('cascade'); // Ensure data types match
            $table->foreign('item_brand')->references('id')->on('brands')->onDelete('cascade');
            $table->foreign('item_unit')->references('id')->on('units')->onDelete('cascade');



            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
