<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashReturnsTable extends Migration
{
    public function up()
    {
        Schema::create('cash_returns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cash_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->timestamp('return_date');
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->boolean('is_active')->nullable();
            $table->boolean('is_deleted')->nullable();

            $table->foreign('cash_id')->references('id')->on('cashes')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cash_returns');
    }
}
