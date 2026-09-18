<?php




namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class QuotationSummarySheet implements FromArray, WithTitle
{
    protected $quotation;

    public function __construct($quotation)
    {
        $this->quotation = $quotation;
    }

    public function title(): string
    {
        return 'Summary';
    }

    public function array(): array
    {
        $q = $this->quotation;

        $data = [

            ['Quotation No', $q->quotation_no],
            ['Quotation Date', $q->quotation_date],
            ['Customer', $q->customer->customer_name ?? '-'],
            ['Enquiry No', $q->enquiry->enquiry_code ?? '-'],
            [],

            ['Supply Charges (%)'],
            ['Fittings', $q->fittings_percent],
            ['Paint', $q->paint_percent],
            ['Transportation', $q->transportation_percent],
            ['Overhead', $q->overhead_percent],
            ['HO Expenses', $q->ho_expenses_percent],
            ['LD', $q->ld_percent],
            ['Packaging', $q->packaging_percent],
            ['Insurance', $q->insurance_percent],
            ['Profit', $q->profit_percent],
            ['Price Variation', $q->price_variation_percent],
            ['BDS', $q->bds_percent],
            ['Cushion', $q->cushion_percent],
        ];

        // ✅ Supply + Erection hone par extra section add hoga
        if ($q->quotation_type == 'supply_erection') {

            $data[] = [];
            $data[] = ['Erection Charges (%)'];

            $data[] = ['Consumables', $q->consumables_percent];
            $data[] = ['PPE', $q->ppe_percent];
            $data[] = ['Supervision', $q->supervision_percent];
            $data[] = ['Site Mobilization', $q->site_mobilization_percent];
            $data[] = ['LD Labour', $q->ld_labour_percent];
            $data[] = ['Labour Insurance', $q->labour_insurance_percent];
            $data[] = ['Erection Profit', $q->erection_profit_percent];
            $data[] = ['Price Variation Labour', $q->price_variation_labour_percent];
            $data[] = ['BDS Labour', $q->bds_labour_percent];
            $data[] = ['Cushion Labour', $q->cushion_labour_percent];
        }

        // ✅ Totals
        $data[] = [];
        $data[] = ['Subtotal', $q->subtotal];
        $data[] = ['Total GST', $q->total_gst];
        $data[] = ['Grand Total', $q->grand_total];

        return $data;
    }
}
