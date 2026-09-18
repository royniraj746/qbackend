<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationItem extends Model
{
    //

    // protected $fillable = [
    //     'quotation_id','product_id','base_price','qty','purchase_amount',
    //     'fittings_amount','paint_amount','transportation_amount',
    //     'overhead_amount','ho_expenses_amount','ld_amount','packaging_amount',
    //     'insurance_amount','profit_amount','price_variation_amount',
    //     'bds_amount','cushion_amount',
    //     'total_extra_amount','supply_rate',
    //     'gst_percent','gst_amount','total_amount'
    // ];
    protected $fillable = [
        'quotation_id',
        'product_id',

        // Base Price
        'base_price',
        'qty',
        'purchase_amount',

        // Supply Amounts
        'fittings_amount',
        'paint_amount',
        'transportation_amount',
        'overhead_amount',
        'ho_expenses_amount',
        'ld_amount',
        'packaging_amount',
        'insurance_amount',
        'profit_amount',
        'price_variation_amount',
        'bds_amount',
        'cushion_amount',

        'total_extra_amount',
        'supply_rate',

        // Labour / Erection
        'labour_cost',
        'labour_amount',
        "erection_gst_amount",
        "supply_gst_amount",

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
        'erection_rate',

        // GST
        'gst_percent',
        'gst_amount',
        'total_amount'
    ];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
