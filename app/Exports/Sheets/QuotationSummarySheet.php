<?php


// namespace App\Exports\Sheets;

// use Maatwebsite\Excel\Concerns\FromArray;
// use Maatwebsite\Excel\Concerns\WithTitle;

// class QuotationSummarySheet implements FromArray, WithTitle
// {
//     protected $quotation;

//     public function __construct($quotation)
//     {
//         $this->quotation = $quotation;
//     }

//     public function title(): string
//     {
//         return 'Summary';
//     }

//     public function array(): array
//     {
//         $q = $this->quotation;

//         $data = [

//             ['Quotation No', $q->quotation_no],
//             ['Quotation Date', $q->quotation_date],
//             ['Customer', $q->customer->customer_name ?? '-'],
//             ['Enquiry No', $q->enquiry->enquiry_code ?? '-'],
//             [],

//             ['Supply Charges (%)'],
//             ['Fittings', $q->fittings_percent],
//             ['Paint', $q->paint_percent],
//             ['Transportation', $q->transportation_percent],
//             ['Overhead', $q->overhead_percent],
//             ['HO Expenses', $q->ho_expenses_percent],
//             ['LD', $q->ld_percent],
//             ['Packaging', $q->packaging_percent],
//             ['Insurance', $q->insurance_percent],
//             ['Profit', $q->profit_percent],
//             ['Price Variation', $q->price_variation_percent],
//             ['BDS', $q->bds_percent],
//             ['Cushion', $q->cushion_percent],
//         ];

//         // ✅ Supply + Erection hone par extra section add hoga
//         if ($q->quotation_type == 'supply_erection') {

//             $data[] = [];
//             $data[] = ['Erection Charges (%)'];

//             $data[] = ['Consumables', $q->consumables_percent];
//             $data[] = ['PPE', $q->ppe_percent];
//             $data[] = ['Supervision', $q->supervision_percent];
//             $data[] = ['Site Mobilization', $q->site_mobilization_percent];
//             $data[] = ['LD Labour', $q->ld_labour_percent];
//             $data[] = ['Labour Insurance', $q->labour_insurance_percent];
//             $data[] = ['Erection Profit', $q->erection_profit_percent];
//             $data[] = ['Price Variation Labour', $q->price_variation_labour_percent];
//             $data[] = ['BDS Labour', $q->bds_labour_percent];
//             $data[] = ['Cushion Labour', $q->cushion_labour_percent];
//         }

//         // ✅ Totals
//         $data[] = [];
//         $data[] = ['Subtotal', $q->subtotal];
//         $data[] = ['Total GST', $q->total_gst];
//         $data[] = ['Grand Total', $q->grand_total];

//         return $data;
//     }
// }



// <?php



// namespace App\Exports\Sheets;

// use Maatwebsite\Excel\Concerns\FromArray;
// use Maatwebsite\Excel\Concerns\WithTitle;
// use Maatwebsite\Excel\Concerns\WithStyles;
// use Maatwebsite\Excel\Concerns\WithColumnWidths;
// use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
// use PhpOffice\PhpSpreadsheet\Style\Border;
// use PhpOffice\PhpSpreadsheet\Style\Fill;
// use PhpOffice\PhpSpreadsheet\Style\Alignment;

// class QuotationSummarySheet implements FromArray, WithTitle, WithStyles, WithColumnWidths
// {
//     protected $quotation;
//     protected $styledRows = [];

//     public function __construct($quotation)
//     {
//         $this->quotation = $quotation;
//     }

//     public function title(): string
//     {
//         return 'Summary - Annexure 1';
//     }

//     public function columnWidths(): array
//     {
//         return [
//             'A' => 10, // Sl. No.
//             'B' => 50, // Item Description / Category Name
//             'C' => 10, // Unit
//             'D' => 10, // Qty
//             'E' => 20, // Supply Rate
//             'F' => 22, // Supply Amount
//             'G' => 20, // Erection Rate
//             'H' => 22, // Erection Amount
//         ];
//     }

//     public function array(): array
//     {
//         $data = [];
//         $currentRow = 1;

//         // Header Info
//         $data[] = ['ANNEXURE-1']; $currentRow++;
//         $data[] = ['Ref: Offer No.', $this->quotation->quotation_no ?? '']; $currentRow++;
//         $data[] = ['Date', $this->quotation->quotation_date ?? '']; $currentRow++;
//         $data[] = ['REV', $this->quotation->rev ?? '0']; $currentRow++;
//         $data[] = ['Customer', $this->quotation->customer->customer_name ?? 'M/S Bajel Projects Limited']; $currentRow++;
//         $data[] = ['Site', $this->quotation->site_location ?? $this->quotation->project_name ?? 'N/A']; $currentRow++;
//         $data[] = []; $currentRow++;

//         // Project Main Title
//         $titleText = "SUPPLY, INSTALLATION, TESTING & COMMISSIONING OF FIRE PROTECTION SYSTEM : " . strtoupper($this->quotation->customer->customer_name ?? '');
//         $data[] = [$titleText];
//         $this->styledRows['main_title'] = $currentRow;
//         $currentRow++;
//         $data[] = []; $currentRow++;

//         // Table Header Row
//         $data[] = [
//             'SL. NO.',
//             'ITEM DESCRIPTION',
//             'UNIT',
//             'QTY.',
//             'SUPPLY RATE',
//             'SUPPLY AMOUNT',
//             'ERECTION RATE',
//             'ERECTION AMOUNT'
//         ];
//         $this->styledRows['table_header'] = $currentRow;
//         $currentRow++;

//         // Group Items by Category
//         $groupedItems = collect($this->quotation->items)->groupBy('project_category_id');

//         // Optional: Group by Section (Main Equipment vs Spares) if available, else standard category list
//         $mainEquipmentItems = [];
//         $sparesItems = [];

//         foreach ($groupedItems as $categoryId => $items) {
//             $firstItem = $items->first();
//             $categoryName = $firstItem->projectcategries->name ?? 'General Category';
//             $isSpare = str_contains(strtolower($categoryName), 'spare');

//             if ($isSpare) {
//                 $sparesItems[$categoryId] = $items;
//             } else {
//                 $mainEquipmentItems[$categoryId] = $items;
//             }
//         }

//         $grandSupplyTotal = 0;
//         $grandErectionTotal = 0;

//         // ----------------------------------------------------
//         // SECTION A: Main Equipment
//         // ----------------------------------------------------
//         if (!empty($mainEquipmentItems)) {
//             $data[] = ['A. Fire Protections – Main Equipment'];
//             $this->styledRows['section_header'][] = $currentRow;
//             $currentRow++;

//             $slNo = 1;
//             $sectionSupplyTotal = 0;
//             $sectionErectionTotal = 0;

//             foreach ($mainEquipmentItems as $categoryId => $items) {
//                 $firstItem = $items->first();
//                 $categoryName = $firstItem->projectcategries->name ?? 'Category ' . $slNo;
//                 $unit = $firstItem->projectcategries->unit ?? $firstItem->unit ?? 'SET';

//                 // Get Category Quantity from quotation_items
//                 $categoryQty = (float) ($firstItem->project_category_qty ?? 1);
//                 $categoryQty = $categoryQty > 0 ? $categoryQty : 1;

//                 // Calculate Category Unit Rate from Annexure-2 Items Sum
//                 $unitSupplyRate = 0;
//                 $unitErectionRate = 0;

//                 foreach ($items as $item) {
//                     $baseSupply = (float) ($item->supply_rate ?? 0);
//                     $supplyGst = (float) ($item->supply_gst_amount ?? 0);
//                     $unitSupplyRate += ($baseSupply + $supplyGst);

//                     $baseErection = (float) ($item->erection_rate ?? 0);
//                     $erectionGst = (float) ($item->erection_gst_amount ?? 0);
//                     $unitErectionRate += ($baseErection + $erectionGst);
//                 }

//                 // Category Total Amount = Unit Rate * Category Qty
//                 $supplyAmount = $unitSupplyRate * $categoryQty;
//                 $erectionAmount = $unitErectionRate * $categoryQty;

//                 $sectionSupplyTotal += $supplyAmount;
//                 $sectionErectionTotal += $erectionAmount;

//                 $data[] = [
//                     $slNo++,
//                     $categoryName,
//                     $unit,
//                     $categoryQty,
//                     $unitSupplyRate,
//                     $supplyAmount,
//                     $unitErectionRate,
//                     $erectionAmount
//                 ];
//                 $this->styledRows['data_rows'][] = $currentRow;
//                 $currentRow++;
//             }

//             // Section Subtotal
//             $data[] = [
//                 '', 'Total Cost of Fire Protections – Main Equipment', '', '', '',
//                 $sectionSupplyTotal,
//                 '',
//                 $sectionErectionTotal
//             ];
//             $this->styledRows['section_subtotal'][] = $currentRow;
//             $currentRow++;

//             $grandSupplyTotal += $sectionSupplyTotal;
//             $grandErectionTotal += $sectionErectionTotal;

//             $data[] = []; $currentRow++; // Spacer
//         }

//         // ----------------------------------------------------
//         // SECTION B: Spares
//         // ----------------------------------------------------
//         if (!empty($sparesItems)) {
//             $data[] = ['B. Fire Protections – Spares'];
//             $this->styledRows['section_header'][] = $currentRow;
//             $currentRow++;

//             $slNo = 1;
//             $sparesSupplyTotal = 0;
//             $sparesErectionTotal = 0;

//             foreach ($sparesItems as $categoryId => $items) {
//                 $firstItem = $items->first();
//                 $categoryName = $firstItem->projectcategries->name ?? 'Spare Item ' . $slNo;
//                 $unit = $firstItem->projectcategries->unit ?? $firstItem->unit ?? 'No.';

//                 $categoryQty = (float) ($firstItem->project_category_qty ?? 1);
//                 $categoryQty = $categoryQty > 0 ? $categoryQty : 1;

//                 $unitSupplyRate = 0;
//                 $unitErectionRate = 0;

//                 foreach ($items as $item) {
//                     $baseSupply = (float) ($item->supply_rate ?? 0);
//                     $supplyGst = (float) ($item->supply_gst_amount ?? 0);
//                     $unitSupplyRate += ($baseSupply + $supplyGst);

//                     $baseErection = (float) ($item->erection_rate ?? 0);
//                     $erectionGst = (float) ($item->erection_gst_amount ?? 0);
//                     $unitErectionRate += ($baseErection + $erectionGst);
//                 }

//                 $supplyAmount = $unitSupplyRate * $categoryQty;
//                 $erectionAmount = $unitErectionRate * $categoryQty;

//                 $sparesSupplyTotal += $supplyAmount;
//                 $sparesErectionTotal += $erectionAmount;

//                 $data[] = [
//                     $slNo++,
//                     $categoryName,
//                     $unit,
//                     $categoryQty,
//                     $unitSupplyRate,
//                     $supplyAmount,
//                     $unitErectionRate,
//                     $erectionAmount
//                 ];
//                 $this->styledRows['data_rows'][] = $currentRow;
//                 $currentRow++;
//             }

//             // Spares Subtotal
//             $data[] = [
//                 '', 'Total Cost of Fire Protections – Spares', '', '', '',
//                 $sparesSupplyTotal,
//                 '',
//                 $sparesErectionTotal
//             ];
//             $this->styledRows['section_subtotal'][] = $currentRow;
//             $currentRow++;

//             $grandSupplyTotal += $sparesSupplyTotal;
//             $grandErectionTotal += $sparesErectionTotal;

//             $data[] = []; $currentRow++; // Spacer
//         }

//         // ----------------------------------------------------
//         // GRAND TOTAL
//         // ----------------------------------------------------
//         $data[] = [
//             '', 'C. Grand Total Cost of Fire Protections + Spares', '', '', '',
//             $grandSupplyTotal,
//             '',
//             $grandErectionTotal
//         ];
//         $this->styledRows['grand_total'] = $currentRow;
//         $currentRow++;

//         $data[] = [
//             '', 'OVERALL GRAND TOTAL (Supply + Erection Inc. GST):', '', '', '',
//             $grandSupplyTotal + $grandErectionTotal
//         ];
//         $this->styledRows['overall_grand_total'] = $currentRow;

//         return $data;
//     }

//     public function styles(Worksheet $sheet)
//     {
//         // Title Header
//         $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('1B365D'));
//         $sheet->getStyle('A2:A6')->getFont()->setBold(true);

//         // Main Project Title Banner
//         if (!empty($this->styledRows['main_title'])) {
//             $row = $this->styledRows['main_title'];
//             $sheet->mergeCells("A{$row}:H{$row}");
//             $sheet->getStyle("A{$row}")->applyFromArray([
//                 'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '1E3A8A']],
//                 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
//             ]);
//         }

//         // Table Header Styling
//         if (!empty($this->styledRows['table_header'])) {
//             $row = $this->styledRows['table_header'];
//             $sheet->getRowDimension($row)->setRowHeight(28);

//             // General Header (A-D)
//             $sheet->getStyle("A{$row}:D{$row}")->applyFromArray([
//                 'font' => ['bold' => true, 'color' => ['rgb' => '212529']],
//                 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFC000']],
//                 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
//             ]);

//             // Supply Header (E-F)
//             $sheet->getStyle("E{$row}:F{$row}")->applyFromArray([
//                 'font' => ['bold' => true, 'color' => ['rgb' => '064E3B']],
//                 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'A7F3D0']],
//                 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
//             ]);

//             // Erection Header (G-H)
//             $sheet->getStyle("G{$row}:H{$row}")->applyFromArray([
//                 'font' => ['bold' => true, 'color' => ['rgb' => '7C2D12']],
//                 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFEDD5']],
//                 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
//             ]);
//         }

//         // Section Headers (A. Main Equipment, B. Spares)
//         if (!empty($this->styledRows['section_header'])) {
//             foreach ($this->styledRows['section_header'] as $row) {
//                 $sheet->getStyle("A{$row}:H{$row}")->applyFromArray([
//                     'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '1E293B']],
//                     'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E2E8F0']]
//                 ]);
//             }
//         }

//         // Data Rows Styling
//         if (!empty($this->styledRows['data_rows'])) {
//             foreach ($this->styledRows['data_rows'] as $row) {
//                 $sheet->getRowDimension($row)->setRowHeight(24);

//                 // Formatting
//                 $sheet->getStyle("E{$row}:H{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
//                 $sheet->getStyle("D{$row}")->getNumberFormat()->setFormatCode('#,##0');

//                 // Alignments
//                 $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
//                 $sheet->getStyle("B{$row}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
//                 $sheet->getStyle("C{$row}:D{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
//                 $sheet->getStyle("E{$row}:H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT)->setVertical(Alignment::VERTICAL_CENTER);

//                 // Color Tints
//                 $sheet->getStyle("E{$row}:F{$row}")->applyFromArray([
//                     'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'ECFDF5']]
//                 ]);
//                 $sheet->getStyle("G{$row}:H{$row}")->applyFromArray([
//                     'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF7ED']]
//                 ]);

//                 // Gridlines
//                 $sheet->getStyle("A{$row}:H{$row}")->applyFromArray([
//                     'borders' => [
//                         'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]
//                     ]
//                 ]);
//             }
//         }

//         // Subtotals
//         if (!empty($this->styledRows['section_subtotal'])) {
//             foreach ($this->styledRows['section_subtotal'] as $row) {
//                 $sheet->getRowDimension($row)->setRowHeight(26);
//                 $sheet->getStyle("A{$row}:H{$row}")->getFont()->setBold(true);
//                 $sheet->getStyle("E{$row}:H{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
//                 $sheet->getStyle("A{$row}:H{$row}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

//                 $sheet->getStyle("A{$row}:H{$row}")->applyFromArray([
//                     'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF08A']],
//                     'borders' => [
//                         'top' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CA8A04']],
//                         'bottom' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['rgb' => 'CA8A04']],
//                     ]
//                 ]);
//             }
//         }

//         // Grand Total Row
//         if (!empty($this->styledRows['grand_total'])) {
//             $row = $this->styledRows['grand_total'];
//             $sheet->getRowDimension($row)->setRowHeight(28);
//             $sheet->getStyle("A{$row}:H{$row}")->getFont()->setBold(true)->setSize(11);
//             $sheet->getStyle("E{$row}:H{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
//             $sheet->getStyle("A{$row}:H{$row}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

//             $sheet->getStyle("A{$row}:H{$row}")->applyFromArray([
//                 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FDE047']],
//                 'borders' => [
//                     'top' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '854D0E']],
//                     'bottom' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['rgb' => '854D0E']],
//                 ]
//             ]);
//         }

//         // Overall Grand Total (Combined)
//         if (!empty($this->styledRows['overall_grand_total'])) {
//             $row = $this->styledRows['overall_grand_total'];
//             $sheet->getRowDimension($row)->setRowHeight(28);
//             $sheet->getStyle("B{$row}:F{$row}")->getFont()->setBold(true)->setSize(12);
//             $sheet->getStyle("F{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
//             $sheet->getStyle("B{$row}:F{$row}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

//             $sheet->getStyle("B{$row}:F{$row}")->applyFromArray([
//                 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EAB308']],
//                 'font' => ['color' => ['rgb' => 'FFFFFF']]
//             ]);
//         }

//         return [];
//     }
// }



// <?php

// namespace App\Exports\Sheets;

// use Maatwebsite\Excel\Concerns\FromArray;
// use Maatwebsite\Excel\Concerns\WithTitle;
// use Maatwebsite\Excel\Concerns\WithStyles;
// use Maatwebsite\Excel\Concerns\WithColumnWidths;
// use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
// use PhpOffice\PhpSpreadsheet\Style\Border;
// use PhpOffice\PhpSpreadsheet\Style\Fill;
// use PhpOffice\PhpSpreadsheet\Style\Alignment;

// class QuotationSummarySheet implements FromArray, WithTitle, WithStyles, WithColumnWidths
// {
//     protected $quotation;
//     protected $styledRows = [];

//     public function __construct($quotation)
//     {
//         $this->quotation = $quotation;
//     }

//     public function title(): string
//     {
//         return 'Summary - Annexure 1';
//     }

//     public function columnWidths(): array
//     {
//         return [
//             'A' => 12, // Customer Sl. No.
//             'B' => 55, // ITEM DESCRIPTION
//             'C' => 8,  // Unit
//             'D' => 8,  // Qty.
//             'E' => 16, // Supply Rate
//             'F' => 18, // Supply Amount
//             'G' => 16, // Erection Rate
//             'H' => 18, // Erection Amount
//         ];
//     }

//     public function array(): array
//     {
//         $data = [];
//         $currentRow = 1;

//         // Row 1: Big Centered Header
//         $data[] = ['ANNEXURE-1', '', '', '', '', '', '', ''];
//         $this->styledRows['doc_header'] = $currentRow;
//         $currentRow++;

//         // Row 2: Ref, Date, REV
//         $offerNo = $this->quotation->quotation_no ?? 'AKE/Q/2482627';
//         $dateVal = $this->quotation->quotation_date ?? '20-07-2026';
//         $revVal = $this->quotation->rev ?? '0';
//         $data[] = ['Ref:', "Offer No. {$offerNo}", '', '', '', "Date: {$dateVal}", '', "REV - {$revVal}"];
//         $this->styledRows['meta_row_1'] = $currentRow;
//         $currentRow++;

//         // Row 3: Customer
//         $customerName = $this->quotation->customer->customer_name ?? 'M/S Bajel Projects Limited';
//         $data[] = ['Customer:', $customerName, '', '', '', '', '', ''];
//         $this->styledRows['meta_row_2'] = $currentRow;
//         $currentRow++;

//         // Row 4: Site
//         $siteLocation = $this->quotation->site_location ?? $this->quotation->project_name ?? '765/400/220 kV Lakadia-II Substation';
//         $data[] = ['Site:', $siteLocation, '', '', '', '', '', ''];
//         $this->styledRows['meta_row_3'] = $currentRow;
//         $currentRow++;

//         // Row 5: Project Title Banner
//         $data[] = ['SUPPLY, INSTALLATION, TESTING & COMMISSIONING OF FIRE PROTECTION SYSTEM :', '', '', '', '', '', '', ''];
//         $this->styledRows['main_banner'] = $currentRow;
//         $currentRow++;

//         // Row 6 & 7: 2-Tier Table Header
//         $data[] = ['Customer Sl. NO.', 'ITEM DESCRIPTION', 'Unit', 'Qty.', 'Supply', '', 'Erection', ''];
//         $this->styledRows['table_header_1'] = $currentRow;
//         $currentRow++;

//         $data[] = ['', '', '', '', 'Rate', 'Amount', 'Rate', 'Amount'];
//         $this->styledRows['table_header_2'] = $currentRow;
//         $currentRow++;

//         // Group Items by Category
//         $groupedItems = collect($this->quotation->items)->groupBy('project_category_id');

//         $mainEquipmentItems = [];
//         $sparesItems = [];

//         foreach ($groupedItems as $categoryId => $items) {
//             $firstItem = $items->first();
//             $categoryName = $firstItem->projectcategries->name ?? 'General Category';
//             $isSpare = str_contains(strtolower($categoryName), 'spare');

//             if ($isSpare) {
//                 $sparesItems[$categoryId] = $items;
//             } else {
//                 $mainEquipmentItems[$categoryId] = $items;
//             }
//         }

//         $grandSupplyTotal = 0;
//         $grandErectionTotal = 0;

//         // ----------------------------------------------------
//         // SECTION A: Main Equipment
//         // ----------------------------------------------------
//         if (!empty($mainEquipmentItems)) {
//             $data[] = ['A', 'Fire Protections – Main Equipment', '', '', '', '', '', ''];
//             $this->styledRows['section_header'][] = $currentRow;
//             $currentRow++;

//             $slNo = 1;
//             $sectionSupplyTotal = 0;
//             $sectionErectionTotal = 0;

//             foreach ($mainEquipmentItems as $categoryId => $items) {
//                 $firstItem = $items->first();
//                 $categoryName = $firstItem->projectcategries->name ?? 'Category ' . $slNo;
//                 $unit = $firstItem->projectcategries->unit ?? $firstItem->unit ?? 'SET';

//                 $categoryQty = (float) ($firstItem->project_category_qty ?? 1);
//                 $categoryQty = $categoryQty > 0 ? $categoryQty : 1;

//                 $unitSupplyRate = 0;
//                 $unitErectionRate = 0;

//                 foreach ($items as $item) {
//                     $baseSupply = (float) ($item->supply_rate ?? 0);
//                     $supplyGst = (float) ($item->supply_gst_amount ?? 0);
//                     $unitSupplyRate += ($baseSupply + $supplyGst);

//                     $baseErection = (float) ($item->erection_rate ?? 0);
//                     $erectionGst = (float) ($item->erection_gst_amount ?? 0);
//                     $unitErectionRate += ($baseErection + $erectionGst);
//                 }

//                 $supplyAmount = $unitSupplyRate * $categoryQty;
//                 $erectionAmount = $unitErectionRate * $categoryQty;

//                 $sectionSupplyTotal += $supplyAmount;
//                 $sectionErectionTotal += $erectionAmount;

//                 $data[] = [
//                     $slNo++,
//                     $categoryName,
//                     $unit,
//                     $categoryQty,
//                     $unitSupplyRate,
//                     $supplyAmount,
//                     $unitErectionRate,
//                     $erectionAmount
//                 ];
//                 $this->styledRows['data_rows'][] = $currentRow;
//                 $currentRow++;
//             }

//             // Section Subtotal
//             $data[] = [
//                 '', 'Total Cost of Fire Protections – Main Equipment', '', '', '',
//                 $sectionSupplyTotal,
//                 '',
//                 $sectionErectionTotal
//             ];
//             $this->styledRows['section_subtotal'][] = $currentRow;
//             $currentRow++;

//             $grandSupplyTotal += $sectionSupplyTotal;
//             $grandErectionTotal += $sectionErectionTotal;
//         }

//         // ----------------------------------------------------
//         // SECTION B: Spares
//         // ----------------------------------------------------
//         if (!empty($sparesItems)) {
//             $data[] = ['B', 'Fire Protections – Spares', '', '', '', '', '', ''];
//             $this->styledRows['section_header'][] = $currentRow;
//             $currentRow++;

//             $slNo = 1;
//             $sparesSupplyTotal = 0;
//             $sparesErectionTotal = 0;

//             foreach ($sparesItems as $categoryId => $items) {
//                 $firstItem = $items->first();
//                 $categoryName = $firstItem->projectcategries->name ?? 'Spare Item ' . $slNo;
//                 $unit = $firstItem->projectcategries->unit ?? $firstItem->unit ?? 'No.';

//                 $categoryQty = (float) ($firstItem->project_category_qty ?? 1);
//                 $categoryQty = $categoryQty > 0 ? $categoryQty : 1;

//                 $unitSupplyRate = 0;
//                 $unitErectionRate = 0;

//                 foreach ($items as $item) {
//                     $baseSupply = (float) ($item->supply_rate ?? 0);
//                     $supplyGst = (float) ($item->supply_gst_amount ?? 0);
//                     $unitSupplyRate += ($baseSupply + $supplyGst);

//                     $baseErection = (float) ($item->erection_rate ?? 0);
//                     $erectionGst = (float) ($item->erection_gst_amount ?? 0);
//                     $unitErectionRate += ($baseErection + $erectionGst);
//                 }

//                 $supplyAmount = $unitSupplyRate * $categoryQty;
//                 $erectionAmount = $unitErectionRate * $categoryQty;

//                 $sparesSupplyTotal += $supplyAmount;
//                 $sparesErectionTotal += $erectionAmount;

//                 $data[] = [
//                     $slNo++,
//                     $categoryName,
//                     $unit,
//                     $categoryQty,
//                     $unitSupplyRate,
//                     $supplyAmount,
//                     $unitErectionRate,
//                     $erectionAmount
//                 ];
//                 $this->styledRows['data_rows'][] = $currentRow;
//                 $currentRow++;
//             }

//             // Spares Subtotal
//             $data[] = [
//                 '', 'Total Cost of Fire Protections – Spares', '', '', '',
//                 $sparesSupplyTotal,
//                 '',
//                 $sparesErectionTotal
//             ];
//             $this->styledRows['section_subtotal'][] = $currentRow;
//             $currentRow++;

//             $grandSupplyTotal += $sparesSupplyTotal;
//             $grandErectionTotal += $sparesErectionTotal;
//         }

//         // ----------------------------------------------------
//         // GRAND TOTAL
//         // ----------------------------------------------------
//         $data[] = [
//             '', 'Grand Total Cost of Fire Protections + Spares', '', '', '',
//             $grandSupplyTotal,
//             '',
//             $grandErectionTotal
//         ];
//         $this->styledRows['grand_total'] = $currentRow;

//         return $data;
//     }

//     public function styles(Worksheet $sheet)
//     {
//         $thinBorder = [
//             'borders' => [
//                 'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]
//             ]
//         ];

//         // 1. Doc Title (ANNEXURE-1)
//         if (!empty($this->styledRows['doc_header'])) {
//             $row = $this->styledRows['doc_header'];
//             $sheet->mergeCells("A{$row}:H{$row}");
//             $sheet->getRowDimension($row)->setRowHeight(30);
//             $sheet->getStyle("A{$row}")->applyFromArray([
//                 'font' => ['bold' => true, 'size' => 18, 'name' => 'Calibri'],
//                 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
//             ]);
//             $sheet->getStyle("A{$row}:H{$row}")->applyFromArray($thinBorder);
//         }

//         // 2. Metadata Rows (Ref, Date, Customer, Site)
//         $metaRows = [$this->styledRows['meta_row_1'], $this->styledRows['meta_row_2'], $this->styledRows['meta_row_3']];
//         foreach ($metaRows as $row) {
//             $sheet->getRowDimension($row)->setRowHeight(20);
//             $sheet->getStyle("A{$row}:H{$row}")->getFont()->setBold(true)->setSize(11);
//             $sheet->getStyle("A{$row}:H{$row}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
//             $sheet->getStyle("A{$row}:H{$row}")->applyFromArray($thinBorder);
//         }

//         // Specific Metadata Merges
//         $r1 = $this->styledRows['meta_row_1'];
//         $sheet->mergeCells("B{$r1}:D{$r1}");
//         $sheet->mergeCells("F{$r1}:G{$r1}");

//         $r2 = $this->styledRows['meta_row_2'];
//         $sheet->mergeCells("B{$r2}:H{$r2}");

//         $r3 = $this->styledRows['meta_row_3'];
//         $sheet->mergeCells("B{$r3}:D{$r3}");

//         // 3. Main Title Banner (SUPPLY, INSTALLATION...)
//         if (!empty($this->styledRows['main_banner'])) {
//             $row = $this->styledRows['main_banner'];
//             $sheet->mergeCells("A{$row}:H{$row}");
//             $sheet->getRowDimension($row)->setRowHeight(24);
//             $sheet->getStyle("A{$row}")->applyFromArray([
//                 'font' => ['bold' => true, 'size' => 12],
//                 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
//             ]);
//             $sheet->getStyle("A{$row}:H{$row}")->applyFromArray($thinBorder);
//         }

//         // 4. Double-Decker Table Headers
//         $h1 = $this->styledRows['table_header_1'];
//         $h2 = $this->styledRows['table_header_2'];

//         // Merges for Header
//         $sheet->mergeCells("A{$h1}:A{$h2}"); // Customer Sl. NO.
//         $sheet->mergeCells("B{$h1}:B{$h2}"); // ITEM DESCRIPTION
//         $sheet->mergeCells("C{$h1}:C{$h2}"); // Unit
//         $sheet->mergeCells("D{$h1}:D{$h2}"); // Qty.
//         $sheet->mergeCells("E{$h1}:F{$h1}"); // Supply
//         $sheet->mergeCells("G{$h1}:H{$h1}"); // Erection

//         $sheet->getRowDimension($h1)->setRowHeight(22);
//         $sheet->getRowDimension($h2)->setRowHeight(22);

//         // Header Styling (Light Green Fill - E2EFDA)
//         $sheet->getStyle("A{$h1}:H{$h2}")->applyFromArray([
//             'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '000000']],
//             'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E2EFDA']],
//             'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
//             'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
//         ]);

//         // 5. Section Header Styling (Magenta / Bright Pink - FF66FF)
//         if (!empty($this->styledRows['section_header'])) {
//             foreach ($this->styledRows['section_header'] as $row) {
//                 $sheet->getRowDimension($row)->setRowHeight(22);
//                 $sheet->mergeCells("B{$row}:H{$row}");
//                 $sheet->getStyle("A{$row}:H{$row}")->applyFromArray([
//                     'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '000000']],
//                     'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FF66FF']],
//                     'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
//                     'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
//                 ]);
//                 $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
//             }
//         }

//         // 6. Data Rows Styling
//         if (!empty($this->styledRows['data_rows'])) {
//             foreach ($this->styledRows['data_rows'] as $row) {
//                 $sheet->getRowDimension($row)->setRowHeight(35); // Heights for wrapped text

//                 // Formatting
//                 $sheet->getStyle("E{$row}:H{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
//                 $sheet->getStyle("D{$row}")->getNumberFormat()->setFormatCode('#,##0');

//                 // Alignments
//                 $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
//                 $sheet->getStyle("B{$row}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);
//                 $sheet->getStyle("C{$row}:D{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
//                 $sheet->getStyle("E{$row}:H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT)->setVertical(Alignment::VERTICAL_CENTER);

//                 // Amounts Bold (Col F & H)
//                 $sheet->getStyle("F{$row}")->getFont()->setBold(true);
//                 $sheet->getStyle("H{$row}")->getFont()->setBold(true);

//                 // Gridlines
//                 $sheet->getStyle("A{$row}:H{$row}")->applyFromArray($thinBorder);
//             }
//         }

//         // 7. Subtotals & Grand Total
//         $summaryRows = array_merge($this->styledRows['section_subtotal'] ?? [], [$this->styledRows['grand_total'] ?? null]);
//         foreach (array_filter($summaryRows) as $row) {
//             $sheet->getRowDimension($row)->setRowHeight(24);
//             $sheet->getStyle("A{$row}:H{$row}")->getFont()->setBold(true);
//             $sheet->getStyle("E{$row}:H{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
//             $sheet->getStyle("A{$row}:H{$row}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
//             $sheet->getStyle("E{$row}:H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
//             $sheet->getStyle("A{$row}:H{$row}")->applyFromArray($thinBorder);
//         }

//         return [];
//     }
// }

// <?php
// <?php
// <?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class QuotationSummarySheet implements FromArray, WithTitle, WithStyles, WithColumnWidths, WithEvents
{
    protected $quotation;
    protected $styledRows = [];

    public function __construct($quotation)
    {
        $this->quotation = $quotation;
    }

    public function title(): string
    {
        return 'Summary - Annexure 1';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12, // Customer Sl. No.
            'B' => 55, // ITEM DESCRIPTION
            'C' => 10, // Unit
            'D' => 10, // Qty.
            'E' => 18, // Supply Rate
            'F' => 20, // Supply Amount
            'G' => 18, // Erection Rate
            'H' => 20, // Erection Amount
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Page Margins (Padding for Print/PDF)
                $sheet->getPageMargins()->setTop(0.5);
                $sheet->getPageMargins()->setRight(0.4);
                $sheet->getPageMargins()->setLeft(0.4);
                $sheet->getPageMargins()->setBottom(0.5);

                // Page Orientation & Fit to Page
                $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);
            },
        ];
    }

    public function array(): array
    {
        $data = [];
        $currentRow = 1;

        // Row 1: Document Header
        $data[] = ['ANNEXURE-1', '', '', '', '', '', '', ''];
        $this->styledRows['doc_header'] = $currentRow;
        $currentRow++;

        // Row 2: Ref, Date, REV
        $offerNo = $this->quotation->quotation_no ?? 'AKE/Q/2482627';
        $dateVal = $this->quotation->quotation_date ?? '20-07-2026';
        $revVal = $this->quotation->rev ?? '0';
        $data[] = ['Ref:', "Offer No. {$offerNo}", '', '', '', "Date: {$dateVal}", '', "REV - {$revVal}"];
        $this->styledRows['meta_row_1'] = $currentRow;
        $currentRow++;

        // Row 3: Customer
        $customerName = $this->quotation->customer->customer_name ?? 'M/S Bajel Projects Limited';
        $data[] = ['Customer:', $customerName, '', '', '', '', '', ''];
        $this->styledRows['meta_row_2'] = $currentRow;
        $currentRow++;

        // Row 4: Site
        $siteLocation = $this->quotation->site_location ?? $this->quotation->project_name ?? '765/400/220 kV Lakadia-II Substation';
        $data[] = ['Site:', $siteLocation, '', '', '', '', '', ''];
        $this->styledRows['meta_row_3'] = $currentRow;
        $currentRow++;

        // Row 5: Main Title Banner
        $data[] = ['SUPPLY, INSTALLATION, TESTING & COMMISSIONING OF FIRE PROTECTION SYSTEM :', '', '', '', '', '', '', ''];
        $this->styledRows['main_banner'] = $currentRow;
        $currentRow++;

        // Row 6 & 7: Table Headers
        $data[] = ['Customer Sl. NO.', 'ITEM DESCRIPTION', 'Unit', 'Qty.', 'Supply', '', 'Erection', ''];
        $this->styledRows['table_header_1'] = $currentRow;
        $currentRow++;

        $data[] = ['', '', '', '', 'Rate', 'Amount', 'Rate', 'Amount'];
        $this->styledRows['table_header_2'] = $currentRow;
        $currentRow++;

        // Group Items
        $groupedItems = collect($this->quotation->items)->groupBy('project_category_id');

        $mainEquipmentItems = [];
        $sparesItems = [];

        foreach ($groupedItems as $categoryId => $items) {
            $firstItem = $items->first();
            $categoryName = $firstItem->projectcategries->name ?? 'General Category';
            $isSpare = str_contains(strtolower($categoryName), 'spare');

            if ($isSpare) {
                $sparesItems[$categoryId] = $items;
            } else {
                $mainEquipmentItems[$categoryId] = $items;
            }
        }

        $grandSupplyTotal = 0;
        $grandErectionTotal = 0;

        // ----------------------------------------------------
        // SECTION A: Main Equipment
        // ----------------------------------------------------
        if (!empty($mainEquipmentItems)) {
            $data[] = ['A', 'Fire Protections – Main Equipment', '', '', '', '', '', ''];
            $this->styledRows['section_header'][] = $currentRow;
            $currentRow++;

            $slNo = 1;
            $sectionSupplyTotal = 0;
            $sectionErectionTotal = 0;

            foreach ($mainEquipmentItems as $categoryId => $items) {
                $firstItem = $items->first();
                $categoryName = $firstItem->projectcategries->name ?? 'Category ' . $slNo;
                $unit = $firstItem->projectcategries->unit ?? $firstItem->unit ?? 'SET';

                $categoryQty = (float) ($firstItem->project_category_qty ?? 1);
                $categoryQty = $categoryQty > 0 ? $categoryQty : 1;

                $unitSupplyRate = 0;
                $unitErectionRate = 0;

                foreach ($items as $item) {
                    $baseSupply = (float) ($item->supply_rate ?? 0);
                    $supplyGst = (float) ($item->supply_gst_amount ?? 0);
                    $unitSupplyRate += ($baseSupply + $supplyGst);

                    $baseErection = (float) ($item->erection_rate ?? 0);
                    $erectionGst = (float) ($item->erection_gst_amount ?? 0);
                    $unitErectionRate += ($baseErection + $erectionGst);
                }

                $supplyAmount = $unitSupplyRate * $categoryQty;
                $erectionAmount = $unitErectionRate * $categoryQty;

                $sectionSupplyTotal += $supplyAmount;
                $sectionErectionTotal += $erectionAmount;

                $data[] = [
                    $slNo++,
                    $categoryName,
                    $unit,
                    $categoryQty,
                    $unitSupplyRate,
                    $supplyAmount,
                    $unitErectionRate,
                    $erectionAmount
                ];
                $this->styledRows['data_rows'][] = $currentRow;
                $currentRow++;
            }

            // Subtotal Section A
            $data[] = [
                '', 'Total Cost of Fire Protections – Main Equipment', '', '', '',
                $sectionSupplyTotal,
                '',
                $sectionErectionTotal
            ];
            $this->styledRows['section_subtotal'][] = $currentRow;
            $currentRow++;

            $grandSupplyTotal += $sectionSupplyTotal;
            $grandErectionTotal += $sectionErectionTotal;
        }

        // ----------------------------------------------------
        // SECTION B: Spares
        // ----------------------------------------------------
        if (!empty($sparesItems)) {
            $data[] = ['B', 'Fire Protections – Spares', '', '', '', '', '', ''];
            $this->styledRows['section_header'][] = $currentRow;
            $currentRow++;

            $slNo = 1;
            $sparesSupplyTotal = 0;
            $sparesErectionTotal = 0;

            foreach ($sparesItems as $categoryId => $items) {
                $firstItem = $items->first();
                $categoryName = $firstItem->projectcategries->name ?? 'Spare Item ' . $slNo;
                $unit = $firstItem->projectcategries->unit ?? $firstItem->unit ?? 'No.';

                $categoryQty = (float) ($firstItem->project_category_qty ?? 1);
                $categoryQty = $categoryQty > 0 ? $categoryQty : 1;

                $unitSupplyRate = 0;
                $unitErectionRate = 0;

                foreach ($items as $item) {
                    $baseSupply = (float) ($item->supply_rate ?? 0);
                    $supplyGst = (float) ($item->supply_gst_amount ?? 0);
                    $unitSupplyRate += ($baseSupply + $supplyGst);

                    $baseErection = (float) ($item->erection_rate ?? 0);
                    $erectionGst = (float) ($item->erection_gst_amount ?? 0);
                    $unitErectionRate += ($baseErection + $erectionGst);
                }

                $supplyAmount = $unitSupplyRate * $categoryQty;
                $erectionAmount = $unitErectionRate * $categoryQty;

                $sparesSupplyTotal += $supplyAmount;
                $sparesErectionTotal += $erectionAmount;

                $data[] = [
                    $slNo++,
                    $categoryName,
                    $unit,
                    $categoryQty,
                    $unitSupplyRate,
                    $supplyAmount,
                    $unitErectionRate,
                    $erectionAmount
                ];
                $this->styledRows['data_rows'][] = $currentRow;
                $currentRow++;
            }

            // Subtotal Section B
            $data[] = [
                '', 'Total Cost of Fire Protections – Spares', '', '', '',
                $sparesSupplyTotal,
                '',
                $sparesErectionTotal
            ];
            $this->styledRows['section_subtotal'][] = $currentRow;
            $currentRow++;

            $grandSupplyTotal += $sparesSupplyTotal;
            $grandErectionTotal += $sparesErectionTotal;
        }

        // ----------------------------------------------------
        // GRAND TOTALS
        // ----------------------------------------------------
        // 1. Column-wise Grand Total
        $data[] = [
            'C', 'Grand Total Cost of Fire Protections + Spares', '', '', '',
            $grandSupplyTotal,
            '',
            $grandErectionTotal
        ];
        $this->styledRows['grand_total'] = $currentRow;
        $currentRow++;

        // 2. OVERALL COMBINED GRAND TOTAL (Supply Amount + Erection Amount)
        $overallGrandTotal = $grandSupplyTotal + $grandErectionTotal;
        $data[] = [
            '', 'OVERALL GRAND TOTAL (Supply Amount + Erection Amount Inc. GST):', '', '', '',
            $overallGrandTotal,
            '',
            ''
        ];
        $this->styledRows['overall_grand_total'] = $currentRow;

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        $thinBorder = [
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]
            ]
        ];

        // 1. Header (ANNEXURE-1)
        if (!empty($this->styledRows['doc_header'])) {
            $row = $this->styledRows['doc_header'];
            $sheet->mergeCells("A{$row}:H{$row}");
            $sheet->getRowDimension($row)->setRowHeight(32);
            $sheet->getStyle("A{$row}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 18, 'name' => 'Calibri'],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
            ]);
            $sheet->getStyle("A{$row}:H{$row}")->applyFromArray($thinBorder);
        }

        // 2. Metadata Rows
        $metaRows = [$this->styledRows['meta_row_1'], $this->styledRows['meta_row_2'], $this->styledRows['meta_row_3']];
        foreach ($metaRows as $row) {
            $sheet->getRowDimension($row)->setRowHeight(22);
            $sheet->getStyle("A{$row}:H{$row}")->getFont()->setBold(true)->setSize(11);
            $sheet->getStyle("A{$row}:H{$row}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setIndent(1);
            $sheet->getStyle("A{$row}:H{$row}")->applyFromArray($thinBorder);
        }

        $r1 = $this->styledRows['meta_row_1'];
        $sheet->mergeCells("B{$r1}:D{$r1}");
        $sheet->mergeCells("F{$r1}:G{$r1}");

        $r2 = $this->styledRows['meta_row_2'];
        $sheet->mergeCells("B{$r2}:H{$r2}");

        $r3 = $this->styledRows['meta_row_3'];
        $sheet->mergeCells("B{$r3}:D{$r3}");

        // 3. Main Title Banner
        if (!empty($this->styledRows['main_banner'])) {
            $row = $this->styledRows['main_banner'];
            $sheet->mergeCells("A{$row}:H{$row}");
            $sheet->getRowDimension($row)->setRowHeight(26);
            $sheet->getStyle("A{$row}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 12],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
            ]);
            $sheet->getStyle("A{$row}:H{$row}")->applyFromArray($thinBorder);
        }

        // 4. Double-Decker Table Headers
        $h1 = $this->styledRows['table_header_1'];
        $h2 = $this->styledRows['table_header_2'];

        $sheet->mergeCells("A{$h1}:A{$h2}");
        $sheet->mergeCells("B{$h1}:B{$h2}");
        $sheet->mergeCells("C{$h1}:C{$h2}");
        $sheet->mergeCells("D{$h1}:D{$h2}");
        $sheet->mergeCells("E{$h1}:F{$h1}");
        $sheet->mergeCells("G{$h1}:H{$h1}");

        $sheet->getRowDimension($h1)->setRowHeight(22);
        $sheet->getRowDimension($h2)->setRowHeight(22);

        $sheet->getStyle("A{$h1}:H{$h2}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '000000']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E2EFDA']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
        ]);

        // 5. Section Headers (Magenta Pink)
        if (!empty($this->styledRows['section_header'])) {
            foreach ($this->styledRows['section_header'] as $row) {
                $sheet->getRowDimension($row)->setRowHeight(24);
                $sheet->mergeCells("B{$row}:H{$row}");
                $sheet->getStyle("A{$row}:H{$row}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '000000']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FF66FF']],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
                ]);
                $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("B{$row}")->getAlignment()->setIndent(1);
            }
        }

        // 6. Data Rows
        if (!empty($this->styledRows['data_rows'])) {
            foreach ($this->styledRows['data_rows'] as $row) {
                $sheet->getRowDimension($row)->setRowHeight(38);

                $sheet->getStyle("E{$row}:H{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle("D{$row}")->getNumberFormat()->setFormatCode('#,##0');

                $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle("B{$row}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true)->setIndent(1);
                $sheet->getStyle("C{$row}:D{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle("E{$row}:H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT)->setVertical(Alignment::VERTICAL_CENTER);

                $sheet->getStyle("F{$row}")->getFont()->setBold(true);
                $sheet->getStyle("H{$row}")->getFont()->setBold(true);

                $sheet->getStyle("A{$row}:H{$row}")->applyFromArray($thinBorder);
            }
        }

        // 7. Subtotals
        if (!empty($this->styledRows['section_subtotal'])) {
            foreach ($this->styledRows['section_subtotal'] as $row) {
                $sheet->getRowDimension($row)->setRowHeight(26);
                $sheet->getStyle("A{$row}:H{$row}")->getFont()->setBold(true);
                $sheet->getStyle("E{$row}:H{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle("B{$row}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setIndent(1);
                $sheet->getStyle("E{$row}:H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT)->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle("A{$row}:H{$row}")->applyFromArray($thinBorder);
            }
        }

        // 8. Column-wise Grand Total (Row C)
        if (!empty($this->styledRows['grand_total'])) {
            $row = $this->styledRows['grand_total'];
            $sheet->getRowDimension($row)->setRowHeight(28);
            $sheet->getStyle("A{$row}:H{$row}")->getFont()->setBold(true)->setSize(11);
            $sheet->getStyle("E{$row}:H{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("B{$row}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setIndent(1);
            $sheet->getStyle("E{$row}:H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT)->setVertical(Alignment::VERTICAL_CENTER);

            $sheet->getStyle("A{$row}:H{$row}")->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF2CC']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
            ]);
        }

        // 9. OVERALL COMBINED GRAND TOTAL
        if (!empty($this->styledRows['overall_grand_total'])) {
            $row = $this->styledRows['overall_grand_total'];

            $sheet->mergeCells("A{$row}:E{$row}");
            $sheet->mergeCells("F{$row}:H{$row}");

            $sheet->getRowDimension($row)->setRowHeight(34);

            $sheet->getStyle("A{$row}:H{$row}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '000000']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFC000']],
                'borders' => [
                    'top' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                    'bottom' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['rgb' => '000000']],
                    'left' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                    'right' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                ]
            ]);

            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT)->setVertical(Alignment::VERTICAL_CENTER)->setIndent(1);
            $sheet->getStyle("F{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("F{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
        }

        return [];
    }
}
