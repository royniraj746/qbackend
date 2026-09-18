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

        Schema::table('quotations', function (Blueprint $table) {

            $table->enum('quotation_type', ['supply','erection','supply_erection'])
                ->default('supply')
                ->after('quotation_date');

            $table->text('supply_description')
                ->nullable()
                ->after('quotation_type');

            $table->text('erection_description')
                ->nullable()
                ->after('supply_description');

            // 🔹 Erection Percentages
            $table->decimal('consumables_percent',8,2)->nullable();
            $table->decimal('ppe_percent',8,2)->nullable();
            $table->decimal('supervision_percent',8,2)->nullable();
            $table->decimal('site_mobilization_percent',8,2)->nullable();
            $table->decimal('ld_labour_percent',8,2)->nullable();
            $table->decimal('labour_insurance_percent',8,2)->nullable();
            $table->decimal('erection_profit_percent',8,2)->nullable();
            $table->decimal('price_variation_labour_percent',8,2)->nullable();
            $table->decimal('bds_labour_percent',8,2)->nullable();
            $table->decimal('cushion_labour_percent',8,2)->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {

            $table->dropColumn([
                'quotation_type',
                'supply_description',
                'erection_description',
                'consumables_percent',
                'ppe_percent',
                'supervision_percent',
                'site_mobilization_percent',
                'ld_labour_percent',
                'labour_insurance_percent',
                'erection_profit_percent',
                'price_variation_labour_percent',
                'bds_labour_percent',
                'cushion_labour_percent'
            ]);

        });
    }
};
