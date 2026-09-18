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

            // Basic Info
            $table->string('name');
            $table->string('product_code')->unique(); // Auto generate
            $table->string('sku')->unique();

            // Relations
            $table->foreignId('category_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete();

            $table->foreignId('brand_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete();

            // Description
            $table->text('description')->nullable();

            // Pricing
            $table->decimal('base_price', 10, 2);
            $table->decimal('interest_rate', 5, 2)->nullable(); // %
            $table->decimal('tax_rate', 5, 2)->nullable(); // GST %
            $table->decimal('selling_price', 10, 2)->nullable();

            // Inventory
            $table->string('unit')->nullable(); // kg, pcs
            $table->integer('stock_quantity')->default(0);
            $table->integer('min_stock_alert')->default(0);

            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);

            $table->timestamps();
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
