<?php



// namespace App\Exports\Sheets;

// use Maatwebsite\Excel\Concerns\FromArray;
// use Maatwebsite\Excel\Concerns\WithTitle;
// use Maatwebsite\Excel\Concerns\WithStyles;
// use Maatwebsite\Excel\Concerns\WithColumnWidths;
// use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
// use PhpOffice\PhpSpreadsheet\Style\Border;
// use PhpOffice\PhpSpreadsheet\Style\Fill;
// use PhpOffice\PhpSpreadsheet\Style\Alignment;

// class QuotationCategorySheet implements FromArray, WithTitle, WithStyles, WithColumnWidths
// {
//     protected $quotation;
//     protected $styledRows = [];

//     public function __construct($quotation)
//     {
//         $this->quotation = $quotation;
//     }

//     public function title(): string
//     {
//         return 'ANNEXURE-2';
//     }

//     public function columnWidths(): array
//     {
//         return [
//             'A' => 10, // Sl. No.
//             'B' => 45, // Description / Product Name
//             'C' => 12, // Unit
//             'D' => 12, // Qty
//             'E' => 20, // Supply Rate (Inc. GST)
//             'F' => 22, // Supply Amount (Inc. GST)
//             'G' => 20, // Erection Rate (Inc. GST)
//             'H' => 22, // Erection Amount (Inc. GST)
//         ];
//     }

//     public function array(): array
//     {
//         $data = [];
//         $currentRow = 1;

//         // Header Info
//         $data[] = ['ANNEXURE-2']; $currentRow++;
//         $data[] = ['Ref: Quotation No.', $this->quotation->quotation_no ?? '']; $currentRow++;
//         $data[] = ['Date', $this->quotation->quotation_date ?? '']; $currentRow++;
//         $data[] = ['Rev', $this->quotation->rev ?? '0']; $currentRow++;
//         $data[] = ['Customer', $this->quotation->customer->customer_name ?? 'M/S Bajel Projects Limited']; $currentRow++;
//         $data[] = []; $currentRow++;

//         // Project Categories Grouping
//         $groupedItems = collect($this->quotation->items)->groupBy('project_category_id');

//         $overallSupplyTotal = 0;
//         $overallErectionTotal = 0;

//         foreach ($groupedItems as $categoryId => $items) {
//             $categoryName = $items->first()->projectcategries->name ?? 'General Category';
//             $categoryDesc = $items->first()->projectcategries->description ?? '';

//             // Category Title Row
//             $data[] = ["CATEGORY: " . strtoupper($categoryName)];
//             $this->styledRows['category_title'][] = $currentRow;
//             $currentRow++;

//             if (!empty($categoryDesc)) {
//                 $data[] = ["Description: " . $categoryDesc];
//                 $this->styledRows['category_desc'][] = $currentRow;
//                 $currentRow++;
//             }

//             // Table Header Row
//             $data[] = [
//                 'SL. NO.',
//                 'DESCRIPTION OF ITEM',
//                 'UNIT',
//                 'QTY',
//                 'SUPPLY RATE',
//                 'SUPPLY AMOUNT',
//                 'ERECTION RATE',
//                 'ERECTION AMOUNT'
//             ];
//             $this->styledRows['table_header'][] = $currentRow;
//             $currentRow++;

//             $categorySupplyTotal = 0;
//             $categoryErectionTotal = 0;
//             $slNo = 1;

//             foreach ($items as $item) {
//                 $qty = (float) ($item->qty ?? 1);
//                 $qty = $qty > 0 ? $qty : 1; // Prevent Division by zero

//                 // 1. SUPPLY CALCULATIONS (Including GST)
//                 $baseSupplyAmount = (float) ($item->supply_rate ?? 0);
//                 $supplyGstAmount = (float) ($item->supply_gst_amount ?? 0);

//                 $supplyAmount = $baseSupplyAmount + $supplyGstAmount;
//                 $supplyRate = $supplyAmount / $qty;

//                 // 2. ERECTION CALCULATIONS (Including GST)
//                 $baseErectionRate = (float) ($item->erection_rate ?? 0);
//                 // $baseErectionAmount = (float) ($item->erection_amount ?? ($baseErectionRate * $qty));
//                 $erectionGstAmount = (float) ($item->erection_gst_amount ?? 0);

//                 // $erectionAmount = $baseErectionAmount + $erectionGstAmount;
//                 $erectionAmount = $erectionGstAmount+$baseErectionRate;
//                 $erectionRate = $erectionAmount / $qty;

//                 $categorySupplyTotal += $supplyAmount;
//                 $categoryErectionTotal += $erectionAmount;

//                 $data[] = [
//                     $slNo++,
//                     $item->product->name ?? 'N/A',
//                     $item->product->unit ?? 'Pcs',
//                     $qty,
//                     $supplyRate,
//                     $supplyAmount,
//                     $erectionRate,
//                     $erectionAmount
//                 ];
//                 $this->styledRows['data_rows'][] = $currentRow;
//                 $currentRow++;
//             }

//             // Category Sub Total Row
//             $data[] = [
//                 '', '', '', 'Sub Total:', '',
//                 $categorySupplyTotal,
//                 '',
//                 $categoryErectionTotal
//             ];
//             $this->styledRows['subtotal'][] = $currentRow;
//             $currentRow++;

//             $data[] = []; // Spacer Row
//             $currentRow++;

//             $overallSupplyTotal += $categorySupplyTotal;
//             $overallErectionTotal += $categoryErectionTotal;
//         }

//         // Grand Summary Block
//         $data[] = ['OVERALL TOTAL (Including GST)'];
//         $this->styledRows['grand_header'] = $currentRow;
//         $currentRow++;

//         $data[] = ['', '', '', 'Total Supply Amount:', $overallSupplyTotal];
//         $this->styledRows['grand_summary'][] = $currentRow;
//         $currentRow++;

//         $data[] = ['', '', '', 'Total Erection Amount:', $overallErectionTotal];
//         $this->styledRows['grand_summary'][] = $currentRow;
//         $currentRow++;

//         $data[] = ['', '', '', 'Grand Total:', $overallSupplyTotal + $overallErectionTotal];
//         $this->styledRows['grand_total'] = $currentRow;

//         return $data;
//     }

//     public function styles(Worksheet $sheet)
//     {
//         // Document Title
//         $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('1B365D'));
//         $sheet->getStyle('A2:A5')->getFont()->setBold(true);

//         // 1. Category Titles
//         if (!empty($this->styledRows['category_title'])) {
//             foreach ($this->styledRows['category_title'] as $row) {
//                 $sheet->getStyle("A{$row}:H{$row}")->applyFromArray([
//                     'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '333333']],
//                     'fill' => [
//                         'fillType' => Fill::FILL_SOLID,
//                         'startColor' => ['rgb' => 'FFF2CC'] // Light Yellow Accent
//                     ]
//                 ]);
//             }
//         }

//         // 2. Table Header Styling (Yellow Accent Header Background)
//         if (!empty($this->styledRows['table_header'])) {
//             foreach ($this->styledRows['table_header'] as $row) {
//                 $sheet->getRowDimension($row)->setRowHeight(28); // Generous Header Padding

//                 // General Header Fill (A-D)
//                 $sheet->getStyle("A{$row}:D{$row}")->applyFromArray([
//                     'font' => ['bold' => true, 'color' => ['rgb' => '212529']],
//                     'fill' => [
//                         'fillType' => Fill::FILL_SOLID,
//                         'startColor' => ['rgb' => 'FFC000'] // Bright Golden Yellow
//                     ],
//                     'alignment' => [
//                         'horizontal' => Alignment::HORIZONTAL_CENTER,
//                         'vertical' => Alignment::VERTICAL_CENTER,
//                     ]
//                 ]);

//                 // Supply Section Header Fill (E-F)
//                 $sheet->getStyle("E{$row}:F{$row}")->applyFromArray([
//                     'font' => ['bold' => true, 'color' => ['rgb' => '064E3B']],
//                     'fill' => [
//                         'fillType' => Fill::FILL_SOLID,
//                         'startColor' => ['rgb' => 'A7F3D0'] // Light Mint Green
//                     ],
//                     'alignment' => [
//                         'horizontal' => Alignment::HORIZONTAL_CENTER,
//                         'vertical' => Alignment::VERTICAL_CENTER,
//                     ]
//                 ]);

//                 // Erection Section Header Fill (G-H)
//                 $sheet->getStyle("G{$row}:H{$row}")->applyFromArray([
//                     'font' => ['bold' => true, 'color' => ['rgb' => '7C2D12']],
//                     'fill' => [
//                         'fillType' => Fill::FILL_SOLID,
//                         'startColor' => ['rgb' => 'FFEDD5'] // Soft Warm Peach
//                     ],
//                     'alignment' => [
//                         'horizontal' => Alignment::HORIZONTAL_CENTER,
//                         'vertical' => Alignment::VERTICAL_CENTER,
//                     ]
//                 ]);
//             }
//         }

//         // 3. Data Rows Styling (Padding, Custom Colors & Gridlines)
//         if (!empty($this->styledRows['data_rows'])) {
//             foreach ($this->styledRows['data_rows'] as $row) {
//                 $sheet->getRowDimension($row)->setRowHeight(24); // Extra Vertical Padding

//                 // Number Formatting
//                 $sheet->getStyle("E{$row}:H{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
//                 $sheet->getStyle("D{$row}")->getNumberFormat()->setFormatCode('#,##0');

//                 // Alignments
//                 $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
//                 $sheet->getStyle("B{$row}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
//                 $sheet->getStyle("C{$row}:D{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
//                 $sheet->getStyle("E{$row}:H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT)->setVertical(Alignment::VERTICAL_CENTER);

//                 // Supply Columns Background Tint (E-F)
//                 $sheet->getStyle("E{$row}:F{$row}")->applyFromArray([
//                     'fill' => [
//                         'fillType' => Fill::FILL_SOLID,
//                         'startColor' => ['rgb' => 'ECFDF5'] // Extra Light Mint
//                     ]
//                 ]);

//                 // Erection Columns Background Tint (G-H)
//                 $sheet->getStyle("G{$row}:H{$row}")->applyFromArray([
//                     'fill' => [
//                         'fillType' => Fill::FILL_SOLID,
//                         'startColor' => ['rgb' => 'FFF7ED'] // Extra Light Peach
//                     ]
//                 ]);

//                 // Thin Square Gridline Borders
//                 $sheet->getStyle("A{$row}:H{$row}")->applyFromArray([
//                     'borders' => [
//                         'allBorders' => [
//                             'borderStyle' => Border::BORDER_THIN,
//                             'color' => ['rgb' => 'D1D5DB']
//                         ]
//                     ]
//                 ]);
//             }
//         }

//         // 4. SubTotal Rows Styling
//         if (!empty($this->styledRows['subtotal'])) {
//             foreach ($this->styledRows['subtotal'] as $row) {
//                 $sheet->getRowDimension($row)->setRowHeight(26);
//                 $sheet->getStyle("A{$row}:H{$row}")->getFont()->setBold(true);
//                 $sheet->getStyle("A{$row}:H{$row}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
//                 $sheet->getStyle("F{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
//                 $sheet->getStyle("H{$row}")->getNumberFormat()->setFormatCode('#,##0.00');

//                 $sheet->getStyle("A{$row}:H{$row}")->applyFromArray([
//                     'fill' => [
//                         'fillType' => Fill::FILL_SOLID,
//                         'startColor' => ['rgb' => 'FEF08A'] // Subtle Yellow Highlight
//                     ],
//                     'borders' => [
//                         'top' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CA8A04']],
//                         'bottom' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['rgb' => 'CA8A04']],
//                     ]
//                 ]);
//             }
//         }

//         // 5. Grand Summary Block Styling
//         if (!empty($this->styledRows['grand_header'])) {
//             $row = $this->styledRows['grand_header'];
//             $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(12)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('854D0E'));
//         }

//         if (!empty($this->styledRows['grand_summary'])) {
//             foreach ($this->styledRows['grand_summary'] as $row) {
//                 $sheet->getRowDimension($row)->setRowHeight(22);
//                 $sheet->getStyle("D{$row}")->getFont()->setBold(true);
//                 $sheet->getStyle("E{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
//             }
//         }

//         if (!empty($this->styledRows['grand_total'])) {
//             $row = $this->styledRows['grand_total'];
//             $sheet->getRowDimension($row)->setRowHeight(28);
//             $sheet->getStyle("D{$row}:E{$row}")->getFont()->setBold(true)->setSize(12);
//             $sheet->getStyle("E{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
//             $sheet->getStyle("D{$row}:E{$row}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

//             $sheet->getStyle("D{$row}:E{$row}")->applyFromArray([
//                 'fill' => [
//                     'fillType' => Fill::FILL_SOLID,
//                     'startColor' => ['rgb' => 'FEF08A'] // Golden Accent
//                 ],
//                 'borders' => [
//                     'top' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '854D0E']],
//                     'bottom' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['rgb' => '854D0E']],
//                 ]
//             ]);
//         }

//         return [];
//     }
// }



// <?php
// <?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class QuotationCategorySheet implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    protected $quotation;
    protected $styledRows = [];

    public function __construct($quotation)
    {
        $this->quotation = $quotation;
    }

    public function title(): string
    {
        return 'ANNEXURE-2';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10, // SL.NO.
            'B' => 45, // DESCRIPTION OF ITEM
            'C' => 14, // Actl. QTY.
            'D' => 16, // Qty. Considered
            'E' => 12, // UNIT
            'F' => 18, // SUPPLY Rate
            'G' => 22, // SUPPLY Amount
            'H' => 18, // ERECTION Rate
            'I' => 22, // ERECTION Amount
        ];
    }

    public function array(): array
    {
        $data = [];
        $currentRow = 1;

        // --- 1. Document Title ---
        $data[] = ['ANNEXURE-2', '', '', '', '', '', '', '', ''];
        $currentRow++;

        // --- 2. Top Meta Header Block ---
        $data[] = [
            'Ref:',
            'Quotation No. - ' . ($this->quotation->quotation_no ?? ''),
            '', '', '',
            'Date : ' . ($this->quotation->quotation_date ?? ''),
            '',
            'Rev : ' . ($this->quotation->rev ?? '0'),
            ''
        ];
        $currentRow++;

        $data[] = ['Customer:', $this->quotation->customer->customer_name ?? 'M/S Bajel Projects Limited', '', '', '', '', '', '', ''];
        $currentRow++;

        $data[] = ['Site:', $this->quotation->site_location ?? '', '', '', '', '', '', '', ''];
        $currentRow++;

        // --- 3. Section Title ---
        $data[] = ['SITC OF FIRE PROTECTION SYSTEM :', '', '', '', '', '', '', '', ''];
        $currentRow++;

        // --- 4. Two-Level Table Header ---
        $data[] = ['SL.NO.', 'DESCRIPTION OF ITEM', 'Actl. QTY.', 'Qty. Considered', 'UNIT', 'SUPPLY', '', 'ERECTION', ''];
        $this->styledRows['table_header_top'] = $currentRow;
        $currentRow++;

        $data[] = ['', '', '', '', '', 'Rate', 'Amount', 'Rate', 'Amount'];
        $this->styledRows['table_header_sub'] = $currentRow;
        $currentRow++;

        // --- 5. Data & Category Loop (Calculations Same) ---
        $groupedItems = collect($this->quotation->items)->groupBy('project_category_id');

        $overallSupplyTotal = 0;
        $overallErectionTotal = 0;

        foreach ($groupedItems as $categoryId => $items) {
            $categoryName = $items->first()->projectcategries->name ?? 'General Category';

            // Category Title Row
            $data[] = ['A', "CATEGORY: " . strtoupper($categoryName), '', '', '', '', '', '', ''];
            $this->styledRows['category_row'][] = $currentRow;
            $currentRow++;

            $categorySupplyTotal = 0;
            $categoryErectionTotal = 0;
            $slNo = 1;

            foreach ($items as $item) {
                $qty = (float) ($item->qty ?? 1);
                $qty = $qty > 0 ? $qty : 1;

                // Supply Calculations
                $baseSupplyAmount = (float) ($item->supply_rate ?? 0);
                $supplyGstAmount = (float) ($item->supply_gst_amount ?? 0);
                $supplyAmount = $baseSupplyAmount + $supplyGstAmount;
                $supplyRate = $supplyAmount / $qty;

                // Erection Calculations
                $baseErectionRate = (float) ($item->erection_rate ?? 0);
                $erectionGstAmount = (float) ($item->erection_gst_amount ?? 0);
                $erectionAmount = $erectionGstAmount + $baseErectionRate;
                $erectionRate = $erectionAmount / $qty;

                $categorySupplyTotal += $supplyAmount;
                $categoryErectionTotal += $erectionAmount;

                $data[] = [
                    $slNo++,
                    $item->product->name ?? 'N/A',
                    (float) ($item->actual_qty ?? $qty),
                    (float) $qty,
                    $item->product->unit ?? 'Pcs',
                    (float) $supplyRate,
                    (float) $supplyAmount,
                    (float) $erectionRate,
                    (float) $erectionAmount
                ];
                $this->styledRows['data_rows'][] = $currentRow;
                $currentRow++;
            }

            // Category Sub Total Row
            $data[] = [
                '', 'Sub Total', '', '', '', '',
                (float) $categorySupplyTotal,
                '',
                (float) $categoryErectionTotal
            ];
            $this->styledRows['subtotal'][] = $currentRow;
            $currentRow++;

            $overallSupplyTotal += $categorySupplyTotal;
            $overallErectionTotal += $categoryErectionTotal;
        }

        // --- 6. Bottom Summary / Grand Total Block ---
        $data[] = ['', '', '', '', '', '', '', '', '']; // Spacer Row
        $currentRow++;

        // Fixed Array Structure (Amount explicitly at Index 8 -> Column I)
        $data[] = ['', '', '', '', '', 'TOTAL SUPPLY AMOUNT', '', '', (float) $overallSupplyTotal];
        $this->styledRows['bottom_summary_supply'] = $currentRow;
        $currentRow++;

        $data[] = ['', '', '', '', '', 'TOTAL ERECTION AMOUNT', '', '', (float) $overallErectionTotal];
        $this->styledRows['bottom_summary_erection'] = $currentRow;
        $currentRow++;

        $data[] = ['', '', '', '', '', 'GRAND TOTAL', '', '', (float) ($overallSupplyTotal + $overallErectionTotal)];
        $this->styledRows['grand_total'] = $currentRow;

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        // Global Gridlines Enable
        $sheet->setShowGridLines(true);

        // 1. Document Title
        $sheet->getRowDimension(1)->setRowHeight(28);
        $sheet->mergeCells('A1:I1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
        ]);

        // 2. Info Block Borders & Merges (Rows 2 - 4) + Padding
        for ($r = 2; $r <= 4; $r++) {
            $sheet->getRowDimension($r)->setRowHeight(22);
        }

        $sheet->getStyle('A2:I4')->applyFromArray([
            'font' => ['bold' => true, 'size' => 10],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'horizontal' => Alignment::HORIZONTAL_LEFT
            ],
        ]);

        $sheet->mergeCells('B2:E2');
        $sheet->mergeCells('F2:G2');
        $sheet->mergeCells('H2:I2');
        $sheet->mergeCells('B3:I3');
        $sheet->mergeCells('B4:I4');

        // 3. SITC Header (Row 5)
        $sheet->getRowDimension(5)->setRowHeight(24);
        $sheet->mergeCells('A5:I5');
        $sheet->getStyle('A5')->applyFromArray([
            'font' => ['bold' => true, 'size' => 11],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
            ],
        ]);

        // 4. Main Two-level Table Headers (Row 6 & 7) - Padding & Green Fill
        $sheet->getRowDimension(6)->setRowHeight(25);
        $sheet->getRowDimension(7)->setRowHeight(25);

        $sheet->mergeCells('A6:A7');
        $sheet->mergeCells('B6:B7');
        $sheet->mergeCells('C6:C7');
        $sheet->mergeCells('D6:D7');
        $sheet->mergeCells('E6:E7');
        $sheet->mergeCells('F6:G6'); // SUPPLY
        $sheet->mergeCells('H6:I6'); // ERECTION

        $sheet->getStyle('A6:I7')->applyFromArray([
            'font' => ['bold' => true, 'size' => 10],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E2EFDA'] // Light Sage Green
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
            ],
        ]);

        // 5. Category Titles Styling (Soft Blue Fill + Padding)
        if (!empty($this->styledRows['category_row'])) {
            foreach ($this->styledRows['category_row'] as $row) {
                $sheet->getRowDimension($row)->setRowHeight(24);
                $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'DDEBF7'] // Soft Blue
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                    ],
                ]);
            }
        }

        // 6. Data Rows Formatting (Alignments, Padding & Decimal Formatting)
        if (!empty($this->styledRows['data_rows'])) {
            foreach ($this->styledRows['data_rows'] as $row) {
                $sheet->getRowDimension($row)->setRowHeight(22); // Vertical Padding

                // Horizontal Alignments
                $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle("C{$row}:E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle("F{$row}:I{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT)->setVertical(Alignment::VERTICAL_CENTER);

                // Decimal Formatting (2 Decimal Places)
                $sheet->getStyle("F{$row}:I{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle("C{$row}:D{$row}")->getNumberFormat()->setFormatCode('#,##0.00');

                $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                    ],
                ]);
            }
        }

        // 7. SubTotal Styling
        if (!empty($this->styledRows['subtotal'])) {
            foreach ($this->styledRows['subtotal'] as $row) {
                $sheet->getRowDimension($row)->setRowHeight(24);
                $sheet->getStyle("A{$row}:I{$row}")->getFont()->setBold(true);
                $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle("G{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT)->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle("I{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT)->setVertical(Alignment::VERTICAL_CENTER);

                $sheet->getStyle("G{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle("I{$row}")->getNumberFormat()->setFormatCode('#,##0.00');

                $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFF2CC']
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                    ],
                ]);
            }
        }

        // 8. Bottom Totals Block Styling (Supply Total, Erection Total & Grand Total)
        $summaryRows = [
            $this->styledRows['bottom_summary_supply'] ?? null,
            $this->styledRows['bottom_summary_erection'] ?? null
        ];

        foreach ($summaryRows as $row) {
            if ($row) {
                $sheet->getRowDimension($row)->setRowHeight(24);
                $sheet->mergeCells("F{$row}:H{$row}");

                $sheet->getStyle("F{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle("I{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT)->setVertical(Alignment::VERTICAL_CENTER);

                $sheet->getStyle("F{$row}:I{$row}")->getFont()->setBold(true);
                $sheet->getStyle("I{$row}")->getNumberFormat()->setFormatCode('#,##0.00');

                $sheet->getStyle("F{$row}:I{$row}")->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                    ],
                ]);
            }
        }

        // Grand Total Row Styling
        if (!empty($this->styledRows['grand_total'])) {
            $row = $this->styledRows['grand_total'];
            $sheet->getRowDimension($row)->setRowHeight(26);
            $sheet->mergeCells("F{$row}:H{$row}");

            $sheet->getStyle("F{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("I{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT)->setVertical(Alignment::VERTICAL_CENTER);

            $sheet->getStyle("F{$row}:I{$row}")->getFont()->setBold(true)->setSize(11);
            $sheet->getStyle("I{$row}")->getNumberFormat()->setFormatCode('#,##0.00');

            $sheet->getStyle("F{$row}:I{$row}")->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2EFDA'] // Light Sage Green Accent
                ],
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '000000']],
                ],
            ]);
        }

        return [];
    }
}
