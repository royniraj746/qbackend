<?php

namespace App\Http\Controllers\API;

use App\Exports\QuotationExport;
use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\QuotationItem;
use Barryvdh\DomPDF\PDF;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;





class QuotationController extends Controller
{
    // $user = auth()->user();




// public function store(Request $request)
// {
//     DB::transaction(function () use ($request) {

//         $user = auth()->user();

//         $enquiry = Enquiry::with('enquirycustomer')
//             ->where('id', $request->enquiry_id)
//             ->first();

//         if (!$enquiry) {
//             abort(422, 'Invalid enquiry id');
//         }

//         $enquiry->status = 'Quoted';
//         $enquiry->save();

//         $quotation = Quotation::create([
//             'enquiry_id' => $enquiry->id,
//             'enquirycustomer_id' => $enquiry->enquiry_customer_id,
//             'quotation_no' => 'QT-' . time(),
//             'quotation_date' => now(),
//             'created_by' => $user->id,
//             ...$request->except('items')
//         ]);

//         $subtotal = 0;
//         $totalGst = 0;

//         foreach ($request->items as $item) {

//             /*
//             --------------------------------
//             SUPPLY CALCULATION
//             --------------------------------
//             */

//             $purchase = $item['base_price'] * $item['qty'];

//             $fittings_amount        = ($purchase * $quotation->fittings_percent) / 100;
//             $paint_amount           = ($purchase * $quotation->paint_percent) / 100;
//             $transportation_amount  = ($purchase * $quotation->transportation_percent) / 100;
//             $overhead_amount        = ($purchase * $quotation->overhead_percent) / 100;
//             $ho_expenses_amount     = ($purchase * $quotation->ho_expenses_percent) / 100;
//             $ld_amount              = ($purchase * $quotation->ld_percent) / 100;
//             $packaging_amount       = ($purchase * $quotation->packaging_percent) / 100;
//             $insurance_amount       = ($purchase * $quotation->insurance_percent) / 100;
//             $profit_amount          = ($purchase * $quotation->profit_percent) / 100;
//             $price_variation_amount = ($purchase * $quotation->price_variation_percent) / 100;
//             $bds_amount             = ($purchase * $quotation->bds_percent) / 100;
//             $cushion_amount         = ($purchase * $quotation->cushion_percent) / 100;

//             $total_extra = collect([
//                 $fittings_amount,
//                 $paint_amount,
//                 $transportation_amount,
//                 $overhead_amount,
//                 $ho_expenses_amount,
//                 $ld_amount,
//                 $packaging_amount,
//                 $insurance_amount,
//                 $profit_amount,
//                 $price_variation_amount,
//                 $bds_amount,
//                 $cushion_amount
//             ])->sum();

//             $supply = $purchase + $total_extra;

//             /*
//             --------------------------------
//             ERECTION CALCULATION
//             --------------------------------
//             */

//             $labour_cost = null;
//             $labour_amount = 0;
//             $erection_rate = 0;
//             $total_erection_extra = 0;

//             $consumables_amount = null;
//             $ppe_amount = null;
//             $supervision_amount = null;
//             $site_mobilization_amount = null;
//             $ld_labour_amount = null;
//             $labour_insurance_amount = null;
//             $erection_profit_amount = null;
//             $price_variation_labour_amount = null;
//             $bds_labour_amount = null;
//             $cushion_labour_amount = null;

//             if ($quotation->quotation_type == 'supply_erection') {

//                 $product = Product::find($item['product_id']);

//                 $labour_cost = $product->labour_cost ?? 0;

//                 $labour_amount = $labour_cost * $item['qty'];

//                 $consumables_amount = ($labour_amount * $quotation->consumables_percent) / 100;
//                 $ppe_amount = ($labour_amount * $quotation->ppe_percent) / 100;
//                 $supervision_amount = ($labour_amount * $quotation->supervision_percent) / 100;
//                 $site_mobilization_amount = ($labour_amount * $quotation->site_mobilization_percent) / 100;
//                 $ld_labour_amount = ($labour_amount * $quotation->ld_labour_percent) / 100;
//                 $labour_insurance_amount = ($labour_amount * $quotation->labour_insurance_percent) / 100;
//                 $erection_profit_amount = ($labour_amount * $quotation->erection_profit_percent) / 100;
//                 $price_variation_labour_amount = ($labour_amount * $quotation->price_variation_labour_percent) / 100;
//                 $bds_labour_amount = ($labour_amount * $quotation->bds_labour_percent) / 100;
//                 $cushion_labour_amount = ($labour_amount * $quotation->cushion_labour_percent) / 100;

//                 $total_erection_extra = collect([
//                     $consumables_amount,
//                     $ppe_amount,
//                     $supervision_amount,
//                     $site_mobilization_amount,
//                     $ld_labour_amount,
//                     $labour_insurance_amount,
//                     $erection_profit_amount,
//                     $price_variation_labour_amount,
//                     $bds_labour_amount,
//                     $cushion_labour_amount
//                 ])->sum();

//                 $erection_rate = $labour_amount + $total_erection_extra;
//             }

//             /*
//             --------------------------------
//             FINAL TOTAL
//             --------------------------------
//             */

//             // $before_gst = $supply + $erection_rate;

//             // $gst = ($before_gst * $item['gst_percent']) / 100;

//             // $total = $before_gst + $gst;

//             /*
// --------------------------------
// GST CALCULATION
// --------------------------------
// */

// $supply_gst_amount = ($supply * $item['gst_percent']) / 100;

// $erection_gst_amount = ($erection_rate * $item['gst_percent']) / 100;

// $gst = $supply_gst_amount + $erection_gst_amount;

// /*
// --------------------------------
// FINAL TOTAL
// --------------------------------
// */

// $before_gst = $supply + $erection_rate;

// $total = $before_gst + $gst;

//             QuotationItem::create([
//                 'quotation_id' => $quotation->id,
//                 'product_id' => $item['product_id'],

//                 'base_price' => $item['base_price'],
//                 'qty' => $item['qty'],
//                 'purchase_amount' => $purchase,

//                 'fittings_amount' => $fittings_amount,
//                 'paint_amount' => $paint_amount,
//                 'transportation_amount' => $transportation_amount,
//                 'overhead_amount' => $overhead_amount,
//                 'ho_expenses_amount' => $ho_expenses_amount,
//                 'ld_amount' => $ld_amount,
//                 'packaging_amount' => $packaging_amount,
//                 'insurance_amount' => $insurance_amount,
//                 'profit_amount' => $profit_amount,
//                 'price_variation_amount' => $price_variation_amount,
//                 'bds_amount' => $bds_amount,
//                 'cushion_amount' => $cushion_amount,

//                 'total_extra_amount' => $total_extra,
//                 'supply_rate' => $supply,

//                 'labour_cost' => $labour_cost,
//                 'labour_amount' => $labour_amount,

//                 'consumables_amount' => $consumables_amount,
//                 'ppe_amount' => $ppe_amount,
//                 'supervision_amount' => $supervision_amount,
//                 'site_mobilization_amount' => $site_mobilization_amount,
//                 'ld_labour_amount' => $ld_labour_amount,
//                 'labour_insurance_amount' => $labour_insurance_amount,
//                 'erection_profit_amount' => $erection_profit_amount,
//                 'price_variation_labour_amount' => $price_variation_labour_amount,
//                 'bds_labour_amount' => $bds_labour_amount,
//                 'cushion_labour_amount' => $cushion_labour_amount,

//                 'total_erection_extra' => $total_erection_extra,
//                 'erection_rate' => $erection_rate,
// // new gst add
//                 'supply_gst_amount' => $supply_gst_amount,
//                 'erection_gst_amount' => $erection_gst_amount,



//                 'gst_percent' => $item['gst_percent'],
//                 'gst_amount' => $gst,
//                 'total_amount' => $total
//             ]);

//             // $subtotal += $before_gst;
//             // $totalGst += $gst;
//             $subtotal += $before_gst;

// $totalGst += $supply_gst_amount + $erection_gst_amount;
//         }
//         $quotation->update([
//             'subtotal' => $subtotal,
//             'total_gst' => $totalGst,
//             'grand_total' => $subtotal + $totalGst
//         ]);
//         // $quotation->update([
//         //     'subtotal' => $subtotal,
//         //     'total_gst' => $totalGst,
//         //     'grand_total' => $subtotal + $totalGst
//         // ]);
//     });

//     return response()->json([
//         'message' => 'Quotation Created Successfully'
//     ]);
// }

private function getFinancialYear()
{
    $today = now();

    if ($today->month >= 4) {
        $start = $today->year;
        $end = $today->year + 1;
    } else {
        $start = $today->year - 1;
        $end = $today->year;
    }

    return substr($start, -2) . substr($end, -2);
}

public function store(Request $request)
{
    DB::transaction(function () use ($request) {
        Validator::make($request->all(),[
            'items.*.gst_percent'=>'required|numeric|min:0|max:100'
        ])->validate();
        $user = auth()->user();

        $enquiry = Enquiry::with('enquirycustomer')
            ->where('id', $request->enquiry_id)
            ->first();

        if (!$enquiry) {
            abort(422, 'Invalid enquiry id');
        }

        $enquiry->status = 'Quoted';
        $enquiry->save();


// Financial Year
$financialYear = $this->getFinancialYear();

// Financial Year Start Date
if (now()->month >= 4) {
    $financialStart = now()->copy()->setDate(now()->year, 4, 1)->startOfDay();
} else {
    $financialStart = now()->copy()->subYear()->setDate(now()->subYear()->year, 4, 1)->startOfDay();
}

// Financial Year End Date
$financialEnd = $financialStart->copy()->addYear()->subDay()->endOfDay();

// Current FY Serial Number
$prefix = "ALE/Q/";
$suffix = $financialYear;


$lastQuotation = Quotation::whereBetween('quotation_date', [
    $financialStart,
    $financialEnd
])
    ->where('quotation_no', 'NOT LIKE', '%-%') // Hyphen wale ignore
    ->orderByDesc('id')
    ->first();

if ($lastQuotation) {

    // Example: ALE/Q/003/2627
    preg_match(
        '/^ALE\/Q\/(\d+)\/' . preg_quote($financialYear, '/') . '$/',
        $lastQuotation->quotation_no,
        $matches
    );

    $lastSerial = isset($matches[1]) ? (int) $matches[1] : 0;

    $serial = $lastSerial + 1;

} else {

    $serial = 1;
}

// 001, 002, 003...
$serial = str_pad($serial, 3, '0', STR_PAD_LEFT);

// Final Quotation Number
$quotationNo = "ALE/Q/{$serial}/{$financialYear}";

        $quotation = Quotation::create([
            'enquiry_id' => $enquiry->id,
            'enquirycustomer_id' => $enquiry->enquiry_customer_id,
            'quotation_no' => $quotationNo,
            'quotation_date' => now(),
            'created_by' => $user->id,
            ...$request->except('items')
        ]);

        $subtotal = 0;
        $totalGst = 0;

        foreach ($request->items as $item) {

            /*
            --------------------------------
            SUPPLY CALCULATION
            --------------------------------
            */


            $purchase = $item['base_price'] * $item['qty'];

// extras
$fittings_amount        = ($purchase * $quotation->fittings_percent) / 100;
$paint_amount           = ($purchase * $quotation->paint_percent) / 100;
$transportation_amount  = ($purchase * $quotation->transportation_percent) / 100;
$overhead_amount        = ($purchase * $quotation->overhead_percent) / 100;
$ho_expenses_amount     = ($purchase * $quotation->ho_expenses_percent) / 100;
$ld_amount              = ($purchase * $quotation->ld_percent) / 100;
$packaging_amount       = ($purchase * $quotation->packaging_percent) / 100;
$insurance_amount       = ($purchase * $quotation->insurance_percent) / 100;
$price_variation_amount = ($purchase * $quotation->price_variation_percent) / 100;

$base_total =
    $purchase +
    $fittings_amount +
    $paint_amount +
    $transportation_amount +
    $overhead_amount +
    $ho_expenses_amount +
    $ld_amount +
    $packaging_amount +
    $insurance_amount +
    $price_variation_amount;

// PROFIT
$profit_amount = ($base_total * $quotation->profit_percent) / 100;

// CUSHION
$cushion_base = $base_total + $profit_amount;
$cushion_amount = ($cushion_base * $quotation->cushion_percent) / 100;

// BDS
$bds_base = $cushion_base + $cushion_amount;
$bds_amount = ($bds_base * $quotation->bds_percent) / 100;

// TOTAL
$supply = $base_total + $bds_amount+$cushion_amount+$profit_amount;

            // $supply = $purchase + $total_extra;

            /*
            --------------------------------
            ERECTION CALCULATION
            --------------------------------
            */

            $labour_cost = null;
            $labour_amount = 0;
            $erection_rate = 0;
            $total_erection_extra = 0;

            $consumables_amount = null;
            $ppe_amount = null;
            $supervision_amount = null;
            $site_mobilization_amount = null;
            $ld_labour_amount = null;
            $labour_insurance_amount = null;
            $erection_profit_amount = null;
            $price_variation_labour_amount = null;
            $bds_labour_amount = null;
            $cushion_labour_amount = null;

            if ($quotation->quotation_type == 'supply_erection') {

                $product = Product::find($item['product_id']);

                $labour_cost = $product->labour_cost ?? 0;


                $labour_amount = $labour_cost * $item['qty'];

$consumables_amount        = ($labour_amount * $quotation->consumables_percent) / 100;
$ppe_amount                = ($labour_amount * $quotation->ppe_percent) / 100;
$supervision_amount        = ($labour_amount * $quotation->supervision_percent) / 100;
$site_mobilization_amount  = ($labour_amount * $quotation->site_mobilization_percent) / 100;
$ld_labour_amount          = ($labour_amount * $quotation->ld_labour_percent) / 100;
$labour_insurance_amount   = ($labour_amount * $quotation->labour_insurance_percent) / 100;
$price_variation_labour_amount = ($labour_amount * $quotation->price_variation_labour_percent) / 100;

$labour_base =
    $labour_amount +
    $consumables_amount +
    $ppe_amount +
    $supervision_amount +
    $site_mobilization_amount +
    $ld_labour_amount +
    $labour_insurance_amount +
    $price_variation_labour_amount;

// PROFIT
$erection_profit_amount =
    ($labour_base * $quotation->erection_profit_percent) / 100;

// CUSHION
$cushion_labour_base = $labour_base + $erection_profit_amount;

$cushion_labour_amount =
    ($cushion_labour_base * $quotation->cushion_labour_percent) / 100;

// BDS
$bds_labour_base = $cushion_labour_base + $cushion_labour_amount;

$bds_labour_amount =
    ($bds_labour_base * $quotation->bds_labour_percent) / 100;

// TOTAL
$erection_rate = $labour_base + $bds_labour_amount+$cushion_labour_amount+$erection_profit_amount;

                // $erection_rate = $labour_amount + $total_erection_extra;
            }



$supply_gst_amount = ($supply * $item['gst_percent']) / 100;

$erection_gst_amount = ($erection_rate * $item['gst_percent']) / 100;

$gst = $supply_gst_amount + $erection_gst_amount;

/*
--------------------------------
FINAL TOTAL
--------------------------------
*/

$before_gst = $supply + $erection_rate;

$total = $before_gst + $gst;

            QuotationItem::create([
                'quotation_id' => $quotation->id,
                'product_id' => $item['product_id'],



                'project_category_id' => $item['project_category_id'] ?? null,
                'group_qty' => $item['group_qty'] ?? null,
                'project_category_qty' => $item['project_category_qty'] ?? null,

                'base_price' => $item['base_price'],
                'qty' => $item['qty'],
                'purchase_amount' => $purchase,
                // 'erection_total'=>$quotation->grand_total,

                'fittings_amount' => $fittings_amount,
                'paint_amount' => $paint_amount,
                'transportation_amount' => $transportation_amount,
                'overhead_amount' => $overhead_amount,
                'ho_expenses_amount' => $ho_expenses_amount,
                'ld_amount' => $ld_amount,
                'packaging_amount' => $packaging_amount,
                'insurance_amount' => $insurance_amount,
                'profit_amount' => $profit_amount,
                'price_variation_amount' => $price_variation_amount,
                'bds_amount' => $bds_amount,
                'cushion_amount' => $cushion_amount,

                // 'total_extra_amount' => $total_extra,
                'total_extra_amount' => 0,
                'supply_rate' => $supply,

                'labour_cost' => $labour_cost,
                'labour_amount' => $labour_amount,

                'consumables_amount' => $consumables_amount,
                'ppe_amount' => $ppe_amount,
                'supervision_amount' => $supervision_amount,
                'site_mobilization_amount' => $site_mobilization_amount,
                'ld_labour_amount' => $ld_labour_amount,
                'labour_insurance_amount' => $labour_insurance_amount,
                'erection_profit_amount' => $erection_profit_amount,
                'price_variation_labour_amount' => $price_variation_labour_amount,
                'bds_labour_amount' => $bds_labour_amount,
                'cushion_labour_amount' => $cushion_labour_amount,

                'total_erection_extra' => $total_erection_extra,
                'erection_rate' => $erection_rate,
// new gst add
                'supply_gst_amount' => $supply_gst_amount,
                'erection_gst_amount' => $erection_gst_amount,



                'gst_percent' => $item['gst_percent'],
                'gst_amount' => $gst,
                'total_amount' => $total
            ]);

            // $subtotal += $before_gst;
            // $totalGst += $gst;
            $subtotal += $before_gst;

$totalGst += $supply_gst_amount + $erection_gst_amount;
        }
        $quotation->update([
            'subtotal' => $subtotal,
            'total_gst' => $totalGst,
            'grand_total' => $subtotal + $totalGst,
            'erection_total'=>$request->grand_final_total,
        ]);

    });

    return response()->json([
        'message' => 'Quotation Created Successfully'
    ]);
}

    // customer',
    // 'Enquirycustomer',
    // 'enquiry.enquirycustomer'
    // public function index()
    // {
    //     $quotations = Quotation::with([
    //         'enquiry.enquirycustomer.createdBy',
    //         'enquiry.createdBy',
    //         'createdBy'

    //     ])->latest()->get();

    //     return response()->json($quotations);
    // }
    public function index(Request $request)
{
    $query = Quotation::with([
        'enquiry.enquirycustomer.createdBy',
        'enquiry.createdBy',
        'createdBy'
    ]);

    // Search
    if ($request->filled('search')) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {

            // Quotation No
            $q->where('quotation_no', 'like', "%{$search}%")

            // Enquiry No
            ->orWhereHas('enquiry', function ($eq) use ($search) {
                $eq->where('enquiry_code', 'like', "%{$search}%");
            })

            // Customer Name
            ->orWhereHas('enquiry.enquirycustomer', function ($cq) use ($search) {
                $cq->where('customer_name', 'like', "%{$search}%");
            });
        });
    }

    // Quotation Type Filter
    if ($request->filled('quotation_type')) {
        $query->where('quotation_type', $request->quotation_type);
    }

    // Date Range Filter
    if ($request->filled('start_date')) {
        $query->whereDate('created_at', '>=', $request->start_date);
    }

    if ($request->filled('end_date')) {
        $query->whereDate('created_at', '<=', $request->end_date);
    }

    $quotations = $query
        ->latest()
        ->paginate($request->per_page ?? 10);

    return response()->json($quotations);
}
    public function show($id)
    {
        $quotation = Quotation::with([
            'items.product',
            'items.projectcategries',
            'customer',
            'enquiry'
        ])->find($id);

        if (!$quotation) {
            return response()->json([
                'status' => false,
                'message' => 'Quotation not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $quotation
        ]);
    }



    public function print($id)
    {
        $quotation = Quotation::with([
            'items.product',
            'enquiry.customer'
        ])->findOrFail($id);

        $pdf = \PDF::loadView('pdf.quotation', compact('quotation'));
        return $pdf->download("Quotation-{$quotation->quotation_no}.pdf");
    }

    // public function excel($id)
    // {
    //     $quotation = Quotation::with([
    //         'items.product',
    //         'items.projectcategries',
    //         'customer',
    //         'enquiry'
    //     ])->findOrFail($id);

    //     return Excel::download(
    //         new QuotationExport($quotation),
    //         'Quotation-'.$quotation->quotation_no.'.xlsx'
    //     );
    // }


    public function excel($id)
    {
        $quotation = Quotation::with([
            'items.product',
            'items.projectcategries',
            'customer',
            'enquiry'
        ])->findOrFail($id);

        // ✅ Clean filename: '/' aur '\' ko '-' se replace karein
        $safeQuotationNo = str_replace(['/', '\\'], '-', $quotation->quotation_no);

        return Excel::download(
            new QuotationExport($quotation),
            'Quotation-' . $safeQuotationNo . '.xlsx'
        );
    }



// 1. एग्ज़िस्टिंग अपडेट मेथड (बिना बदलाव के, केवल सुरक्षित रखने के लिए)
public function update(Request $request, $id)
{
    $request->validate([
        'items' => 'required|array|min:1',
        'items.*.product_id' => 'required|integer',
        'items.*.base_price' => 'required|numeric|min:0',
        'items.*.qty' => 'required|numeric|min:1',
        'items.*.gst_percent' => 'required|numeric|min:0',
    ]);

    DB::transaction(function () use ($request, $id) {
        $quotation = Quotation::with('items')->findOrFail($id);

        $quotation->update(
            $request->except(['items', 'enquiry_id', 'quotation_no'])
        );

        $quotation->items()->delete();
        $this->saveQuotationItems($quotation, $request->items);
    });

    return response()->json([
        'success' => true,
        'message' => 'Quotation Updated Successfully'
    ]);
}

// 2. नया रीविज़न मेथड (REVISION QUOTATION BUTTON TRIGGER)
// public function revision(Request $request, $id)
// {
//     $request->validate([
//         'items' => 'required|array|min:1',
//         'items.*.product_id' => 'required|integer',
//         'items.*.base_price' => 'required|numeric|min:0',
//         'items.*.qty' => 'required|numeric|min:1',
//         'items.*.gst_percent' => 'required|numeric|min:0',
//     ]);

//     $newQuotation = DB::transaction(function () use ($request, $id) {
//         // 1. पुराने कोटेशन को ढूंढें
//         $oldQuotation = Quotation::findOrFail($id);

//         // --- 2. परफेक्ट रीविज़न नंबर लॉजिक ---
//         $oldNo = $oldQuotation->quotation_no; // e.g., "QT-1782639047" या "QT-1782639047-1"

//         // स्ट्रिक्ट Regex: मुख्य बेस नंबर को अलग निकालें
//         if (preg_match('/^(QT-\d+)-(\d+)$/', $oldNo, $matches)) {
//             $baseQuotationNo = $matches[1]; // ये देगा हमेशा "QT-1782639047"
//         } else {
//             $baseQuotationNo = $oldNo; // बिना काउंटर के "QT-1782639047"
//         }

//         // 3. ब्रैकेट ग्रुपिंग के साथ सिर्फ इसी बेस नंबर का सबसे लेटेस्ट रिकॉर्ड निकालें
//         $latestRevision = Quotation::where(function($query) use ($baseQuotationNo) {
//                 $query->where('quotation_no', $baseQuotationNo)
//                       ->orWhere('quotation_no', 'LIKE', $baseQuotationNo . '-%');
//             })
//             ->orderByRaw('CAST(SUBSTRING_INDEX(quotation_no, "-", -1) AS UNSIGNED) DESC')
//             ->orderBy('id', 'desc')
//             ->first();

//         $newCounter = 1; // पहला काउंटर डिफ़ॉल्ट 1 होगा

//         if ($latestRevision) {
//             $latestNo = $latestRevision->quotation_no;
//             // अगर लेटेस्ट कोटेशन नंबर में अंत में हाइफ़न और नंबर है
//             if (preg_match('/-(\d+)$/', $latestNo, $counterMatches)) {
//                 $newCounter = (int)$counterMatches[1] + 1; // पुराने काउंटर में +1 जोड़ें (जैसे 1+1=2)
//             }
//         }

//         // नया नंबर तैयार: e.g., QT-1782639047-1
//         $newQuotationNo = $baseQuotationNo . '-' . $newCounter;
//         // ------------------------------------

//         // 4. नया कोटेशन रिकॉर्ड बनाएं (अनचाहे रिक्वेस्ट डेटा को साफ करते हुए)
//         $user = auth()->user();

//         // फ्रंटएंड से आने वाले quotation_no, enquiry_id को हटा दें ताकि पुराना ओवरराइड न हो
//         $cleanInputs = $request->except(['items', 'quotation_no', 'enquiry_id', 'enquirycustomer_id', 'id']);

//         $quotationData = array_merge($cleanInputs, [
//             'enquiry_id' => $oldQuotation->enquiry_id,
//             'enquirycustomer_id' => $oldQuotation->enquirycustomer_id,
//             'quotation_no' => $newQuotationNo, // हमारा नया जनरेटेड नंबर
//             'quotation_date' => now(),
//             'created_by' => $user ? $user->id : $oldQuotation->created_by,
//         ]);

//         $quotation = Quotation::create($quotationData);

//         // 5. आइटम्स को नए कोटेशन आईडी के साथ सेव करें
//         $this->saveQuotationItems($quotation, $request->items);

//         return $quotation;
//     });

//     return response()->json([
//         'success' => true,
//         'message' => 'New Quotation Revision ' . $newQuotation->quotation_no . ' Created Successfully',
//         'data' => $newQuotation
//     ]);
// }

public function revision(Request $request, $id)
{
    $request->validate([
        'items' => 'required|array|min:1',
        'items.*.product_id' => 'required|integer',
        'items.*.base_price' => 'required|numeric|min:0',
        'items.*.qty' => 'required|numeric|min:1',
        'items.*.gst_percent' => 'required|numeric|min:0',
    ]);

    $newQuotation = DB::transaction(function () use ($request, $id) {
        // 1. पुराने कोटेशन को ढूंढें
        $oldQuotation = Quotation::findOrFail($id);

        // --- 2. 'rev' कॉलम आधारित रीविज़न नंबर लॉजिक ---
        $oldNo = $oldQuotation->quotation_no; // e.g., "QT-1782639047" या "QT-1782639047-1"

        // हमेशा हाइफ़न (-) के पहले का ओरिजिनल बेस नंबर निकालें
        if (preg_match('/^(QT-\d+)/', $oldNo, $matches)) {
            $baseQuotationNo = $matches[1]; // ये हमेशा शुद्ध "QT-1782639047" देगा
        } else {
            $baseQuotationNo = $oldNo;
        }

        // पुराने 'rev' की वैल्यू में 1 प्लस करें (अगर 0 है तो 1 बनेगा, 1 है तो 2 बनेगा)
        $newRevValue = (int)$oldQuotation->rev + 1;

        // नया नंबर तैयार करें: Base Number + Hyphen + New Rev Count
        $newQuotationNo = $baseQuotationNo . '-' . $newRevValue;
        // ------------------------------------

        // 3. नया कोटेशन रिकॉर्ड बनाएं
        $user = auth()->user();

        // फ्रंटएंड से आने वाले अनचाहे वेरिएबल्स को साफ करें
        $cleanInputs = $request->except(['items', 'quotation_no', 'enquiry_id', 'enquirycustomer_id', 'id', 'rev']);

        $quotationData = array_merge($cleanInputs, [
            'enquiry_id' => $oldQuotation->enquiry_id,
            'enquirycustomer_id' => $oldQuotation->enquirycustomer_id,
            'quotation_no' => $newQuotationNo, // नया नंबर (e.g., QT-1782639047-1)
            'rev' => $newRevValue,             // बढ़ा हुआ rev नंबर (1, 2, 3...)
            'quotation_date' => now(),
            'created_by' => $user ? $user->id : $oldQuotation->created_by,
        ]);

        $quotation = Quotation::create($quotationData);

        // 4. आइटम्स को नए कोटेशन आईडी के साथ सेव करें
        $this->saveQuotationItems($quotation, $request->items);

        return $quotation;
    });

    return response()->json([
        'success' => true,
        'message' => 'New Quotation Revision ' . $newQuotation->quotation_no . ' Created Successfully',
        'data' => $newQuotation
    ]);
}
// कोड डुप्लीकेशन से बचने के लिए कॉमन मैथड (प्राइस और टैक्स कैलकुलेशन)
private function saveQuotationItems($quotation, $items)
{
    $subtotal = 0;
    $totalGst = 0;

    foreach ($items as $item) {
        $purchase = $item['base_price'] * $item['qty'];

        $fittings_amount        = ($purchase * $quotation->fittings_percent) / 100;
        $paint_amount           = ($purchase * $quotation->paint_percent) / 100;
        $transportation_amount  = ($purchase * $quotation->transportation_percent) / 100;
        $overhead_amount        = ($purchase * $quotation->overhead_percent) / 100;
        $ho_expenses_amount     = ($purchase * $quotation->ho_expenses_percent) / 100;
        $ld_amount              = ($purchase * $quotation->ld_percent) / 100;
        $packaging_amount       = ($purchase * $quotation->packaging_percent) / 100;
        $insurance_amount       = ($purchase * $quotation->insurance_percent) / 100;
        $price_variation_amount = ($purchase * $quotation->price_variation_percent) / 100;

        $base_total = $purchase + $fittings_amount + $paint_amount + $transportation_amount + $overhead_amount + $ho_expenses_amount + $ld_amount + $packaging_amount + $insurance_amount + $price_variation_amount;

        $profit_amount = ($base_total * $quotation->profit_percent) / 100;
        $cushion_base = $base_total + $profit_amount;
        $cushion_amount = ($cushion_base * $quotation->cushion_percent) / 100;
        $bds_base = $cushion_base + $cushion_amount;
        $bds_amount = ($bds_base * $quotation->bds_percent) / 100;

        $supply = $base_total + $profit_amount + $cushion_amount + $bds_amount;

        $total_extra = $fittings_amount + $paint_amount + $transportation_amount + $overhead_amount + $ho_expenses_amount + $ld_amount + $packaging_amount + $insurance_amount + $price_variation_amount + $profit_amount + $cushion_amount + $bds_amount;

        $labour_cost = null;
        $labour_amount = 0;
        $erection_rate = 0;
        $total_erection_extra = 0;

        $consumables_amount = null;
        $ppe_amount = null;
        $supervision_amount = null;
        $site_mobilization_amount = null;
        $ld_labour_amount = null;
        $labour_insurance_amount = null;
        $erection_profit_amount = null;
        $price_variation_labour_amount = null;
        $bds_labour_amount = null;
        $cushion_labour_amount = null;

        if ($quotation->quotation_type == 'supply_erection') {
            $product = Product::find($item['product_id']);
            $labour_cost = $product->labour_cost ?? 0;
            $labour_amount = $labour_cost * $item['qty'];

            $consumables_amount            = ($labour_amount * $quotation->consumables_percent) / 100;
            $ppe_amount                    = ($labour_amount * $quotation->ppe_percent) / 100;
            $supervision_amount            = ($labour_amount * $quotation->supervision_percent) / 100;
            $site_mobilization_amount      = ($labour_amount * $quotation->site_mobilization_percent) / 100;
            $ld_labour_amount              = ($labour_amount * $quotation->ld_labour_percent) / 100;
            $labour_insurance_amount       = ($labour_amount * $quotation->labour_insurance_percent) / 100;
            $price_variation_labour_amount = ($labour_amount * $quotation->price_variation_labour_percent) / 100;

            $labour_base = $labour_amount + $consumables_amount + $ppe_amount + $supervision_amount + $site_mobilization_amount + $ld_labour_amount + $labour_insurance_amount + $price_variation_labour_amount;

            $erection_profit_amount = ($labour_base * $quotation->erection_profit_percent) / 100;
            $cushion_labour_base = $labour_base + $erection_profit_amount;
            $cushion_labour_amount = ($cushion_labour_base * $quotation->cushion_labour_percent) / 100;
            $bds_labour_base = $cushion_labour_base + $cushion_labour_amount;
            $bds_labour_amount = ($bds_labour_base * $quotation->bds_labour_percent) / 100;

            $erection_rate = $labour_base + $erection_profit_amount + $cushion_labour_amount + $bds_labour_amount;

            $total_erection_extra = $consumables_amount + $ppe_amount + $supervision_amount + $site_mobilization_amount + $ld_labour_amount + $labour_insurance_amount + $price_variation_labour_amount + $erection_profit_amount + $cushion_labour_amount + $bds_labour_amount;
        }

        $supply_gst_amount = ($supply * $item['gst_percent']) / 100;
        $erection_gst_amount = ($erection_rate * $item['gst_percent']) / 100;
        $gst = $supply_gst_amount + $erection_gst_amount;

        $before_gst = $supply + $erection_rate;
        $total = $before_gst + $gst;

        QuotationItem::create([
            'quotation_id' => $quotation->id,
            'product_id' => $item['product_id'],
            'base_price' => $item['base_price'],


            'project_category_id' => $item['project_category_id'] ?? null,
            'project_category_qty' => $item['project_category_qty'],
            // 'base_price' => $item['base_price'],

            'qty' => $item['qty'],
            'purchase_amount' => $purchase,
            'fittings_amount' => $fittings_amount,
            'paint_amount' => $paint_amount,
            'transportation_amount' => $transportation_amount,
            'overhead_amount' => $overhead_amount,
            'ho_expenses_amount' => $ho_expenses_amount,
            'ld_amount' => $ld_amount,
            'packaging_amount' => $packaging_amount,
            'insurance_amount' => $insurance_amount,
            'profit_amount' => $profit_amount,
            'price_variation_amount' => $price_variation_amount,
            'bds_amount' => $bds_amount,
            'cushion_amount' => $cushion_amount,
            'total_extra_amount' => $total_extra,
            'supply_rate' => $supply,
            'labour_cost' => $labour_cost,
            'labour_amount' => $labour_amount,
            'consumables_amount' => $consumables_amount,
            'ppe_amount' => $ppe_amount,

            'supervision_amount' => $supervision_amount,
            'site_mobilization_amount' => $site_mobilization_amount,
            'ld_labour_amount' => $ld_labour_amount,
            'labour_insurance_amount' => $labour_insurance_amount,
            'erection_profit_amount' => $erection_profit_amount,
            'price_variation_labour_amount' => $price_variation_labour_amount,
            'bds_labour_amount' => $bds_labour_amount,
            'cushion_labour_amount' => $cushion_labour_amount,
            'total_erection_extra' => $total_erection_extra,
            'erection_rate' => $erection_rate,
            'supply_gst_amount' => $supply_gst_amount,
            'erection_gst_amount' => $erection_gst_amount,
            'gst_percent' => $item['gst_percent'],
            'gst_amount' => $gst,
            'total_amount' => $total
        ]);

        $subtotal += $before_gst;
        $totalGst += $gst;
    }

    $quotation->update([
        'subtotal' => $subtotal,
        'total_gst' => $totalGst,
        'grand_total' => $subtotal + $totalGst,
        'erection_total'=>$quotation->grand_total,
    ]);
}




// public function update(Request $request, $id)
// {
//     $request->validate([
//         'items' => 'required|array|min:1',
//         'items.*.product_id' => 'required|integer',
//         'items.*.base_price' => 'required|numeric|min:0',
//         'items.*.qty' => 'required|numeric|min:1',
//         'items.*.gst_percent' => 'required|numeric|min:0',
//     ]);

//     DB::transaction(function () use ($request, $id) {

//         $quotation = Quotation::with('items')->findOrFail($id);

//         $quotation->update(
//             $request->except(['items', 'enquiry_id', 'quotation_no'])
//         );

//         // delete old items
//         $quotation->items()->delete();

//         $subtotal = 0;
//         $totalGst = 0;

//         foreach ($request->items as $item) {

//             /*
//             -------------------------
//             SUPPLY CALCULATION
//             -------------------------
//             */

//             $purchase = $item['base_price'] * $item['qty'];


//             $fittings_amount        = ($purchase * $quotation->fittings_percent) / 100;
// $paint_amount           = ($purchase * $quotation->paint_percent) / 100;
// $transportation_amount  = ($purchase * $quotation->transportation_percent) / 100;
// $overhead_amount        = ($purchase * $quotation->overhead_percent) / 100;
// $ho_expenses_amount     = ($purchase * $quotation->ho_expenses_percent) / 100;
// $ld_amount              = ($purchase * $quotation->ld_percent) / 100;
// $packaging_amount       = ($purchase * $quotation->packaging_percent) / 100;
// $insurance_amount       = ($purchase * $quotation->insurance_percent) / 100;
// $price_variation_amount = ($purchase * $quotation->price_variation_percent) / 100;

// $base_total =
//     $purchase +
//     $fittings_amount +
//     $paint_amount +
//     $transportation_amount +
//     $overhead_amount +
//     $ho_expenses_amount +
//     $ld_amount +
//     $packaging_amount +
//     $insurance_amount +
//     $price_variation_amount;

// $profit_amount = ($base_total * $quotation->profit_percent) / 100;

// $cushion_base = $base_total + $profit_amount;
// $cushion_amount = ($cushion_base * $quotation->cushion_percent) / 100;

// $bds_base = $cushion_base + $cushion_amount;
// $bds_amount = ($bds_base * $quotation->bds_percent) / 100;

// $supply =
//     $base_total +
//     $profit_amount +
//     $cushion_amount +
//     $bds_amount;

// $total_extra =
//     $fittings_amount +
//     $paint_amount +
//     $transportation_amount +
//     $overhead_amount +
//     $ho_expenses_amount +
//     $ld_amount +
//     $packaging_amount +
//     $insurance_amount +
//     $price_variation_amount +
//     $profit_amount +
//     $cushion_amount +
//     $bds_amount;

//             /*
//             -------------------------
//             ERECTION CALCULATION
//             -------------------------
//             */

//             $labour_cost = null;
//             $labour_amount = 0;
//             $erection_rate = 0;
//             $total_erection_extra = 0;

//             $consumables_amount = null;
//             $ppe_amount = null;
//             $supervision_amount = null;
//             $site_mobilization_amount = null;
//             $ld_labour_amount = null;
//             $labour_insurance_amount = null;
//             $erection_profit_amount = null;
//             $price_variation_labour_amount = null;
//             $bds_labour_amount = null;
//             $cushion_labour_amount = null;

//             if ($quotation->quotation_type == 'supply_erection') {

//                 $product = Product::find($item['product_id']);

//                 $labour_cost = $product->labour_cost ?? 0;

//                 $labour_amount = $labour_cost * $item['qty'];


//                 $consumables_amount        = ($labour_amount * $quotation->consumables_percent) / 100;
// $ppe_amount                = ($labour_amount * $quotation->ppe_percent) / 100;
// $supervision_amount        = ($labour_amount * $quotation->supervision_percent) / 100;
// $site_mobilization_amount  = ($labour_amount * $quotation->site_mobilization_percent) / 100;
// $ld_labour_amount          = ($labour_amount * $quotation->ld_labour_percent) / 100;
// $labour_insurance_amount   = ($labour_amount * $quotation->labour_insurance_percent) / 100;
// $price_variation_labour_amount =
//     ($labour_amount * $quotation->price_variation_labour_percent) / 100;

// $labour_base =
//     $labour_amount +
//     $consumables_amount +
//     $ppe_amount +
//     $supervision_amount +
//     $site_mobilization_amount +
//     $ld_labour_amount +
//     $labour_insurance_amount +
//     $price_variation_labour_amount;

// $erection_profit_amount =
//     ($labour_base * $quotation->erection_profit_percent) / 100;

// $cushion_labour_base =
//     $labour_base + $erection_profit_amount;

// $cushion_labour_amount =
//     ($cushion_labour_base * $quotation->cushion_labour_percent) / 100;

// $bds_labour_base =
//     $cushion_labour_base + $cushion_labour_amount;

// $bds_labour_amount =
//     ($bds_labour_base * $quotation->bds_labour_percent) / 100;

// $erection_rate =
//     $labour_base +
//     $erection_profit_amount +
//     $cushion_labour_amount +
//     $bds_labour_amount;

// $total_erection_extra =
//     $consumables_amount +
//     $ppe_amount +
//     $supervision_amount +
//     $site_mobilization_amount +
//     $ld_labour_amount +
//     $labour_insurance_amount +
//     $price_variation_labour_amount +
//     $erection_profit_amount +
//     $cushion_labour_amount +
//     $bds_labour_amount;
//             }

//             /*
//             -------------------------
//             FINAL TOTAL
//             -------------------------
//             */

//             // $before_gst = $supply + $erection_rate;

//             // $gst = ($before_gst * $item['gst_percent']) / 100;

//             // $total = $before_gst + $gst;
//             /*
// --------------------------------
// GST CALCULATION
// --------------------------------
// */

// $supply_gst_amount = ($supply * $item['gst_percent']) / 100;

// $erection_gst_amount = ($erection_rate * $item['gst_percent']) / 100;

// $gst = $supply_gst_amount + $erection_gst_amount;

// /*
// --------------------------------
// FINAL TOTAL
// --------------------------------
// */

// $before_gst = $supply + $erection_rate;

// $total = $before_gst + $gst;

//             QuotationItem::create([
//                 'quotation_id' => $quotation->id,
//                 'product_id' => $item['product_id'],

//                 'base_price' => $item['base_price'],
//                 'qty' => $item['qty'],
//                 'purchase_amount' => $purchase,

//                 'fittings_amount' => $fittings_amount,
//                 'paint_amount' => $paint_amount,
//                 'transportation_amount' => $transportation_amount,
//                 'overhead_amount' => $overhead_amount,
//                 'ho_expenses_amount' => $ho_expenses_amount,
//                 'ld_amount' => $ld_amount,
//                 'packaging_amount' => $packaging_amount,
//                 'insurance_amount' => $insurance_amount,
//                 'profit_amount' => $profit_amount,
//                 'price_variation_amount' => $price_variation_amount,
//                 'bds_amount' => $bds_amount,
//                 'cushion_amount' => $cushion_amount,

//                 'total_extra_amount' => $total_extra,
//                 'supply_rate' => $supply,

//                 'labour_cost' => $labour_cost,
//                 'labour_amount' => $labour_amount,

//                 'consumables_amount' => $consumables_amount,
//                 'ppe_amount' => $ppe_amount,
//                 'supervision_amount' => $supervision_amount,
//                 'site_mobilization_amount' => $site_mobilization_amount,
//                 'ld_labour_amount' => $ld_labour_amount,
//                 'labour_insurance_amount' => $labour_insurance_amount,
//                 'erection_profit_amount' => $erection_profit_amount,
//                 'price_variation_labour_amount' => $price_variation_labour_amount,
//                 'bds_labour_amount' => $bds_labour_amount,
//                 'cushion_labour_amount' => $cushion_labour_amount,

//                 'total_erection_extra' => $total_erection_extra,
//                 'erection_rate' => $erection_rate,
// // new add gst
//                 'supply_gst_amount' => $supply_gst_amount,
//                 'erection_gst_amount' => $erection_gst_amount,


//                 'gst_percent' => $item['gst_percent'],
//                 'gst_amount' => $gst,
//                 'total_amount' => $total
//             ]);

//             // $subtotal += $before_gst;
//             // $totalGst += $gst;
//             $subtotal += $before_gst;
// $totalGst += $supply_gst_amount + $erection_gst_amount;
//         }

//         // $quotation->update([
//         //     'subtotal' => $subtotal,
//         //     'total_gst' => $totalGst,
//         //     'grand_total' => $subtotal + $totalGst
//         // ]);
//         $quotation->update([
//             'subtotal' => $subtotal,
//             'total_gst' => $totalGst,
//             'grand_total' => $subtotal + $totalGst
//         ]);
//     });

//     return response()->json([
//         'success' => true,
//         'message' => 'Quotation Updated Successfully'
//     ]);
// }


}
