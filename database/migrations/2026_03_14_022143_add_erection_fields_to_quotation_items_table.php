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
        Schema::table('quotation_items', function (Blueprint $table) {

            $table->decimal('labour_cost',10,2)->nullable();
            $table->decimal('labour_amount',10,2)->nullable();

            $table->decimal('consumables_amount',10,2)->nullable();
            $table->decimal('ppe_amount',10,2)->nullable();
            $table->decimal('supervision_amount',10,2)->nullable();
            $table->decimal('site_mobilization_amount',10,2)->nullable();
            $table->decimal('ld_labour_amount',10,2)->nullable();
            $table->decimal('labour_insurance_amount',10,2)->nullable();
            $table->decimal('erection_profit_amount',10,2)->nullable();
            $table->decimal('price_variation_labour_amount',10,2)->nullable();
            $table->decimal('bds_labour_amount',10,2)->nullable();
            $table->decimal('cushion_labour_amount',10,2)->nullable();

            $table->decimal('total_erection_extra',10,2)->nullable();
            $table->decimal('erection_rate',10,2)->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotation_items', function (Blueprint $table) {

            $table->dropColumn([
                'labour_cost',
                'labour_amount',
                'consumables_amount',
                'ppe_amount',
                'supervision_amount',
                'site_mobilization_amount',
                'ld_labour_amount',
                'labour_insurance_amount',
                'erection_profit_amount',
                'price_variation_labour_amount',
                'bds_labour_amount',
                'cushion_labour_amount',
                'total_erection_extra',
                'erection_rate'
            ]);

        });
    }
};
