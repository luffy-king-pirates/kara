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
        Schema::table('cashes', function (Blueprint $table) {
            $table->string('comment')->nullable();
            $table->string('special_releif_number')->nullable();
            $table->string('discount')->nullable();
            $table->string('lpo')->nullable();
            $table->string('status')->nullable();
            $table->string('total_qty')->nullable();
            $table->string('vat')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cashes', function (Blueprint $table) {
            //
        });
    }
};
