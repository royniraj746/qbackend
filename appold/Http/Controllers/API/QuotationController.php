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
// class QuotationController extends Controller
// {
//     //

//     public function store(Request $request)
// {
//     DB::transaction(function () use ($request) {

//         $enquiry = Enquiry::with('enquirycustomer')->findOrFail($request->enquiry_id);

//         $quotation = Quotation::create([
//             'enquiry_id' => $enquiry->id,
//             'enquirycustomer_id' => $enquiry->enquirycustomer_id,
//             'quotation_no' => 'QT-' . time(),
//             'quotation_date' => now(),

//             ...$request->only([
//                 'fittings_percent','paint_percent','transportation_percent',
//                 'overhead_percent','ho_expenses_percent','ld_percent',
//                 'packaging_percent','insurance_percent','profit_percent',
//                 'price_variation_percent','bds_percent','cushion_percent'
//             ])
//         ]);

//         $subtotal = $totalGst = 0;

//         foreach ($request->items as $item) {

//             $purchase = $item['base_price'] * $item['qty'];

//             $extra = function($percent) use ($purchase) {
//                 return ($purchase * $percent) / 100;
//             };

//             $totalExtra =
//                 $extra($quotation->fittings_percent) +
//                 $extra($quotation->paint_percent) +
//                 $extra($quotation->transportation_percent) +
//                 $extra($quotation->overhead_percent) +
//                 $extra($quotation->ho_expenses_percent) +
//                 $extra($quotation->ld_percent) +
//                 $extra($quotation->packaging_percent) +
//                 $extra($quotation->insurance_percent) +
//                 $extra($quotation->profit_percent) +
//                 $extra($quotation->price_variation_percent) +
//                 $extra($quotation->bds_percent) +
//                 $extra($quotation->cushion_percent);

//             $supply = $purchase + $totalExtra;
//             $gstAmount = ($supply * $item['gst_percent']) / 100;

//             QuotationItem::create([
//                 'quotation_id' => $quotation->id,
//                 'product_id' => $item['product_id'],
//                 'base_price' => $item['base_price'],
//                 'qty' => $item['qty'],
//                 'purchase_amount' => $purchase,

//                 'profit_amount' => $extra($quotation->profit_percent),
//                 'total_extra_amount' => $totalExtra,

//                 'supply_rate' => $supply,
//                 'gst_percent' => $item['gst_percent'],
//                 'gst_amount' => $gstAmount,
//                 'total_amount' => $supply + $gstAmount
//             ]);

//             $subtotal += $supply;
//             $totalGst += $gstAmount;
//         }

//         $quotation->update([
//             'subtotal' => $subtotal,
//             'total_gst' => $totalGst,
//             'grand_total' => $subtotal + $totalGst
//         ]);
//     });

//     return response()->json(['message' => 'Quotation created']);
// }

// // public function show($id)
// // {
// //     $quotation = Quotation::with([
// //         'items.product',
// //         'customer'
// //     ])->findOrFail($id);

// //     return response()->json([
// //         'status' => true,
// //         'data' => $quotation
// //     ]);
// // }
// public function show($id)
// {
//     $quotation = Quotation::with([
//         'items.product',
//         'customer',
//         'enquiry' // only if relation exists
//     ])->where('id', $id)->first();

//     if (!$quotation) {
//         return response()->json([
//             'status' => false,
//             'message' => 'Quotation not found'
//         ], 404);
//     }

//     return response()->json([
//         'status' => true,
//         'data' => $quotation
//     ]);
// }

// public function print($id)
// {
//     $quotation = Quotation::with(['items.product','customer'])->findOrFail($id);

//     $pdf = PDF::loadView('pdf.quotation', compact('quotation'));
//     return $pdf->download("Quotation-{$quotation->quotation_no}.pdf");
// }

// }

class QuotationController extends Controller
{
    // $user = auth()->user();

    // public function store(Request $request)
    // {


    //     DB::transaction(function () use ($request) {
    //         $user= auth()->user();
    //         Log::info("user check",[$user]);
    //         $enquiry = Enquiry::with('enquirycustomer')
    //             ->where('id',$request->enquiry_id)
    //             ->first();

    //         if(!$enquiry){
    //             abort(422,'Invalid enquiry id');
    //         }

    //         $quotation = Quotation::create([
    //             'enquiry_id' => $enquiry->id,
    //             'enquirycustomer_id' => $enquiry->enquiry_customer_id,
    //             'quotation_no' => 'QT-'.time(),
    //             'quotation_date' => now(),
    //             'created_by'=>$user->id,
    //             ...$request->except('items')
    //         ]);

    //         $subtotal = $totalGst = 0;

    //         foreach($request->items as $item){
    //             $purchase = $item['base_price'] * $item['qty'];

    //             $percentSum = collect([
    //                 $quotation->fittings_percent,
    //                 $quotation->paint_percent,
    //                 $quotation->transportation_percent,
    //                 $quotation->overhead_percent,
    //                 $quotation->ho_expenses_percent,
    //                 $quotation->ld_percent,
    //                 $quotation->packaging_percent,
    //                 $quotation->insurance_percent,
    //                 $quotation->profit_percent,
    //                 $quotation->price_variation_percent,
    //                 $quotation->bds_percent,
    //                 $quotation->cushion_percent,
    //             ])->sum();

    //             $extra = ($purchase * $percentSum) / 100;
    //             $supply = $purchase + $extra;
    //             $gst = ($supply * $item['gst_percent']) / 100;

    //             QuotationItem::create([
    //                 'quotation_id'=>$quotation->id,
    //                 'product_id'=>$item['product_id'],
    //                 'base_price'=>$item['base_price'],
    //                 'qty'=>$item['qty'],
    //                 'purchase_amount'=>$purchase,
    //                 'total_extra_amount'=>$extra,
    //                 'supply_rate'=>$supply,
    //                 'gst_percent'=>$item['gst_percent'],
    //                 'gst_amount'=>$gst,
    //                 'total_amount'=>$supply + $gst
    //             ]);

    //             $subtotal += $supply;
    //             $totalGst += $gst;
    //         }

    //         $quotation->update([
    //             'subtotal'=>$subtotal,
    //             'total_gst'=>$totalGst,
    //             'grand_total'=>$subtotal + $totalGst
    //         ]);
    //     });

    //     return response()->json(['message'=>'Quotation Created']);
    // }
///above this working well

// public function store(Request $request)
// {
//     DB::transaction(function () use ($request) {

//         $user = auth()->user();
//         Log::info("user check", [$user]);

//         $enquiry = Enquiry::with('enquirycustomer')
//             ->where('id', $request->enquiry_id)
//             ->first();

//         if (!$enquiry) {
//             abort(422, 'Invalid enquiry id');
//         }
//         if ($enquiry) {
//             $enquiry->status = 'Quoted';
//             $enquiry->save();
//         }
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

//             $purchase = $item['base_price'] * $item['qty'];

//             // 🔹 Individual charge calculations
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

//             // 🔹 Total extra
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
//             $gst = ($supply * $item['gst_percent']) / 100;
//             $total = $supply + $gst;

//             QuotationItem::create([
//                 'quotation_id'           => $quotation->id,
//                 'product_id'             => $item['product_id'],
//                 'base_price'             => $item['base_price'],
//                 'qty'                    => $item['qty'],
//                 'purchase_amount'        => $purchase,

//                 'fittings_amount'        => $fittings_amount,
//                 'paint_amount'           => $paint_amount,
//                 'transportation_amount'  => $transportation_amount,
//                 'overhead_amount'        => $overhead_amount,
//                 'ho_expenses_amount'     => $ho_expenses_amount,
//                 'ld_amount'              => $ld_amount,
//                 'packaging_amount'       => $packaging_amount,
//                 'insurance_amount'       => $insurance_amount,
//                 'profit_amount'          => $profit_amount,
//                 'price_variation_amount' => $price_variation_amount,
//                 'bds_amount'             => $bds_amount,
//                 'cushion_amount'         => $cushion_amount,

//                 'total_extra_amount'     => $total_extra,
//                 'supply_rate'            => $supply,
//                 'gst_percent'            => $item['gst_percent'],
//                 'gst_amount'             => $gst,
//                 'total_amount'           => $total
//             ]);

//             $subtotal += $supply;
//             $totalGst += $gst;
//         }

//         $quotation->update([
//             'subtotal'    => $subtotal,
//             'total_gst'   => $totalGst,
//             'grand_total' => $subtotal + $totalGst
//         ]);
//     });

//     return response()->json(['message' => 'Quotation Created']);
// }



public function store(Request $request)
{
    DB::transaction(function () use ($request) {

        $user = auth()->user();

        $enquiry = Enquiry::with('enquirycustomer')
            ->where('id', $request->enquiry_id)
            ->first();

        if (!$enquiry) {
            abort(422, 'Invalid enquiry id');
        }

        $enquiry->status = 'Quoted';
        $enquiry->save();

        $quotation = Quotation::create([
            'enquiry_id' => $enquiry->id,
            'enquirycustomer_id' => $enquiry->enquiry_customer_id,
            'quotation_no' => 'QT-' . time(),
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

            $fittings_amount        = ($purchase * $quotation->fittings_percent) / 100;
            $paint_amount           = ($purchase * $quotation->paint_percent) / 100;
            $transportation_amount  = ($purchase * $quotation->transportation_percent) / 100;
            $overhead_amount        = ($purchase * $quotation->overhead_percent) / 100;
            $ho_expenses_amount     = ($purchase * $quotation->ho_expenses_percent) / 100;
            $ld_amount              = ($purchase * $quotation->ld_percent) / 100;
            $packaging_amount       = ($purchase * $quotation->packaging_percent) / 100;
            $insurance_amount       = ($purchase * $quotation->insurance_percent) / 100;
            $profit_amount          = ($purchase * $quotation->profit_percent) / 100;
            $price_variation_amount = ($purchase * $quotation->price_variation_percent) / 100;
            $bds_amount             = ($purchase * $quotation->bds_percent) / 100;
            $cushion_amount         = ($purchase * $quotation->cushion_percent) / 100;

            $total_extra = collect([
                $fittings_amount,
                $paint_amount,
                $transportation_amount,
                $overhead_amount,
                $ho_expenses_amount,
                $ld_amount,
                $packaging_amount,
                $insurance_amount,
                $profit_amount,
                $price_variation_amount,
                $bds_amount,
                $cushion_amount
            ])->sum();

            $supply = $purchase + $total_extra;

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

                $consumables_amount = ($labour_amount * $quotation->consumables_percent) / 100;
                $ppe_amount = ($labour_amount * $quotation->ppe_percent) / 100;
                $supervision_amount = ($labour_amount * $quotation->supervision_percent) / 100;
                $site_mobilization_amount = ($labour_amount * $quotation->site_mobilization_percent) / 100;
                $ld_labour_amount = ($labour_amount * $quotation->ld_labour_percent) / 100;
                $labour_insurance_amount = ($labour_amount * $quotation->labour_insurance_percent) / 100;
                $erection_profit_amount = ($labour_amount * $quotation->erection_profit_percent) / 100;
                $price_variation_labour_amount = ($labour_amount * $quotation->price_variation_labour_percent) / 100;
                $bds_labour_amount = ($labour_amount * $quotation->bds_labour_percent) / 100;
                $cushion_labour_amount = ($labour_amount * $quotation->cushion_labour_percent) / 100;

                $total_erection_extra = collect([
                    $consumables_amount,
                    $ppe_amount,
                    $supervision_amount,
                    $site_mobilization_amount,
                    $ld_labour_amount,
                    $labour_insurance_amount,
                    $erection_profit_amount,
                    $price_variation_labour_amount,
                    $bds_labour_amount,
                    $cushion_labour_amount
                ])->sum();

                $erection_rate = $labour_amount + $total_erection_extra;
            }

            /*
            --------------------------------
            FINAL TOTAL
            --------------------------------
            */

            // $before_gst = $supply + $erection_rate;

            // $gst = ($before_gst * $item['gst_percent']) / 100;

            // $total = $before_gst + $gst;

            /*
--------------------------------
GST CALCULATION
--------------------------------
*/

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

                'base_price' => $item['base_price'],
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
            'grand_total' => $subtotal + $totalGst
        ]);
        // $quotation->update([
        //     'subtotal' => $subtotal,
        //     'total_gst' => $totalGst,
        //     'grand_total' => $subtotal + $totalGst
        // ]);
    });

    return response()->json([
        'message' => 'Quotation Created Successfully'
    ]);
}



    // customer',
    // 'Enquirycustomer',
    // 'enquiry.enquirycustomer'
    public function index()
    {
        $quotations = Quotation::with([
            'enquiry.enquirycustomer.createdBy',
            'enquiry.createdBy',
            'createdBy'

        ])->latest()->get();

        return response()->json($quotations);
    }
    public function show($id)
    {
        $quotation = Quotation::with([
            'items.product',
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

    public function excel($id)
    {
        $quotation = Quotation::with([
            'items.product',
            'items.projectcategries',
            'customer',
            'enquiry'
        ])->findOrFail($id);

        return Excel::download(
            new QuotationExport($quotation),
            'Quotation-'.$quotation->quotation_no.'.xlsx'
        );
    }




//upper wala updat working well
// public function update(Request $request, $id)
// {
//     $request->validate([
//         'items' => 'required|array|min:1',
//         'items.*.product_id' => 'required|integer',
//         'items.*.base_price' => 'required|numeric|min:0',
//         'items.*.qty' => 'required|numeric|min:1',
//         'items.*.gst_percent' => 'required|numeric|min:0',

//         'fittings_percent' => 'nullable|numeric|min:0',
//         'paint_percent' => 'nullable|numeric|min:0',
//         'transportation_percent' => 'nullable|numeric|min:0',
//         'overhead_percent' => 'nullable|numeric|min:0',
//         'ho_expenses_percent' => 'nullable|numeric|min:0',
//         'ld_percent' => 'nullable|numeric|min:0',
//         'packaging_percent' => 'nullable|numeric|min:0',
//         'insurance_percent' => 'nullable|numeric|min:0',
//         'profit_percent' => 'nullable|numeric|min:0',
//         'price_variation_percent' => 'nullable|numeric|min:0',
//         'bds_percent' => 'nullable|numeric|min:0',
//         'cushion_percent' => 'nullable|numeric|min:0',
//     ]);

//     DB::transaction(function () use ($request, $id) {

//         $quotation = Quotation::with('items')->findOrFail($id);

//         // 🔒 Protect enquiry_id & quotation_no
//         $quotation->update(
//             $request->except(['items', 'enquiry_id', 'quotation_no'])
//         );

//         // ❌ Delete old items
//         $quotation->items()->delete();

//         $subtotal = 0;
//         $totalGst = 0;

//         foreach ($request->items as $item) {

//             $purchase = $item['base_price'] * $item['qty'];

//             // 🔹 Individual charge calculations
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
//             $gst = ($supply * $item['gst_percent']) / 100;
//             $total = $supply + $gst;

//             QuotationItem::create([
//                 'quotation_id'           => $quotation->id,
//                 'product_id'             => $item['product_id'],
//                 'base_price'             => $item['base_price'],
//                 'qty'                    => $item['qty'],
//                 'purchase_amount'        => $purchase,

//                 'fittings_amount'        => $fittings_amount,
//                 'paint_amount'           => $paint_amount,
//                 'transportation_amount'  => $transportation_amount,
//                 'overhead_amount'        => $overhead_amount,
//                 'ho_expenses_amount'     => $ho_expenses_amount,
//                 'ld_amount'              => $ld_amount,
//                 'packaging_amount'       => $packaging_amount,
//                 'insurance_amount'       => $insurance_amount,
//                 'profit_amount'          => $profit_amount,
//                 'price_variation_amount' => $price_variation_amount,
//                 'bds_amount'             => $bds_amount,
//                 'cushion_amount'         => $cushion_amount,

//                 'total_extra_amount'     => $total_extra,
//                 'supply_rate'            => $supply,
//                 'gst_percent'            => $item['gst_percent'],
//                 'gst_amount'             => $gst,
//                 'total_amount'           => $total
//             ]);

//             $subtotal += $supply;
//             $totalGst += $gst;
//         }

//         $quotation->update([
//             'subtotal'    => $subtotal,
//             'total_gst'   => $totalGst,
//             'grand_total' => $subtotal + $totalGst
//         ]);
//     });

//     return response()->json([
//         'success' => true,
//         'message' => 'Quotation Updated Successfully'
//     ]);
// }



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

        // delete old items
        $quotation->items()->delete();

        $subtotal = 0;
        $totalGst = 0;

        foreach ($request->items as $item) {

            /*
            -------------------------
            SUPPLY CALCULATION
            -------------------------
            */

            $purchase = $item['base_price'] * $item['qty'];

            $fittings_amount        = ($purchase * $quotation->fittings_percent) / 100;
            $paint_amount           = ($purchase * $quotation->paint_percent) / 100;
            $transportation_amount  = ($purchase * $quotation->transportation_percent) / 100;
            $overhead_amount        = ($purchase * $quotation->overhead_percent) / 100;
            $ho_expenses_amount     = ($purchase * $quotation->ho_expenses_percent) / 100;
            $ld_amount              = ($purchase * $quotation->ld_percent) / 100;
            $packaging_amount       = ($purchase * $quotation->packaging_percent) / 100;
            $insurance_amount       = ($purchase * $quotation->insurance_percent) / 100;
            $profit_amount          = ($purchase * $quotation->profit_percent) / 100;
            $price_variation_amount = ($purchase * $quotation->price_variation_percent) / 100;
            $bds_amount             = ($purchase * $quotation->bds_percent) / 100;
            $cushion_amount         = ($purchase * $quotation->cushion_percent) / 100;

            $total_extra = collect([
                $fittings_amount,
                $paint_amount,
                $transportation_amount,
                $overhead_amount,
                $ho_expenses_amount,
                $ld_amount,
                $packaging_amount,
                $insurance_amount,
                $profit_amount,
                $price_variation_amount,
                $bds_amount,
                $cushion_amount
            ])->sum();

            $supply = $purchase + $total_extra;

            /*
            -------------------------
            ERECTION CALCULATION
            -------------------------
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

                $consumables_amount = ($labour_amount * $quotation->consumables_percent) / 100;
                $ppe_amount = ($labour_amount * $quotation->ppe_percent) / 100;
                $supervision_amount = ($labour_amount * $quotation->supervision_percent) / 100;
                $site_mobilization_amount = ($labour_amount * $quotation->site_mobilization_percent) / 100;
                $ld_labour_amount = ($labour_amount * $quotation->ld_labour_percent) / 100;
                $labour_insurance_amount = ($labour_amount * $quotation->labour_insurance_percent) / 100;
                $erection_profit_amount = ($labour_amount * $quotation->erection_profit_percent) / 100;
                $price_variation_labour_amount = ($labour_amount * $quotation->price_variation_labour_percent) / 100;
                $bds_labour_amount = ($labour_amount * $quotation->bds_labour_percent) / 100;
                $cushion_labour_amount = ($labour_amount * $quotation->cushion_labour_percent) / 100;

                $total_erection_extra = collect([
                    $consumables_amount,
                    $ppe_amount,
                    $supervision_amount,
                    $site_mobilization_amount,
                    $ld_labour_amount,
                    $labour_insurance_amount,
                    $erection_profit_amount,
                    $price_variation_labour_amount,
                    $bds_labour_amount,
                    $cushion_labour_amount
                ])->sum();

                $erection_rate = $labour_amount + $total_erection_extra;
            }

            /*
            -------------------------
            FINAL TOTAL
            -------------------------
            */

            // $before_gst = $supply + $erection_rate;

            // $gst = ($before_gst * $item['gst_percent']) / 100;

            // $total = $before_gst + $gst;
            /*
--------------------------------
GST CALCULATION
--------------------------------
*/

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

                'base_price' => $item['base_price'],
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
// new add gst
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

        // $quotation->update([
        //     'subtotal' => $subtotal,
        //     'total_gst' => $totalGst,
        //     'grand_total' => $subtotal + $totalGst
        // ]);
        $quotation->update([
            'subtotal' => $subtotal,
            'total_gst' => $totalGst,
            'grand_total' => $subtotal + $totalGst
        ]);
    });

    return response()->json([
        'success' => true,
        'message' => 'Quotation Updated Successfully'
    ]);
}

}
