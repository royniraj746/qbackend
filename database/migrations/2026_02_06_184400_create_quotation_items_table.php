<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('quotation_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('product_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->decimal('base_price', 10, 2);
            $table->integer('qty');

            $table->decimal('purchase_amount', 12, 2);

            // EXTRA CHARGE AMOUNTS
            $table->decimal('fittings_amount', 10, 2)->default(0);
            $table->decimal('paint_amount', 10, 2)->default(0);
            $table->decimal('transportation_amount', 10, 2)->default(0);
            $table->decimal('overhead_amount', 10, 2)->default(0);
            $table->decimal('ho_expenses_amount', 10, 2)->default(0);
            $table->decimal('ld_amount', 10, 2)->default(0);
            $table->decimal('packaging_amount', 10, 2)->default(0);
            $table->decimal('insurance_amount', 10, 2)->default(0);
            $table->decimal('profit_amount', 10, 2)->default(0);
            $table->decimal('price_variation_amount', 10, 2)->default(0);
            $table->decimal('bds_amount', 10, 2)->default(0);
            $table->decimal('cushion_amount', 10, 2)->default(0);

            $table->decimal('total_extra_amount', 12, 2);

            $table->decimal('supply_rate', 12, 2);

            $table->decimal('gst_percent', 5, 2);
            $table->decimal('gst_amount', 12, 2);
            $table->decimal('total_amount', 12, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotation_items');
    }
};
