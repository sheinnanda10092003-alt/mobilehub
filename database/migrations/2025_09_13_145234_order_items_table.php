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
        Schema::create('order_items', function (Blueprint $table) {
            $table->increments('OrderItemID');          // Primary key, unsigned integer
            $table->unsignedBigInteger('OrderID');      // FK to orders.OrderID (bigIncrements)
            $table->unsignedInteger('ProductID');       // FK to products.ProductID (increments)
            $table->integer('Quantity');
            $table->decimal('UnitPrice', 10, 2);
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('OrderID')
                  ->references('OrderID')->on('orders')
                  ->onDelete('cascade');

            $table->foreign('ProductID')
                  ->references('ProductID')->on('products')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
