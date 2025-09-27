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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('CustomerID');
            $table->unsignedInteger('ProductID');
            $table->integer('quantity');
            $table->decimal('price', 10, 2); // Price at time of adding to cart
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('CustomerID')->references('CustomerID')->on('customers')->onDelete('cascade');
            $table->foreign('ProductID')->references('ProductID')->on('products')->onDelete('cascade');
            
            // Ensure one cart item per customer-product combination
            $table->unique(['CustomerID', 'ProductID']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
