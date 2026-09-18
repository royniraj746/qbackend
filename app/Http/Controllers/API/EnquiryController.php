<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Enquiry;
use App\Models\Customer;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\EnquiryCustomer;
use Illuminate\Support\Facades\DB;




use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class EnquiryController extends Controller
{

    public function store(Request $request)
{
    DB::beginTransaction();

    try {
        /* ---------------- VALIDATION ---------------- */
        $validated = $request->validate([
            'customer_name'      => 'required|string|max:255',
            'mobile'             => 'nullable|digits:10', // मोबाइल नंबर अब पूरी तरह नलेबल है
            'email'              => 'nullable|email',
            'business_name'      => 'nullable|string|max:255',
            'business_category'  => 'nullable|string|max:255',

            'gst_number'         => 'nullable|string|max:20',
            'pan_number'         => 'nullable|string|max:20',
            'adhar_number'       => 'nullable|string|max:20',

            'address'            => 'nullable|string',
            'city'               => 'nullable|string|max:100',
            'state'              => 'nullable|string|max:100',
            'pincode'            => 'nullable|string|max:10',
            'country'            => 'nullable|string|max:100',

            'enquiry_type'       => 'required|string|max:100',
            'lead_source'        => 'required|string|max:100',
            'status'             => 'nullable|string|max:50',
            'remarks'            => 'nullable|string',
        ]);

        Log::info("Enquiry creation initiated by User ID: " . auth()->id());

        /* ---------------- CUSTOMER HANDLING ---------------- */
        $customer = null;

        // यदि मोबाइल नंबर प्रदान किया गया है, तो मौजूदा रिकॉर्ड की जाँच करें
        if (!empty($validated['mobile'])) {
            $customer = EnquiryCustomer::where('mobile', $validated['mobile'])->first();
        }

        if ($customer) {
            // ✅ पुराने कस्टमर का डेटा अपडेट करें
            $customer->update([
                'customer_name'     => $validated['customer_name'],
                'email'             => $validated['email'] ?? null,
                'business_name'     => $validated['business_name'] ?? null,
                'business_category' => $validated['business_category'] ?? null,

                // 'gst_number'        => $validated['gst_number'] ?? null,
                // 'pan_number'        => $validated['pan_number'] ?? null,
                // 'adhar_number'      => $validated['adhar_number'] ?? null,

                // 'address'           => $validated['address'] ?? null,
                // 'city'              => $validated['city'] ?? null,
                // 'state'             => $validated['state'] ?? null,
                // 'pincode'           => $validated['pincode'] ?? null,
                // 'country'           => $validated['country'] ?? 'India',

                'created_by'        => auth()->id(),
            ]);
        } else {
            // ✅ नया कस्टमर बनाएँ (यदि नंबर खाली है या डेटाबेस में उपलब्ध नहीं है)
            $customer = EnquiryCustomer::create([
                'customer_name'     => $validated['customer_name'],
                'mobile'            => $validated['mobile'] ?? null,
                'email'             => $validated['email'] ?? null,
                'business_name'     => $validated['business_name'] ?? null,
                'business_category' => $validated['business_category'] ?? null,

                'gst_number'        => $validated['gst_number'] ?? null,
                'pan_number'        => $validated['pan_number'] ?? null,
                'adhar_number'      => $validated['adhar_number'] ?? null,

                'address'           => $validated['address'] ?? null,
                'city'              => $validated['city'] ?? null,
                'state'             => $validated['state'] ?? null,
                'pincode'           => $validated['pincode'] ?? null,
                'country'           => $validated['country'] ?? 'India',

                'created_by'        => auth()->id(),
            ]);
        }

        /* ---------------- ENQUIRY CREATION ---------------- */
        $enquiry = Enquiry::create([
            'enquiry_code'        => 'ENQ-' . strtoupper(uniqid()),
            'enquiry_type'        => $validated['enquiry_type'],
            'lead_source'         => $validated['lead_source'] ?? null,
            'status'              => "Unquoted",
            'remarks'             => $validated['remarks'] ?? null,
            'enquiry_customer_id' => $customer->id,
            'created_by'          => auth()->id(),
        ]);

        DB::commit();

        return response()->json([
            'message'  => 'Enquiry created successfully',
            'enquiry'  => $enquiry,
            'customer' => $customer
        ], 201);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'error' => 'Something went wrong',
            'debug' => $e->getMessage()
        ], 500);
    }
}

// public function store(Request $request)
// {
//     DB::beginTransaction();

//     try {

//         /* ---------------- VALIDATION ---------------- */
//         $validated = $request->validate([
//             'customer_name'      => 'required|string|max:255',
//             // 'mobile'             => 'required|digits:10',
//             'mobile'             => 'nullable|digits:10',
//             'email'              => 'nullable|email',
//             'business_name'      => 'nullable|string|max:255',
//             'business_category'  => 'nullable|string|max:255',

//             'gst_number'         => 'nullable|string|max:20',
//             'pan_number'         => 'nullable|string|max:20',
//             'adhar_number'       => 'nullable|string|max:20',

//             'address'            => 'nullable|string',
//             'city'               => 'nullable|string|max:100',
//             'state'              => 'nullable|string|max:100',
//             'pincode'            => 'nullable|string|max:10',
//             'country'            => 'nullable|string|max:100',

//             'enquiry_type'       => 'required|string|max:100',
//             'lead_source'        => 'nullable|string|max:100',
//             'status'             => 'nullable|string|max:50',
//             'remarks'            => 'nullable|string',
//         ]);
// Log::info("auth()->id()",[auth()]);
//         /* ---------------- CUSTOMER ---------------- */
//         $customer = EnquiryCustomer::where('mobile', $validated['mobile'])->first();

//         if ($customer) {
//             // ✅ Update existing customer
//             $customer->update([
//                 'customer_name'     => $validated['customer_name'],
//                 'email'             => $validated['email'] ?? null,
//                 'business_name'     => $validated['business_name'] ?? null,
//                 'business_category' => $validated['business_category'] ?? null,

//                 'gst_number'        => $validated['gst_number'] ?? null,
//                 'pan_number'        => $validated['pan_number'] ?? null,
//                 'adhar_number'      => $validated['adhar_number'] ?? null,

//                 'address'           => $validated['address'] ?? null,
//                 'city'              => $validated['city'] ?? null,
//                 'state'             => $validated['state'] ?? null,
//                 'pincode'           => $validated['pincode'] ?? null,
//                 'country'           => $validated['country'] ?? 'India',

//                 'created_by'        => auth()->id(),
//             ]);
//         } else {
//             // ✅ Create new customer
//             $customer = EnquiryCustomer::create([
//                 'customer_name'     => $validated['customer_name'],
//                 'mobile'            => $validated['mobile'],
//                 'email'             => $validated['email'] ?? null,
//                 'business_name'     => $validated['business_name'] ?? null,
//                 'business_category' => $validated['business_category'] ?? null,

//                 'gst_number'        => $validated['gst_number'] ?? null,
//                 'pan_number'        => $validated['pan_number'] ?? null,
//                 'adhar_number'      => $validated['adhar_number'] ?? null,

//                 'address'           => $validated['address'] ?? null,
//                 'city'              => $validated['city'] ?? null,
//                 'state'             => $validated['state'] ?? null,
//                 'pincode'           => $validated['pincode'] ?? null,
//                 'country'           => $validated['country'] ?? 'India',

//                 'created_by'        => auth()->id(),
//             ]);
//         }

//         /* ---------------- ENQUIRY ---------------- */
//         $enquiry = Enquiry::create([
//             'enquiry_code'        => 'ENQ-' . strtoupper(uniqid()),
//             'enquiry_type'        => $validated['enquiry_type'],
//             'lead_source'         => $validated['lead_source'] ?? null,
//             // 'status'              => $validated['status'] ?? "Unquoted",
//             'status'              => "Unquoted",
//             'remarks'             => $validated['remarks'] ?? null,
//             'enquiry_customer_id' => $customer->id,

//             'created_by'        => auth()->id(),
//         ]);

//         DB::commit();

//         return response()->json([
//             'message'  => 'Enquiry created successfully',
//             'enquiry'  => $enquiry,
//             'customer' => $customer
//         ], 201);

//     } catch (\Exception $e) {
//         DB::rollBack();
//         return response()->json([
//             'error' => 'Something went wrong',
//             'debug' => $e->getMessage()
//         ], 500);
//     }
// }


public function update(Request $request, $id)
{
    DB::beginTransaction();

    try {

        $validated = $request->validate([
            'customer_name'      => 'required|string|max:255',
            'email'              => 'nullable|email',
            'business_name'      => 'nullable|string|max:255',
            'business_category'  => 'nullable|string|max:255',

            'gst_number'         => 'nullable|string|max:20',
            'pan_number'         => 'nullable|string|max:20',
            'adhar_number'       => 'nullable|string|max:20',

            'address'            => 'nullable|string',
            'city'               => 'nullable|string|max:100',
            'state'              => 'nullable|string|max:100',
            'pincode'            => 'nullable|string|max:10',
            'country'            => 'nullable|string|max:100',

            'enquiry_type'       => 'required|string|max:100',
            'lead_source'        => 'nullable|string|max:100',
            'status'             => 'required|string|max:50',
            'remarks'            => 'nullable|string',
        ]);

        $enquiry = Enquiry::with('enquirycustomer')->findOrFail($id);

        /* -------- UPDATE CUSTOMER -------- */
        $enquiry->enquirycustomer->update([
            'customer_name'     => $validated['customer_name'],
            'email'             => $validated['email'] ?? null,
            'business_name'     => $validated['business_name'] ?? null,
            'business_category' => $validated['business_category'] ?? null,

            'gst_number'        => $validated['gst_number'] ?? null,
            'pan_number'        => $validated['pan_number'] ?? null,
            'adhar_number'      => $validated['adhar_number'] ?? null,

            'address'           => $validated['address'] ?? null,
            'city'              => $validated['city'] ?? null,
            'state'             => $validated['state'] ?? null,
            'pincode'           => $validated['pincode'] ?? null,
            'country'           => $validated['country'] ?? 'India',
        ]);

        /* -------- UPDATE ENQUIRY -------- */
        $enquiry->update([
            'enquiry_type' => $validated['enquiry_type'],
            'lead_source'  => $validated['lead_source'] ?? null,
            'status'       => $validated['status'],
            'remarks'      => $validated['remarks'] ?? null,
            'created_by'   => auth()->id(),
        ]);

        DB::commit();

        return response()->json([
            'message' => 'Enquiry updated successfully',
            'data'    => $enquiry->load('enquirycustomer')
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'error' => 'Update failed',
            'debug' => $e->getMessage()
        ], 500);
    }
}



public function index(Request $request)
{
    try {

        $query = Enquiry::with('enquirycustomer',"createdBy")
            ->orderBy('id', 'desc');

        // 🔍 Filters
        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->enquiry_type) {
            $query->where('enquiry_type', $request->enquiry_type);
        }

        if ($request->mobile) {
            $query->whereHas('enquirycustomer', function ($q) use ($request) {
                $q->where('mobile', $request->mobile);
            });
        }
        // Search
if ($request->filled('search')) {

    $search = $request->search;

    $query->where(function ($q) use ($search) {

        $q->where('enquiry_code', 'like', "%{$search}%")

            ->orWhereHas('enquirycustomer', function ($customer) use ($search) {

                $customer->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%");

            })

            ->orWhereHas('createdBy', function ($user) use ($search) {

                $user->where('name', 'like', "%{$search}%");

            });

    });
}

// Status Filter
if ($request->filled('status')) {
    $query->where('status', $request->status);
}

// Date Range
if ($request->filled('start_date')) {
    $query->whereDate('created_at', '>=', $request->start_date);
}

if ($request->filled('end_date')) {
    $query->whereDate('created_at', '<=', $request->end_date);
}

// Per Page
$perPage = $request->per_page ?? 10;

$enquiries = $query->paginate($perPage);

        // $enquiries = $query->paginate(15);

        return response()->json($enquiries);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Failed to fetch enquiries',
            'debug' => $e->getMessage()
        ], 500);
    }
}


public function destroy($id)
{
    Enquiry::findOrFail($id)->delete();
    return response()->json(['message' => 'Deleted']);
}

}
