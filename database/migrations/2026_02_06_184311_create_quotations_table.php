<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('enquiry_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('enquirycustomer_id')
                  ->constrained('enquiry_customers')
                  ->cascadeOnDelete();

            $table->string('quotation_no')->unique();
            $table->date('quotation_date');

            // COMMON EXTRA CHARGE %
            $table->decimal('fittings_percent', 5, 2)->default(0);
            $table->decimal('paint_percent', 5, 2)->default(0);
            $table->decimal('transportation_percent', 5, 2)->default(0);
            $table->decimal('overhead_percent', 5, 2)->default(0);
            $table->decimal('ho_expenses_percent', 5, 2)->default(0);
            $table->decimal('ld_percent', 5, 2)->default(0);
            $table->decimal('packaging_percent', 5, 2)->default(0);
            $table->decimal('insurance_percent', 5, 2)->default(0);
            $table->decimal('profit_percent', 5, 2)->default(0);
            $table->decimal('price_variation_percent', 5, 2)->default(0);
            $table->decimal('bds_percent', 5, 2)->default(0);
            $table->decimal('cushion_percent', 5, 2)->default(0);

            // TOTALS
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('total_gst', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
