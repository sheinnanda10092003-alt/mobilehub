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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->increments('VariantID');
            $table->unsignedInteger('ProductID');
            $table->string('Color', 50);
            $table->string('RAM', 20); // e.g., '4GB', '8GB', '12GB'
            $table->string('Storage', 20); // e.g., '64GB', '128GB', '256GB'
            $table->decimal('Price', 10, 2);
            $table->integer('Stock')->default(0);
            $table->string('SKU', 100)->unique(); // Unique identifier for this variant
            $table->string('image_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('ProductID')->references('ProductID')->on('products')->onDelete('cascade');
            $table->index(['ProductID', 'Color', 'RAM', 'Storage']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
