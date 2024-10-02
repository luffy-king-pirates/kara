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
        Schema::table('transferts', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('approved_by')->nullable()->after('is_approved'); // User who approved the transfer
            $table->text('receiver')->nullable(); // Additional remarks about the transfer

$table->string('transporter')->nullable();
            // Adding a foreign key constraint for the approved_by column
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transferts', function (Blueprint $table) {
            //
        });
    }
};
