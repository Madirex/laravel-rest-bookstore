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
        Schema::create('order_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('quantity');
            $table->integer('book_id')->nullable();
            $table->foreign('book_id')->references('id')->on('books');
            $table->uuid('order_id');
            $table->foreign('order_id')->references('id')->on('orders');
            $table->double('price', 8, 2);
            $table->double('total', 8, 2);
            $table->double('subtotal', 8, 2);
            $table->boolean('selected');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_lines');
    }
};
