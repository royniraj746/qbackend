<?php

namespace App\Exports\Sheets;

use App\Models\Quotation;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CoverPageSheet implements
    FromArray,
    WithStyles,
    WithColumnWidths,
    WithEvents
{
    protected Quotation $quotation;

    protected array $calculatedItems = [];

    protected float $totalSupplyRate = 0;
    protected float $totalSupplyAmount = 0;
    protected float $totalSupplyGST = 0;

    protected float $totalErectionRate = 0;
    protected float $totalErectionAmount = 0;
    protected float $totalErectionGST = 0;

    protected float $grandTotal = 0;

    public function __construct(Quotation $quotation)
    {
        $this->quotation = $quotation;

        $this->quotation->loadMissing([
            'items.product',
            'items.projectcategries',
            'customer',
            'enquiry',
        ]);

        $this->calculateTotals();
    }

    /**
     * -------------------------------------------------------------
     * Calculate Supply / Erection
     * -------------------------------------------------------------
     */
    protected function calculateTotals(): void
    {
        $groupedItems = $this->quotation->items
            ->groupBy('project_category_id');

        foreach ($groupedItems as $categoryId => $items) {

            /** @var Collection $items */

            $firstItem = $items->first();

            /*
             * Category Quantity
             */
            $categoryQty = (float) (
                $firstItem->project_category_qty ?? 1
            );

            if ($categoryQty <= 0) {
                $categoryQty = 1;
            }

            /*
             * ---------------------------------------------------------
             * SUPPLY
             * ---------------------------------------------------------
             */

            $categorySupplyRate = 0;
            $categorySupplyGST = 0;

            foreach ($items as $item) {

                $categorySupplyRate += (float) (
                    $item->supply_rate ?? 0
                );

                $categorySupplyGST += (float) (
                    $item->supply_gst_amount ?? 0
                );
            }

            /*
             * Supply Rate including GST
             */
            $supplyRateWithGST =
                $categorySupplyRate +
                $categorySupplyGST;

            /*
             * Supply Amount
             */
            $supplyAmount =
                $supplyRateWithGST *
                $categoryQty;

            /*
             * GST amount for category
             */
            $supplyGSTAmount =
                $categorySupplyGST *
                $categoryQty;


            /*
             * ---------------------------------------------------------
             * ERECTION
             * ---------------------------------------------------------
             */

            $categoryErectionRate = 0;
            $categoryErectionGST = 0;

            foreach ($items as $item) {

                $categoryErectionRate += (float) (
                    $item->erection_rate ?? 0
                );

                $categoryErectionGST += (float) (
                    $item->erection_gst_amount ?? 0
                );
            }

            /*
             * Erection Rate including GST
             */
            $erectionRateWithGST =
                $categoryErectionRate +
                $categoryErectionGST;

            /*
             * Erection Amount
             */
            $erectionAmount =
                $erectionRateWithGST *
                $categoryQty;

            /*
             * GST amount for category
             */
            $erectionGSTAmount =
                $categoryErectionGST *
                $categoryQty;


            /*
             * ---------------------------------------------------------
             * TOTALS
             * ---------------------------------------------------------
             */

            $this->totalSupplyRate += $supplyRateWithGST;
            $this->totalSupplyAmount += $supplyAmount;
            $this->totalSupplyGST += $supplyGSTAmount;

            $this->totalErectionRate += $erectionRateWithGST;
            $this->totalErectionAmount += $erectionAmount;
            $this->totalErectionGST += $erectionGSTAmount;


            /*
             * Save calculated category
             */
            $this->calculatedItems[] = [
                'project_category_id' => $categoryId,

                'category_name' =>
                    $firstItem->projectcategries->name
                    ?? 'General Category',

                'unit' =>
                    $firstItem->projectcategries->unit
                    ?? $firstItem->unit
                    ?? 'SET',

                'category_qty' => $categoryQty,

                'supply_rate' =>
                    round($supplyRateWithGST, 2),

                'supply_amount' =>
                    round($supplyAmount, 2),

                'supply_gst_amount' =>
                    round($supplyGSTAmount, 2),

                'erection_rate' =>
                    round($erectionRateWithGST, 2),

                'erection_amount' =>
                    round($erectionAmount, 2),

                'erection_gst_amount' =>
                    round($erectionGSTAmount, 2),
            ];
        }

        /*
         * -------------------------------------------------------------
         * GRAND TOTAL
         * -------------------------------------------------------------
         *
         * GST is already included inside Supply/Erection amounts.
         *
         * Therefore:
         *
         * Grand Total =
         * Supply Amount + Erection Amount + Round Off
         */

        $roundOff = (float) (
            $this->quotation->round_off ?? 0
        );

        $this->grandTotal =
            $this->totalSupplyAmount +
            $this->totalErectionAmount +
            $roundOff;
    }

    /**
     * -------------------------------------------------------------
     * Excel Data
     * -------------------------------------------------------------
     */
    public function array(): array
    {
        $customer = $this->quotation->customer;

        $quotationDate = $this->quotation->quotation_date
            ? date(
                'd F Y',
                strtotime($this->quotation->quotation_date)
            )
            : '-';

        $roundOff = (float) (
            $this->quotation->round_off ?? 0
        );

        $rows = [];

        /*
         * Row 1
         */
        $rows[] = [
            'QUOTATION',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
        ];

        /*
         * Row 2
         */
        $rows[] = [
            'For',
            $this->quotation->project_title
                ?? 'REFILLING OF FIRE EXTINGUISHERS FOR PGCIL, GANGTOK 132/66 KV SUB-STATION',
            '',
            '',
            'Quotation Ref. Number:',
            $this->quotation->quotation_no ?? '-',
            'REV:',
            $this->quotation->revision ?? '0',
        ];

        /*
         * Row 3
         */
        $rows[] = [
            '',
            '',
            '',
            '',
            'Quotation Date:',
            $quotationDate,
            '',
            '',
        ];

        /*
         * Row 4
         */
        $rows[] = [
            'Location of worksite:',
            $this->quotation->location ?? '-',
            '',
            '',
            'Ref:',
            $this->quotation->reference_note
                ?? 'Verbal discussion had our Mr. Partha Saha with your good self.',
            '',
            '',
        ];

        /*
         * Row 5
         */
        $rows[] = [''];

        /*
         * Customer
         */
        $rows[] = [
            'DETAILS OF THE CUSTOMER :',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
        ];

        $rows[] = [
            'Name :',
            $customer->customer_name ?? '-',
            'GSTIN :',
            $customer->gst ?? '-',
            '',
            '',
            '',
            '',
        ];

        $rows[] = [
            'Address :',
            $customer->address ?? '-',
            'Contact Person :',
            $customer->contact_person ?? '-',
            '',
            '',
            '',
            '',
        ];

        $rows[] = [
            'State :',
            $customer->state ?? '-',
            'Mobile :',
            $customer->mobile ?? '-',
            '',
            '',
            '',
            '',
        ];

        $rows[] = [
            'Pin Code :',
            $customer->pincode ?? '-',
            'Email Id :',
            $customer->email ?? '-',
            '',
            '',
            '',
            '',
        ];

        /*
         * Blank
         */
        $rows[] = [''];

        /*
         * Terms
         */
        $rows[] = [
            'TERMS AND CONDITIONS :',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
        ];

        $rows[] = [
            '1)',
            'Any additional material / manpower required shall be charged extra.',
            '',
            '',
            '',
            '',
            '',
            '',
        ];

        $rows[] = [
            '2)',
            'Any spare or accessories replacement shall be charged extra.',
            '',
            '',
            '',
            '',
            '',
            '',
        ];

        $rows[] = [
            '3)',
            'All refilling works shall be done at site except CO2 type Fire Extinguishers.',
            '',
            '',
            '',
            '',
            '',
            '',
        ];

        $rows[] = [
            'Payment Terms :',
            '100% of Total Value plus GST to be released against completion.',
            '',
            '',
            '',
            '',
            '',
            '',
        ];

        $rows[] = [
            'Completion :',
            'Within 4 to 6 weeks from P.O./Confirmation.',
            '',
            '',
            '',
            '',
            '',
            '',
        ];

        $rows[] = [
            'Refilling Validity :',
            'As per IS norms.',
            '',
            '',
            '',
            '',
            '',
            '',
        ];

        $rows[] = [
            'Quotation Validity :',
            '30 Days.',
            '',
            '',
            '',
            '',
            '',
            '',
        ];

        /*
         * Blank
         */
        $rows[] = [''];

        /*
         * Summary
         */
        $rows[] = [
            'SUMMARY OF PRICES QUOTED :',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
        ];

        /*
         * Supply Header
         */
        $rows[] = [
            'A. SUPPLY :',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
        ];

        /*
         * Supply Table Header
         */
        $rows[] = [
            'SL No.',
            'Descriptions',
            'Value of Goods in Rs.',
            'GST Rate',
            'GST Amt in Rs.',
            '',
            '',
            '',
        ];

        /*
         * Supply
         */
        $rows[] = [
            1,
            $this->quotation->supply_description
                ?? 'Supply of Fire Protection Equipment',
            round($this->totalSupplyAmount, 2),
            '18%',
            '-',
            '',
            '',
            '',
        ];

        /*
         * Supply Total
         */
        $rows[] = [
            '',
            'GROUP TOTAL (A)',
            round($this->totalSupplyAmount, 2),
            '',
            '-',
            '',
            '',
            '',
        ];

        /*
         * Erection Header
         */
        $rows[] = [
            'B. ERECTION, INSTALLATION, TESTING & COMMISSIONING :',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
        ];

        /*
         * Erection Table Header
         */
        $rows[] = [
            'SL No.',
            'Descriptions',
            'Value of Services in Rs.',
            'GST Rate',
            'GST Amt in Rs.',
            '',
            '',
            '',
        ];

        /*
         * Erection
         */
        $rows[] = [
            2,
            $this->quotation->erection_description
                ?? 'Erection, Installation, Testing & Commissioning',
            round($this->totalErectionAmount, 2),
            '18%',
            '-',
            '',
            '',
            '',
        ];

        /*
         * Erection Total
         */
        $rows[] = [
            '',
            'GROUP TOTAL (B)',
            round($this->totalErectionAmount, 2),
            '',
            '-',
            '',
            '',
            '',
        ];

        /*
         * Grand Total
         */
        $grandSubtotal =
            $this->totalSupplyAmount +
            $this->totalErectionAmount;

        $rows[] = [
            '',
            'GRAND TOTAL (A+B)',
            round($grandSubtotal, 2),
            '',
            '-',
            '',
            '',
            '',
        ];

        /*
         * Blank
         */
        $rows[] = [''];

        /*
         * Total Box
         */
        $rows[] = [
            '',
            'SUPPLY AMOUNT :',
            round($this->totalSupplyAmount, 2),
            '',
            '',
            '',
            '',
            '',
        ];

        $rows[] = [
            '',
            'ERECTION AMOUNT :',
            round($this->totalErectionAmount, 2),
            '',
            '',
            '',
            '',
            '',
        ];

        $rows[] = [
            '',
            'TOTAL VALUE :',
            round($grandSubtotal, 2),
            '',
            '',
            '',
            '',
            '',
        ];

        $rows[] = [
            '',
            'ROUND OFF :',
            round($roundOff, 2),
            '',
            '',
            '',
            '',
            '',
        ];

        $rows[] = [
            '',
            'GRAND TOTAL :',
            round($this->grandTotal, 2),
            '',
            '',
            '',
            '',
            '',
        ];

        /*
         * Blank
         */
        $rows[] = [''];

        /*
         * Footer
         */
        $rows[] = [
            'Thanking you,',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
        ];

        $rows[] = [
            'For A. K. Enterprises',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
        ];

        $rows[] = [
            'A. K. Enterprises',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
        ];

        $rows[] = [
            'FIRE PROTECTION ENGINEERS',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
        ];

        $rows[] = [
            '507/50, Jessore Road, Debendra Nagar, Dum Dum, Kolkata - 700074',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
        ];

        $rows[] = [
            '033-2529-6131 | 98310-21491',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
        ];

        $rows[] = [
            'ake.fire@gmail.com | www.akefire.com',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
        ];

        $rows[] = [
            'AN ISO 9001:2015 COMPANY',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
        ];

        return $rows;
    }

    /**
     * -------------------------------------------------------------
     * Column Widths
     * -------------------------------------------------------------
     */
    public function columnWidths(): array
    {
        return [
            'A' => 16,
            'B' => 45,
            'C' => 20,
            'D' => 15,
            'E' => 18,
            'F' => 18,
            'G' => 18,
            'H' => 18,
        ];
    }

    /**
     * -------------------------------------------------------------
     * Basic Styles
     * -------------------------------------------------------------
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 22,
                ],
            ],
        ];
    }

    /**
     * -------------------------------------------------------------
     * After Sheet
     * -------------------------------------------------------------
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                /*
                 * ---------------------------------------------------------
                 * Default Font
                 * ---------------------------------------------------------
                 */

                $sheet->getParent()
                    ->getDefaultStyle()
                    ->getFont()
                    ->setName('Calibri')
                    ->setSize(11);

                /*
                 * ---------------------------------------------------------
                 * Merge Header
                 * ---------------------------------------------------------
                 */

                $sheet->mergeCells('A1:H1');

                $sheet->mergeCells('B2:D2');

                $sheet->mergeCells('F2:F3');

                $sheet->mergeCells('A4:D4');

                $sheet->mergeCells('F4:H4');

                /*
                 * Customer Header
                 */
                $sheet->mergeCells('A6:H6');

                /*
                 * Customer values
                 */
                $sheet->mergeCells('B7:B7');
                $sheet->mergeCells('D7:H7');

                $sheet->mergeCells('B8:B8');
                $sheet->mergeCells('D8:H8');

                $sheet->mergeCells('B9:B9');
                $sheet->mergeCells('D9:H9');

                $sheet->mergeCells('B10:B10');
                $sheet->mergeCells('D10:H10');

                /*
                 * Terms Header
                 */
                $sheet->mergeCells('A12:H12');

                /*
                 * Terms rows
                 */
                for ($row = 13; $row <= 19; $row++) {
                    $sheet->mergeCells("B{$row}:H{$row}");
                }

                /*
                 * Summary Header
                 */
                $sheet->mergeCells('A21:H21');

                /*
                 * Supply Header
                 */
                $sheet->mergeCells('A22:H22');

                /*
                 * Supply Table
                 *
                 * 23 = header
                 * 24 = value
                 * 25 = total
                 */

                /*
                 * Erection Header
                 */
                $sheet->mergeCells('A26:H26');

                /*
                 * Erection Table
                 *
                 * 27 = header
                 * 28 = value
                 * 29 = total
                 */

                /*
                 * Grand Total
                 *
                 * 30 = grand total
                 */

                /*
                 * Footer
                 */
                for ($row = 38; $row <= 44; $row++) {
                    $sheet->mergeCells("A{$row}:H{$row}");
                }

                /*
                 * ---------------------------------------------------------
                 * Header Styling
                 * ---------------------------------------------------------
                 */

                $sheet->getStyle('A1:H1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 24,
                    ],
                    'alignment' => [
                        'horizontal' =>
                            Alignment::HORIZONTAL_CENTER,
                        'vertical' =>
                            Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' =>
                            Fill::FILL_SOLID,
                        'startColor' => [
                            'rgb' => 'D9EAF7',
                        ],
                    ],
                    'borders' => [
                        'outline' => [
                            'borderStyle' =>
                                Border::BORDER_THICK,
                        ],
                    ],
                ]);

                $sheet->getRowDimension(1)->setRowHeight(35);

                /*
                 * Project / quotation details
                 */

                $sheet->getStyle('A2:H4')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' =>
                                Border::BORDER_THIN,
                            'color' => [
                                'rgb' => '000000',
                            ],
                        ],
                    ],
                    'alignment' => [
                        'vertical' =>
                            Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);

                /*
                 * ---------------------------------------------------------
                 * Section Headers
                 * ---------------------------------------------------------
                 */

                foreach ([
                    'A6:H6',
                    'A12:H12',
                    'A21:H21',
                ] as $range) {

                    $sheet->getStyle($range)->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'size' => 12,
                        ],
                        'fill' => [
                            'fillType' =>
                                Fill::FILL_SOLID,
                            'startColor' => [
                                'rgb' => 'B7C9E2',
                            ],
                        ],
                        'alignment' => [
                            'horizontal' =>
                                Alignment::HORIZONTAL_LEFT,
                            'vertical' =>
                                Alignment::VERTICAL_CENTER,
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' =>
                                    Border::BORDER_THIN,
                            ],
                        ],
                    ]);
                }

                /*
                 * Terms rows
                 */
                $sheet->getStyle('A13:H19')->applyFromArray([
                    'alignment' => [
                        'vertical' =>
                            Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' =>
                                Border::BORDER_THIN,
                        ],
                    ],
                ]);

                /*
                 * Customer
                 */
                $sheet->getStyle('A7:H10')->applyFromArray([
                    'alignment' => [
                        'vertical' =>
                            Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' =>
                                Border::BORDER_THIN,
                        ],
                    ],
                ]);

                /*
                 * ---------------------------------------------------------
                 * Supply Header
                 * ---------------------------------------------------------
                 */

                $sheet->getStyle('A22:H22')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'fill' => [
                        'fillType' =>
                            Fill::FILL_SOLID,
                        'startColor' => [
                            'rgb' => 'E7A7B3',
                        ],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' =>
                                Border::BORDER_THIN,
                        ],
                    ],
                ]);

                /*
                 * Supply table
                 */
                $sheet->getStyle('A23:E25')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' =>
                                Border::BORDER_THIN,
                        ],
                    ],
                    'alignment' => [
                        'vertical' =>
                            Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);

                $sheet->getStyle('A23:E23')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'fill' => [
                        'fillType' =>
                            Fill::FILL_SOLID,
                        'startColor' => [
                            'rgb' => 'F3F3F3',
                        ],
                    ],
                    'alignment' => [
                        'horizontal' =>
                            Alignment::HORIZONTAL_CENTER,
                        'vertical' =>
                            Alignment::VERTICAL_CENTER,
                    ],
                ]);

                /*
                 * Supply Total
                 */
                $sheet->getStyle('A25:E25')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'fill' => [
                        'fillType' =>
                            Fill::FILL_SOLID,
                        'startColor' => [
                            'rgb' => 'EEEEEE',
                        ],
                    ],
                ]);

                /*
                 * ---------------------------------------------------------
                 * Erection Header
                 * ---------------------------------------------------------
                 */

                $sheet->getStyle('A26:H26')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'fill' => [
                        'fillType' =>
                            Fill::FILL_SOLID,
                        'startColor' => [
                            'rgb' => '7EDB8B',
                        ],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' =>
                                Border::BORDER_THIN,
                        ],
                    ],
                ]);

                /*
                 * Erection table
                 */
                $sheet->getStyle('A27:E29')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' =>
                                Border::BORDER_THIN,
                        ],
                    ],
                    'alignment' => [
                        'vertical' =>
                            Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);

                $sheet->getStyle('A27:E27')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'fill' => [
                        'fillType' =>
                            Fill::FILL_SOLID,
                        'startColor' => [
                            'rgb' => 'F3F3F3',
                        ],
                    ],
                    'alignment' => [
                        'horizontal' =>
                            Alignment::HORIZONTAL_CENTER,
                        'vertical' =>
                            Alignment::VERTICAL_CENTER,
                    ],
                ]);

                /*
                 * Erection Total
                 */
                $sheet->getStyle('A29:E29')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'fill' => [
                        'fillType' =>
                            Fill::FILL_SOLID,
                        'startColor' => [
                            'rgb' => 'EEEEEE',
                        ],
                    ],
                ]);

                /*
                 * ---------------------------------------------------------
                 * Grand Total
                 * ---------------------------------------------------------
                 */

                $sheet->getStyle('A30:E30')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                    ],
                    'fill' => [
                        'fillType' =>
                            Fill::FILL_SOLID,
                        'startColor' => [
                            'rgb' => 'FFD966',
                        ],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' =>
                                Border::BORDER_THIN,
                        ],
                    ],
                ]);

                /*
                 * ---------------------------------------------------------
                 * Total Box
                 * ---------------------------------------------------------
                 */

                $sheet->getStyle('B32:C36')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' =>
                                Border::BORDER_THIN,
                        ],
                    ],
                    'alignment' => [
                        'horizontal' =>
                            Alignment::HORIZONTAL_RIGHT,
                        'vertical' =>
                            Alignment::VERTICAL_CENTER,
                    ],
                ]);

                /*
                 * Final total
                 */
                $sheet->getStyle('B36:C36')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                    ],
                    'fill' => [
                        'fillType' =>
                            Fill::FILL_SOLID,
                        'startColor' => [
                            'rgb' => 'FFD966',
                        ],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' =>
                                Border::BORDER_THICK,
                        ],
                    ],
                ]);

                /*
                 * ---------------------------------------------------------
                 * Footer
                 * ---------------------------------------------------------
                 */

                $sheet->getStyle('A38:H44')->applyFromArray([
                    'alignment' => [
                        'horizontal' =>
                            Alignment::HORIZONTAL_CENTER,
                        'vertical' =>
                            Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);

                $sheet->getStyle('A40:H40')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 16,
                    ],
                ]);

                $sheet->getStyle('A41:H41')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                ]);

                /*
                 * ---------------------------------------------------------
                 * Number Format
                 * ---------------------------------------------------------
                 */

                foreach ([
                    'C24',
                    'C25',
                    'C28',
                    'C29',
                    'C30',
                    'C32',
                    'C33',
                    'C34',
                    'C35',
                    'C36',
                ] as $cell) {

                    $sheet->getStyle($cell)
                        ->getNumberFormat()
                        ->setFormatCode(
                            '₹ #,##0.00'
                        );
                }

                /*
                 * ---------------------------------------------------------
                 * Row Heights
                 * ---------------------------------------------------------
                 */

                $sheet->getRowDimension(2)->setRowHeight(35);
                $sheet->getRowDimension(4)->setRowHeight(30);

                for ($row = 7; $row <= 10; $row++) {
                    $sheet->getRowDimension($row)
                        ->setRowHeight(25);
                }

                for ($row = 13; $row <= 19; $row++) {
                    $sheet->getRowDimension($row)
                        ->setRowHeight(25);
                }

                $sheet->getRowDimension(23)->setRowHeight(30);
                $sheet->getRowDimension(27)->setRowHeight(30);

                /*
                 * ---------------------------------------------------------
                 * Freeze
                 * ---------------------------------------------------------
                 */

                $sheet->freezePane('A2');

                /*
                 * ---------------------------------------------------------
                 * Print Setup
                 * ---------------------------------------------------------
                 */

                $pageSetup = $sheet->getPageSetup();

                $pageSetup->setOrientation(
                    \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT
                );

                $pageSetup->setPaperSize(
                    \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4
                );

                $pageSetup->setFitToWidth(1);
                $pageSetup->setFitToHeight(0);

                $sheet->setShowGridlines(false);

                /*
                 * Print margins
                 */
                $sheet->getPageMargins()
                    ->setTop(0.25)
                    ->setBottom(0.25)
                    ->setLeft(0.25)
                    ->setRight(0.25);

                /*
                 * Print area
                 */
                $sheet->getPageSetup()
                    ->setPrintArea('A1:H44');
            },
        ];
    }
}

