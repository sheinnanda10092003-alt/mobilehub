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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('OrderID');
            $table->string('payment_method'); // e.g., 'credit_card', 'debit_card', 'paypal', 'cash_on_delivery'
            $table->string('payment_status')->default('pending'); // pending, completed, failed, refunded
            $table->decimal('amount', 10, 2);
            $table->string('transaction_id')->nullable(); // For online payments
            $table->json('payment_details')->nullable(); // Store additional payment info
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('OrderID')->references('OrderID')->on('orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
