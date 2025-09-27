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
        Schema::create('purchases', function (Blueprint $table) {
            $table->increments('PurchaseID');
            $table->unsignedInteger('SupplierID');
            $table->decimal('TotalAmount', 10, 2);
            $table->enum('Status', ['pending', 'received', 'cancelled'])->default('pending');
            $table->date('PurchaseDate');
            $table->text('Notes')->nullable();
            $table->unsignedInteger('CreatedBy'); // Staff ID who created the purchase
            $table->timestamps();
            
            $table->foreign('SupplierID')->references('SupplierID')->on('suppliers')->onDelete('cascade');
            $table->foreign('CreatedBy')->references('StaffID')->on('staff')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
