<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_cashes_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashesTable extends Migration
{
    public function up()
    {
        Schema::create('cashes', function (Blueprint $table) {
            $table->id();
        $table->string('cash_number')->unique();
        $table->date('creation_date');
        $table->decimal('total_amount', 15, 2)->nullable();
        $table->boolean('is_deleted')->default(false);
        $table->boolean('is_active')->default(true);
        $table->unsignedBigInteger('customer_id')->nullable(); // Foreign key for Customer
        $table->unsignedBigInteger('created_by')->nullable();
        $table->unsignedBigInteger('updated_by')->nullable();
        $table->timestamps();

        // Define foreign key relationships
        $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
        $table->foreign('created_by')->references('id')->on('users');
        $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cashes');
    }
}
