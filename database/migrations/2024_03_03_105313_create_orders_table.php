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
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('status');
            $table->string('subtotal', 8, 2);
            $table->double('total_amount', 8, 2);
            $table->integer('total_lines');
            $table->boolean('is_deleted');
            $table->uuid('cart_code')->nullable();
            $table->foreign('cart_code')->references('id')->on('cart_codes');
            $table->dateTime('finished_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
