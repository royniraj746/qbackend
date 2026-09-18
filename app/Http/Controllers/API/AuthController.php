<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // REGISTER



    public function register(Request $request)
{
    $request->validate([
        // USER
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6',

        // CUSTOMER
        'mobile' => 'required|string|max:15',
        'business_name' => 'nullable|string|max:255',
        'user_type' => 'nullable|in:individual,business,associate',
        'gst_number' => 'nullable|string|max:20',
        'pan_number' => 'nullable|string|max:20',
        'adhar_number' => 'nullable|string|max:20',

        'address' => 'nullable|string',
        'state' => 'nullable|string|max:100',
        'city' => 'nullable|string|max:100',
        'pincode' => 'nullable|string|max:10',

        'currency' => 'nullable|string|max:10',
        'default_tax' => 'nullable|numeric|min:0',
        'quotation_validity_days' => 'nullable|integer|min:1',
    ]);

    DB::beginTransaction();

    try {
       $createdBY=auth()->user()->id;
        /* ---------------- USER CREATE ---------------- */
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // $user->assignRole($request->role);
        $user->assignRole('user');

        /* ---------------- CUSTOMER CREATE ---------------- */
        $customer = Customer::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,

            'business_name' => $request->business_name,
            'user_type' => $request->user_type ?? 'individual',
            'gst_number' => $request->gst_number,
            'pan_number' => $request->pan_number,
            'adhar_number' => $request->adhar_number,

            'address' => $request->address,
            'state' => $request->state,
            'city' => $request->city,
            'pincode' => $request->pincode,
            'country' => 'India',

            'currency' => $request->currency ?? 'INR',
            'default_tax' => $request->default_tax ?? 0,
            'quotation_validity_days' => $request->quotation_validity_days ?? 15,

            "created_by" => $createdBY,
        ]);

        /* ---------------- TOKEN ---------------- */
        $token = $user->createToken('auth_token')->plainTextToken;

        DB::commit();

        return response()->json([
            'status' => true,
            'message' => 'User registered successfully',
            'token' => $token,
            'role' => $request->role,
            'user' => $user,
            'customer' => $customer,
        ], 201);

    } catch (\Exception $e) {

        DB::rollBack();

        return response()->json([
            'status' => false,
            'message' => 'Registration failed',
            'error' => $e->getMessage(),
        ], 500);
    }
}

    // LOGIN
    // public function login(Request $request)
    // {
    //     $request->validate([
    //         'email' => 'required|email',
    //         'password' => 'required',
    //     ]);

    //     $user = User::where('email', $request->email)->first();

    //     if (!$user || !Hash::check($request->password, $user->password)) {
    //         return response()->json(['message' => 'Invalid credentials'], 401);
    //     }

    //     $token = $user->createToken('auth_token')->plainTextToken;

    //     return response()->json([
    //         'status' => true,
    //         'token' => $token,
    //         'role' => $user->getRoleNames()->first(),
    //         'permissions' => $user->getAllPermissions()->pluck('name'),
    //         'user' => $user
    //     ]);
    // }

    public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    $token = $user->createToken('auth_token')->plainTextToken;

    // 🔥 MODULE-WISE PERMISSIONS
    $permissions = [];

    foreach ($user->getAllPermissions() as $permission) {
        // users.create → [users, create]
        [$module, $action] = explode('.', $permission->name);

        $permissions[$module][] = $action;
    }

    return response()->json([
        'status' => true,
        'token' => $token,
        'role' => $user->getRoleNames()->first(),
        'permissions' => $permissions,
        'user' => $user,
    ]);
}


    // LOGOUT
    public function logout(Request $request)
    {
        $user=auth()->user();
        if ($user) {
            $user->tokens()->delete();
        }
    // $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out']);
    }



public function me( Request $request){
    try{
        $user = auth()->user();
        return response()->json([
            'status' => true,
            'user' => $user,
            'role' => $user->getRoleNames()->first(),
            'permissions' => $request->user()->getAllPermissions()->pluck('name'),
        ]);
    }
    catch(\Exception $e){
        return response()->json([
            'status' => false,
            'message' => 'Failed to fetch user data',
            'error' => $e->getMessage(),
        ], 500);
}
}
}

