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
        Schema::create('purshases', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_number');
            $table->unsignedBigInteger('supplier_id');  // Proforma ID
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');

            // Use timestamps() once to add created_at and updated_at
            $table->timestamps();

            // Foreign keys for users who created/updated the record
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();

            // Define foreign key constraints
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');

            // Soft delete indicator
            $table->boolean('is_deleted')->default(false);
            // Soft approved indicator
             $table->boolean('is_approved')->default(false);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purshases');
    }
};
