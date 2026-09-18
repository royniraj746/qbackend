<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EnquiryCustomer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EnquiryCustomerController extends Controller
{
    //

    public function findByPhone($mobile)
{
    $customer = EnquiryCustomer::where('mobile',$mobile)->first();

    return response()->json([
        'exists' => !!$customer,
        'data' => $customer
    ]);

}




// public function index()
// {
//     $data=EnquiryCustomer::with('createdBy')->get();


//     return response()->json([
//         'data'=>$data
//     ]);
// }
public function index(Request $request)
{
    $query = EnquiryCustomer::with('createdBy');

    // Search
    if ($request->filled('search')) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            $q->where('customer_name', 'like', "%{$search}%")
              ->orWhere('mobile', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('business_name', 'like', "%{$search}%")
              ->orWhere('state', 'like', "%{$search}%")
              ->orWhere('city', 'like', "%{$search}%");
        });
    }
// Date Filter
if ($request->filled('start_date')) {
    $query->whereDate('created_at', '>=', $request->start_date);
}

if ($request->filled('end_date')) {
    $query->whereDate('created_at', '<=', $request->end_date);
}
    $perPage = $request->per_page ?? 10;

    $data = $query
        ->latest()
        ->paginate($perPage);

    return response()->json($data);
}

/* ===== CREATE ===== */
public function store(Request $request)
{

    $user=auth()->user();
    Log::info("token166",[$user]);
    // $customer = EnquiryCustomer::create($request->all());
    $customer= EnquiryCustomer::create([...$request->all(),'created_by'=>$user->id]);
    return response()->json([
        'message' => 'Customer created',
        'data' => $customer
    ]);
}

/* ===== VIEW ===== */
public function show($id)
{
    return response()->json([
        'data' => EnquiryCustomer::findOrFail($id)
    ]);
}

/* ===== UPDATE ===== */
public function update(Request $request, $id)
{
    $customer = EnquiryCustomer::findOrFail($id);
    $customer->update($request->all());

    return response()->json([
        'message' => 'Customer updated',
        'data' => $customer
    ]);
}

/* ===== DELETE ===== */
public function destroy($id)
{
    EnquiryCustomer::findOrFail($id)->delete();

    return response()->json([
        'message' => 'Customer deleted'
    ]);
}
}
