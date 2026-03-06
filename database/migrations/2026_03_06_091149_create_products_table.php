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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category', 100)->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('discount_percentage', 5, 2)->nullable();
            $table->decimal('rating', 3, 2)->nullable();
            $table->integer('stock')->nullable();
            $table->string('brand', 100)->nullable();
            $table->string('sku', 50)->unique()->nullable();
            $table->integer('weight')->nullable();
            $table->string('warranty_information', 255)->nullable();
            $table->string('shipping_information', 255)->nullable();
            $table->string('availability_status', 50)->nullable();
            $table->string('return_policy', 255)->nullable();
            $table->integer('minimum_order_quantity')->nullable();

            // JSON columns for structured data
            $table->json('tags')->nullable();
            $table->json('images')->nullable();
            $table->json('dimensions')->nullable();
            $table->json('reviews')->nullable();
            $table->json('meta')->nullable();

            $table->text('thumbnail')->nullable();
            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
