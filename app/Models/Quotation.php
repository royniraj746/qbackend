<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    //


    // protected $fillable = [
    //     'enquiry_id','enquirycustomer_id','quotation_no','quotation_date',
    //     'fittings_percent','paint_percent','transportation_percent',
    //     'overhead_percent','ho_expenses_percent','ld_percent','packaging_percent',
    //     'insurance_percent','profit_percent','price_variation_percent',
    //     'bds_percent','cushion_percent',
    //     'subtotal','total_gst','grand_total','created_by'
    // ];
    protected $fillable = [
        'enquiry_id',
        'enquirycustomer_id',
        'quotation_no',
        'quotation_date',

        'quotation_type',
        'supply_description',
        'erection_description',

        // Supply Percentages
        'fittings_percent',
        'paint_percent',
        'transportation_percent',
        'overhead_percent',
        'ho_expenses_percent',
        'ld_percent',
        'packaging_percent',
        'insurance_percent',
        'profit_percent',
        'price_variation_percent',
        'bds_percent',
        'cushion_percent',

        // Erection Percentages
        'consumables_percent',
        'ppe_percent',
        'supervision_percent',
        'site_mobilization_percent',
        'ld_labour_percent',
        'labour_insurance_percent',
        'erection_profit_percent',
        'price_variation_labour_percent',
        'bds_labour_percent',
        'cushion_labour_percent',

        // Totals
        'subtotal',
        'total_gst',
        'grand_total',

        'created_by',
        'rev',
        'erection_total'
    ];

    public function items() {
        return $this->hasMany(related: QuotationItem::class);
    }

    public function enquiry() {
        return $this->belongsTo(Enquiry::class);
    }

    public function Enquirycustomer() {
        return $this->belongsTo(EnquiryCustomer::class,'enquirycustomer_id');
    }
    public function customer() {
        return $this->belongsTo(EnquiryCustomer::class,'enquirycustomer_id');
    }
    public function createdBy(){
        return $this->belongsTo(User::class,'created_by');
    }
}
