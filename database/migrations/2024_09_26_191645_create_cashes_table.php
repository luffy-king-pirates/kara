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
            $table->integer('expected_profit')->nullable();
            $table->integer('cash_number');
            $table->string('customer_name');
            $table->string('tin_number')->nullable();
            $table->date('creation_date');
            $table->integer('vat_amount')->nullable();
            $table->integer('total_items')->nullable();
            $table->boolean('is_active')->nullable();
            $table->boolean('is_deleted')->nullable();
            $table->integer('inclusive_amount')->nullable();
            $table->integer('exclusive_amount')->nullable();
            $table->timestamps();
            $table->string('vrn_number')->nullable();
            $table->boolean('cash_aproved')->default(0)->nullable();
            $table->integer('created_by')->nullable();
            $table->string('customer_adress')->nullable();
            $table->integer('updated_by')->nullable();
            $table->string('company_name');
            $table->integer('sales_profit')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cashes');
    }
}
