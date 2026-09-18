<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Container\Attributes\Log;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
class UserController extends Controller
{
    //


//

public function update(Request $request, $customerId)
{
    // Log::info('customer_id', ['customer_id' => $customerId]);

    $userrr = auth()->user();
    \Log::info('Authenticated User', ['user' => $userrr]);


    $request->validate([
        'name' => 'required|string|max:255',
        // email will not be updated
        'mobile' => 'required|string|max:15',
        'business_name' => 'nullable|string|max:255',
        'user_type' => 'nullable|in:individual,business',
        'gst_number' => 'nullable|string|max:20',
        'pan_number' => 'nullable|string|max:20',
        'adhar_number' => 'nullable|string|max:20',
        'address' => 'nullable|string',
        'state' => 'nullable|string|max:100',
        'city' => 'nullable|string|max:100',
        'pincode' => 'nullable|string|max:10',
    ]);

    DB::beginTransaction();

    try {
        // 1️⃣ Find customer first
        $customer = Customer::findOrFail($customerId);

        // 2️⃣ Get related user
        $user = User::findOrFail($customer->user_id);

        // 3️⃣ Update user (only name, email stays same)
        $user->update([
            'name' => $request->name,
        ]);

        // 4️⃣ Update customer
        $customer->update([
            'name' => $request->name,
            'mobile' => $request->mobile,
            'user_type' => $request->user_type ?? 'individual',
            'business_name' => $request->business_name,
            'gst_number' => $request->gst_number,
            'pan_number' => $request->pan_number,
            'adhar_number' => $request->adhar_number,
            'address' => $request->address,
            'state' => $request->state,
            'city' => $request->city,
            'pincode' => $request->pincode,
        ]);

        DB::commit();

        return response()->json([
            'status' => true,
            'message' => 'User updated successfully',
            'user' => $user,
            'customer' => $customer,
        ]);

    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'status' => false,
            'message' => 'Update failed',
            'error' => $e->getMessage(),
        ], 500);
    }
}


public function destroy($userId)
{
    DB::beginTransaction();

    try {
        $user = User::findOrFail($userId);
        $customer = Customer::where('user_id', $user->id)->first();

        if ($customer) {
            $customer->delete();
        }

        $user->delete();

        DB::commit();

        return response()->json([
            'status' => true,
            'message' => 'User deleted successfully',
        ]);

    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'status' => false,
            'message' => 'Delete failed',
            'error' => $e->getMessage(),
        ], 500);
    }
}


// public function index()
// {
//     try {
//         // Get all users with their customer info
//         $users = Customer::with('user')->get();

//         return response()->json([
//             'status' => true,
//             'message' => 'Users fetched successfully',
//             'data' => $users,
//         ]);

//     } catch (\Exception $e) {
//         return response()->json([
//             'status' => false,
//             'message' => 'Failed to fetch users',
//             'error' => $e->getMessage(),
//         ], 500);
//     }
// }

public function index(Request $request)
{
    try {

        $query = Customer::with('user')
            ->latest();

        // Search
        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%")
                    ->orWhere('business_name', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('state', 'like', "%{$search}%");
            });
        }

        // Start Date Filter
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        // End Date Filter
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $perPage = $request->input('per_page', 10);

        $customers = $query->paginate($perPage);

        return response()->json([
            'status' => true,
            'message' => 'Customers fetched successfully',
            'data' => $customers,
        ], 200);

    } catch (\Exception $e) {

        return response()->json([
            'status' => false,
            'message' => 'Failed to fetch customers',
            'error' => $e->getMessage(),
        ], 500);
    }
}
}
