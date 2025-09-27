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
        Schema::create('purchase_items', function (Blueprint $table) {
            $table->increments('PurchaseItemID');
            $table->unsignedInteger('PurchaseID');
            $table->unsignedInteger('ProductID');
            $table->integer('Quantity');
            $table->decimal('UnitPrice', 10, 2);
            $table->decimal('TotalPrice', 10, 2);
            $table->timestamps();
            
            $table->foreign('PurchaseID')->references('PurchaseID')->on('purchases')->onDelete('cascade');
            $table->foreign('ProductID')->references('ProductID')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_items');
    }
};
