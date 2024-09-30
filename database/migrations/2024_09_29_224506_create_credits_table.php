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
        Schema::create('credits', function (Blueprint $table) {
            $table->id(); // Primary key (auto-increment)
            $table->string('credit_number')->unique(); // Unique credit number
            $table->date('creation_date'); // Date when the credit was created
            $table->decimal('total_amount', 15, 2)->nullable(); // Total amount (nullable)
            $table->boolean('is_deleted')->default(false); // Soft delete flag
            $table->boolean('is_active')->default(true); // Active status flag
            $table->unsignedBigInteger('customer_id')->nullable(); // Foreign key for Customer
            $table->unsignedBigInteger('created_by')->nullable(); // Foreign key for who created the record
            $table->unsignedBigInteger('updated_by')->nullable(); // Foreign key for who updated the record
            $table->timestamps(); // Laravel-created timestamps for `created_at` and `updated_at`

            // Define foreign key relationships
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null'); // Link to customers table
            $table->foreign('created_by')->references('id')->on('users'); // Link to users table for creator
            $table->foreign('updated_by')->references('id')->on('users'); // Link to users table for updater
     
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credits');
    }
};
