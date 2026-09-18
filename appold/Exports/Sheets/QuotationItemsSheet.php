<?php




namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class QuotationItemsSheet implements FromArray, WithHeadings, WithTitle
{
    protected $quotation;

    public function __construct($quotation)
    {
        $this->quotation = $quotation;
    }

    public function title(): string
    {
        return 'Items';
    }

    public function headings(): array
    {
        $q = $this->quotation;

        $headings = [
            'Sr',
            'Product Name',
            'Base Price',
            'Qty',

            'Fittings (' . $q->fittings_percent . '%)',
            'Paint (' . $q->paint_percent . '%)',
            'Transport (' . $q->transportation_percent . '%)',
            'Overhead (' . $q->overhead_percent . '%)',
            'HO (' . $q->ho_expenses_percent . '%)',
            'LD (' . $q->ld_percent . '%)',
            'Packaging (' . $q->packaging_percent . '%)',
            'Insurance (' . $q->insurance_percent . '%)',
            'Profit (' . $q->profit_percent . '%)',
            'Price Variation (' . $q->price_variation_percent . '%)',
            'BDS (' . $q->bds_percent . '%)',
            'Cushion (' . $q->cushion_percent . '%)',

            'Total Extra',
            'Supply Rate(Quotation Rate)',
        ];

        // ✅ If Supply + Erection
        if ($q->quotation_type == 'supply_erection') {

            $headings = array_merge($headings, [

                'Labour Cost',
                'Labour Amount',

                'Consumables (' . $q->consumables_percent . '%)',
                'PPE (' . $q->ppe_percent . '%)',
                'Supervision (' . $q->supervision_percent . '%)',
                'Site Mobilization (' . $q->site_mobilization_percent . '%)',
                'LD Labour (' . $q->ld_labour_percent . '%)',
                'Labour Insurance (' . $q->labour_insurance_percent . '%)',
                'Erection Profit (' . $q->erection_profit_percent . '%)',
                'Price Variation Labour (' . $q->price_variation_labour_percent . '%)',
                'BDS Labour (' . $q->bds_labour_percent . '%)',
                'Cushion Labour (' . $q->cushion_labour_percent . '%)',

                'Total Erection Extra',
                'Erection Rate(Quotation Rate)'
            ]);
        }

        $headings = array_merge($headings, [

            'GST %',
            'GST Amount',
            'Total Amount'
        ]);

        return $headings;
    }

    public function array(): array
    {
        $rows = [];

        foreach ($this->quotation->items as $index => $item) {

            $row = [
                $index + 1,
                $item->product->name ?? '-',
                $item->base_price,
                $item->qty,

                $item->fittings_amount,
                $item->paint_amount,
                $item->transportation_amount,
                $item->overhead_amount,
                $item->ho_expenses_amount,
                $item->ld_amount,
                $item->packaging_amount,
                $item->insurance_amount,
                $item->profit_amount,
                $item->price_variation_amount,
                $item->bds_amount,
                $item->cushion_amount,

                $item->total_extra_amount,
                $item->supply_rate,
            ];

            // ✅ If Supply + Erection
            if ($this->quotation->quotation_type == 'supply_erection') {

                $row = array_merge($row, [

                    $item->labour_cost,
                    $item->labour_amount,

                    $item->consumables_amount,
                    $item->ppe_amount,
                    $item->supervision_amount,
                    $item->site_mobilization_amount,
                    $item->ld_labour_amount,
                    $item->labour_insurance_amount,
                    $item->erection_profit_amount,
                    $item->price_variation_labour_amount,
                    $item->bds_labour_amount,
                    $item->cushion_labour_amount,

                    $item->total_erection_extra,
                    $item->erection_rate
                ]);
            }

            $row = array_merge($row, [

                $item->gst_percent,
                $item->gst_amount,
                $item->total_amount
            ]);

            $rows[] = $row;
        }

        return $rows;
    }
}
