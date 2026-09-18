<?php




// namespace App\Exports\Sheets;

// use Maatwebsite\Excel\Concerns\FromArray;
// use Maatwebsite\Excel\Concerns\WithHeadings;
// use Maatwebsite\Excel\Concerns\WithTitle;

// class QuotationItemsSheet implements FromArray, WithHeadings, WithTitle
// {
//     protected $quotation;

//     public function __construct($quotation)
//     {
//         $this->quotation = $quotation;
//     }

//     public function title(): string
//     {
//         return 'Items';
//     }

//     public function headings(): array
//     {
//         $q = $this->quotation;

//         $headings = [
//             'Sr',
//             'Product Name',
//             'Base Price',
//             'Qty',

//             'Fittings (' . $q->fittings_percent . '%)',
//             'Paint (' . $q->paint_percent . '%)',
//             'Transport (' . $q->transportation_percent . '%)',
//             'Overhead (' . $q->overhead_percent . '%)',
//             'HO (' . $q->ho_expenses_percent . '%)',
//             'LD (' . $q->ld_percent . '%)',
//             'Packaging (' . $q->packaging_percent . '%)',
//             'Insurance (' . $q->insurance_percent . '%)',
//             'Profit (' . $q->profit_percent . '%)',
//             'Price Variation (' . $q->price_variation_percent . '%)',
//             'BDS (' . $q->bds_percent . '%)',
//             'Cushion (' . $q->cushion_percent . '%)',

//             'Total Extra',
//             'Supply Rate(Quotation Rate)',
//         ];

//         // ✅ If Supply + Erection
//         if ($q->quotation_type == 'supply_erection') {

//             $headings = array_merge($headings, [

//                 'Labour Cost',
//                 'Labour Amount',

//                 'Consumables (' . $q->consumables_percent . '%)',
//                 'PPE (' . $q->ppe_percent . '%)',
//                 'Supervision (' . $q->supervision_percent . '%)',
//                 'Site Mobilization (' . $q->site_mobilization_percent . '%)',
//                 'LD Labour (' . $q->ld_labour_percent . '%)',
//                 'Labour Insurance (' . $q->labour_insurance_percent . '%)',
//                 'Erection Profit (' . $q->erection_profit_percent . '%)',
//                 'Price Variation Labour (' . $q->price_variation_labour_percent . '%)',
//                 'BDS Labour (' . $q->bds_labour_percent . '%)',
//                 'Cushion Labour (' . $q->cushion_labour_percent . '%)',

//                 'Total Erection Extra',
//                 'Erection Rate(Quotation Rate)'
//             ]);
//         }

//         $headings = array_merge($headings, [

//             'GST %',
//             'GST Amount',
//             'Total Amount'
//         ]);

//         return $headings;
//     }

//     public function array(): array
//     {
//         $rows = [];

//         foreach ($this->quotation->items as $index => $item) {

//             $row = [
//                 $index + 1,
//                 $item->product->name ?? '-',
//                 $item->base_price,
//                 $item->qty,

//                 $item->fittings_amount,
//                 $item->paint_amount,
//                 $item->transportation_amount,
//                 $item->overhead_amount,
//                 $item->ho_expenses_amount,
//                 $item->ld_amount,
//                 $item->packaging_amount,
//                 $item->insurance_amount,
//                 $item->profit_amount,
//                 $item->price_variation_amount,
//                 $item->bds_amount,
//                 $item->cushion_amount,

//                 $item->total_extra_amount,
//                 $item->supply_rate,
//             ];

//             // ✅ If Supply + Erection
//             if ($this->quotation->quotation_type == 'supply_erection') {

//                 $row = array_merge($row, [

//                     $item->labour_cost,
//                     $item->labour_amount,

//                     $item->consumables_amount,
//                     $item->ppe_amount,
//                     $item->supervision_amount,
//                     $item->site_mobilization_amount,
//                     $item->ld_labour_amount,
//                     $item->labour_insurance_amount,
//                     $item->erection_profit_amount,
//                     $item->price_variation_labour_amount,
//                     $item->bds_labour_amount,
//                     $item->cushion_labour_amount,

//                     $item->total_erection_extra,
//                     $item->erection_rate
//                 ]);
//             }

//             $row = array_merge($row, [

//                 $item->gst_percent,
//                 $item->gst_amount,
//                 $item->total_amount
//             ]);

//             $rows[] = $row;
//         }

//         return $rows;
//     }
// }

// <?php

// namespace App\Exports\Sheets;

// use Maatwebsite\Excel\Concerns\FromArray;
// use Maatwebsite\Excel\Concerns\WithHeadings;
// use Maatwebsite\Excel\Concerns\WithTitle;
// use Maatwebsite\Excel\Concerns\WithStyles;
// use Maatwebsite\Excel\Concerns\WithCustomStartCell;
// use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
// use PhpOffice\PhpSpreadsheet\Style\Alignment;
// use PhpOffice\PhpSpreadsheet\Style\Border;
// use PhpOffice\PhpSpreadsheet\Style\Fill;

// class QuotationItemsSheet implements FromArray, WithHeadings, WithTitle, WithStyles, WithCustomStartCell
// {
//     protected $quotation;

//     public function __construct($quotation)
//     {
//         $this->quotation = $quotation;
//     }

//     public function title(): string
//     {
//         return 'Items';
//     }

//     public function startCell(): string
//     {
//         return 'A2';
//     }

//     public function headings(): array
//     {
//         $q = $this->quotation;

//         $headings = [
//             'Sl No.',
//             'Item',
//             'Make',
//             'Base Cost',
//             'Fittings @ ' . $q->fittings_percent . '%',
//             'Paint /Wrapping Coating @ ' . $q->paint_percent . '%',
//             'Transportation @ ' . $q->transportation_percent . '%',
//             'Over Head Charges @ ' . $q->overhead_percent . '%',
//             'HO Expenses @ ' . $q->ho_expenses_percent . '%',
//             'LD @ ' . $q->ld_percent . '%',
//             'Packaging @ ' . $q->packaging_percent . '%',
//             'Insurance @ ' . $q->insurance_percent . '%',
//             'Profit @ ' . $q->profit_percent . '%',
//             'Price Variation @ ' . $q->price_variation_percent . '%',
//             'BDS @ ' . $q->bds_percent . '%',
//             'Supply Rate',
//             'Cushion @ ' . $q->cushion_percent . '%',
//             'Quote Price',
//         ];

//         if ($q->quotation_type == 'supply_erection') {
//             $headings = array_merge($headings, [
//                 'Labour Cost',
//                 'Consumables @ ' . $q->consumables_percent . '%',
//                 'PPE @ ' . $q->ppe_percent . '%',
//                 'Supervision @ ' . $q->supervision_percent . '%',
//                 'Site Mobilization @ ' . $q->site_mobilization_percent . '%',
//                 'LD @ ' . $q->ld_labour_percent . '%',
//                 'Labour Insurance @ ' . $q->labour_insurance_percent . '%',
//                 'Profit @ ' . $q->erection_profit_percent . '%',
//                 'Price Variation @ ' . $q->price_variation_labour_percent . '%',
//                 'BDS @ ' . $q->bds_labour_percent . '%',
//                 'Erection Rate',
//                 'Cushion @ ' . $q->cushion_labour_percent . '%',
//                 'Quote Price',
//             ]);
//         }

//         $headings = array_merge($headings, [
//             'GST %',
//             'GST Amount',
//             'Total Amount'
//         ]);

//         return $headings;
//     }

//     public function array(): array
//     {
//         $rows = [];

//         foreach ($this->quotation->items as $index => $item) {

//             $row = [
//                 $index + 1,
//                 $item->product->name ?? '-',
//                 $item->make ?? '-',
//                 $item->base_price,
//                 $item->fittings_amount,
//                 $item->paint_amount,
//                 $item->transportation_amount,
//                 $item->overhead_amount,
//                 $item->ho_expenses_amount,
//                 $item->ld_amount,
//                 $item->packaging_amount,
//                 $item->insurance_amount,
//                 $item->profit_amount,
//                 $item->price_variation_amount,
//                 $item->bds_amount,
//                 $item->supply_rate,
//                 $item->cushion_amount,
//                 $item->total_extra_amount,
//             ];

//             if ($this->quotation->quotation_type == 'supply_erection') {
//                 $row = array_merge($row, [
//                     $item->labour_cost,
//                     $item->consumables_amount,
//                     $item->ppe_amount,
//                     $item->supervision_amount,
//                     $item->site_mobilization_amount,
//                     $item->ld_labour_amount,
//                     $item->labour_insurance_amount,
//                     $item->erection_profit_amount,
//                     $item->price_variation_labour_amount,
//                     $item->bds_labour_amount,
//                     $item->erection_rate,
//                     $item->cushion_labour_amount,
//                     $item->total_erection_extra,
//                 ]);
//             }

//             $row = array_merge($row, [
//                 $item->gst_percent,
//                 $item->gst_amount,
//                 $item->total_amount
//             ]);

//             $rows[] = $row;
//         }

//         return $rows;
//     }

//     public function styles(Worksheet $sheet)
//     {
//         $isSupplyErection = $this->quotation->quotation_type == 'supply_erection';

//         // Group Header Row (Row 1)
//         $sheet->mergeCells('D1:R1');
//         $sheet->setCellValue('D1', 'SUPPLY');

//         if ($isSupplyErection) {
//             $sheet->mergeCells('S1:AE1');
//             $sheet->setCellValue('S1', 'ERECTION');
//         }

//         // Top Header Styling
//         $sheet->getStyle('D1:AE1')->applyFromArray([
//             'font' => ['bold' => true, 'size' => 14, 'italic' => true],
//             'alignment' => [
//                 'horizontal' => Alignment::HORIZONTAL_CENTER,
//                 'vertical' => Alignment::VERTICAL_CENTER,
//             ],
//             'fill' => [
//                 'fillType' => Fill::FILL_SOLID,
//                 'startColor' => ['rgb' => 'D9E1F2'], // Soft blue
//             ],
//         ]);

//         // Main Headings Styling (Row 2)
//         $sheet->getStyle('A2:AE2')->applyFromArray([
//             'font' => ['bold' => true, 'size' => 10],
//             'alignment' => [
//                 'horizontal' => Alignment::HORIZONTAL_CENTER,
//                 'vertical' => Alignment::VERTICAL_CENTER,
//                 'wrapText' => true,
//             ],
//             'borders' => [
//                 'allBorders' => [
//                     'borderStyle' => Border::BORDER_THIN,
//                     'color' => ['rgb' => '000000'],
//                 ],
//             ],
//         ]);

//         // Green Highlight for Supply Quote Price Column (R Column)
//         $sheet->getStyle('R2:R' . ($sheet->getHighestRow()))->applyFromArray([
//             'fill' => [
//                 'fillType' => Fill::FILL_SOLID,
//                 'startColor' => ['rgb' => 'C6EFCE'],
//             ],
//         ]);

//         // Green Highlight for Erection Quote Price Column (AE Column if applicable)
//         if ($isSupplyErection) {
//             $sheet->getStyle('AE2:AE' . ($sheet->getHighestRow()))->applyFromArray([
//                 'fill' => [
//                     'fillType' => Fill::FILL_SOLID,
//                     'startColor' => ['rgb' => 'C6EFCE'],
//                 ],
//             ]);
//         }

//         return [];
//     }
// }

// <?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class QuotationItemsSheet implements FromArray, WithHeadings, WithTitle, WithStyles, WithCustomStartCell, WithEvents
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

    public function startCell(): string
    {
        return 'A2';
    }

    public function headings(): array
    {
        $q = $this->quotation;

        $headings = [
            'Sl No.',
            'Item',
            'Make',
            'Base Cost',
            'Fittings @ ' . $q->fittings_percent . '%',
            'Paint /Wrapping Coating @ ' . $q->paint_percent . '%',
            'Transportation @ ' . $q->transportation_percent . '%',
            'Over Head Charges @ ' . $q->overhead_percent . '%',
            'HO Expenses @ ' . $q->ho_expenses_percent . '%',
            'LD @ ' . $q->ld_percent . '%',
            'Packaging @ ' . $q->packaging_percent . '%',
            'Insurance @ ' . $q->insurance_percent . '%',
            'Profit @ ' . $q->profit_percent . '%',
            'Price Variation @ ' . $q->price_variation_percent . '%',
            'BDS @ ' . $q->bds_percent . '%',
            'Supply Rate',
            'Cushion @ ' . $q->cushion_percent . '%',
            '---',
        ];

        if ($q->quotation_type == 'supply_erection') {
            $headings = array_merge($headings, [
                'Labour Cost',
                'Consumables @ ' . $q->consumables_percent . '%',
                'PPE @ ' . $q->ppe_percent . '%',
                'Supervision @ ' . $q->supervision_percent . '%',
                'Site Mobilization @ ' . $q->site_mobilization_percent . '%',
                'LD @ ' . $q->ld_labour_percent . '%',
                'Labour Insurance @ ' . $q->labour_insurance_percent . '%',
                'Profit @ ' . $q->erection_profit_percent . '%',
                'Price Variation @ ' . $q->price_variation_labour_percent . '%',
                'BDS @ ' . $q->bds_labour_percent . '%',
                'Erection Rate',
                'Cushion @ ' . $q->cushion_labour_percent . '%',
                '--',
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
                $item->make ?? '-',
                $item->base_price,
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
                $item->supply_rate,
                $item->cushion_amount,
                $item->total_extra_amount,
            ];

            if ($this->quotation->quotation_type == 'supply_erection') {
                $row = array_merge($row, [
                    $item->labour_cost,
                    $item->consumables_amount,
                    $item->ppe_amount,
                    $item->supervision_amount,
                    $item->site_mobilization_amount,
                    $item->ld_labour_amount,
                    $item->labour_insurance_amount,
                    $item->erection_profit_amount,
                    $item->price_variation_labour_amount,
                    $item->bds_labour_amount,
                    $item->erection_rate,
                    $item->cushion_labour_amount,
                    $item->total_erection_extra,
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

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Freeze pane: Sl No, Item, Make, Base Cost (Columns A to D) freeze rahenge horizontal scroll par.
                // Row 1 & Row 2 headers bhi top par freeze rahenge vertical scroll par.
                $event->sheet->getDelegate()->freezePane('E3');

                // Columns Width Fixes (Item column ka width adjust karke compact text display)
                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(8);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(35); // Item column width fixed
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(15);
            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $isSupplyErection = $this->quotation->quotation_type == 'supply_erection';
        $highestRow = $sheet->getHighestRow();

        // 1. Group Headers Text
        $sheet->mergeCells('D1:R1');
        $sheet->setCellValue('D1', 'SUPPLY');

        // SUPPLY Header Color (Orange Accent)
        $sheet->getStyle('D1:R1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 13, 'italic' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FCE4D6'],
            ],
        ]);

        if ($isSupplyErection) {
            $sheet->mergeCells('S1:AE1');
            $sheet->setCellValue('S1', 'ERECTION');

            // ERECTION Header Color (Blue Accent)
            $sheet->getStyle('S1:AE1')->applyFromArray([
                'font' => ['bold' => true, 'size' => 13, 'italic' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'D9E1F2'],
                ],
            ]);
        }

        // 2. Headings Styling (Row 2) + Auto Text-Wrap for Padding Reduction
        $sheet->getStyle('A2:AH2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 9],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true, // Text wrap se extra wide padding issue resolve ho jaayega
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // 3. Item Column Text Wrap & Left Alignment
        $sheet->getStyle('B3:B' . $highestRow)->getAlignment()->setWrapText(true);
        $sheet->getStyle('B3:B' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // 4. Quote Price Highlights (Green)
        $sheet->getStyle('R2:R' . $highestRow)->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'C6EFCE'],
            ],
        ]);

        if ($isSupplyErection) {
            $sheet->getStyle('AE2:AE' . $highestRow)->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'C6EFCE'],
                ],
            ]);
        }

        return [];
    }
}
