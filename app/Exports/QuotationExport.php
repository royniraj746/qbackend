<?php

namespace App\Exports;

use App\Models\Quotation;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
// use App\Exports\Sheets\QuotationCategorySheet;
class QuotationExport implements WithMultipleSheets
{
    protected $quotation;

    public function __construct(Quotation $quotation)
    {
        $this->quotation = $quotation;
    }

    /**
     * Return multiple sheets
     */
    public function sheets(): array
    {
        return [
            new Sheets\QuotationSummarySheet($this->quotation),
            new Sheets\QuotationItemsSheet($this->quotation),
            new Sheets\QuotationCategorySheet($this->quotation),
        ];
    }
}
