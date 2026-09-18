<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BrandController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\EnquiryController;
use App\Http\Controllers\API\EnquiryCustomerController;
use App\Http\Controllers\API\GstController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\API\QuotationController;
use App\Http\Controllers\API\RolePermissionController;
use App\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->group(function () {
    // Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/me' ,[authController::class, 'me']);

});

// Route::get('/dashboard', [DashboardController::class, 'index'])
//         ->middleware('check.permission:dashboard.view');

// admin routes
Route::middleware(['auth:sanctum', 'role:admin'])->group( function () {

    Route::prefix('users')->group(function() {
        Route::get('/', [UserController::class, 'index']);        // List users
        // Route::post('/', [UserController::class, 'register']);   // Create user
        Route::put('/{id}', [UserController::class, 'update']);  // Update user
        Route::delete('/{id}', [UserController::class, 'destroy']); // Delete user
    });


});






Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/admin/roles', [RolePermissionController::class, 'roles']);
    Route::get('/admin/permissions', [RolePermissionController::class, 'permissions']);
    Route::post('/admin/permissions/assign', [RolePermissionController::class, 'assign']);
    Route::get('/admin/roles/{id}/permissions', [RolePermissionController::class, 'rolePermissions']);

});
Route::post('/products/bulk-upload', [ProductController::class, 'bulkUpload']);
Route::apiResource('categories', CategoryController::class);
Route::apiResource('products', ProductController::class);
Route::apiResource('brands', BrandController::class);


Route::middleware(['auth:sanctum'])->group(function () {


    Route::get('enquiry-customer/by-phone/{mobile}',
    [EnquiryCustomerController::class,'findByPhone']);
    Route::get('/enquiries', [EnquiryController::class, 'index']);
    Route::put('/enquiries/{id}', [EnquiryController::class, 'update']);
    Route::delete('/enquiries/{id}', [EnquiryController::class, 'destroy']);

  Route::post('enquiries',
    [EnquiryController::class,'store']);



    Route::prefix('enquiry-customers')->group(function () {
        Route::get('/', [EnquiryCustomerController::class, 'index']);
        Route::post('/', [EnquiryCustomerController::class, 'store']);
        Route::get('{id}', [EnquiryCustomerController::class, 'show']);
        Route::put('{id}', [EnquiryCustomerController::class, 'update']);
        Route::delete('{id}', [EnquiryCustomerController::class, 'destroy']);
    });


    //quotation routes
//     Route::post('/quotations', [QuotationController::class,'store']);
// Route::get('/quotations/{id}', [QuotationController::class,'show']);
// Route::get('/quotations/{id}/print', [QuotationController::class, 'print']);


Route::get('/quotations',[QuotationController::class,'index']);
Route::post('/quotations',[QuotationController::class,'store']);
Route::get('/quotations/{id}',[QuotationController::class,'show']);
// Route::get('/quotations/{id}/print',[QuotationController::class,'print']);
//gst route here
Route::apiResource('gsts', GstController::class);
// Route::get('/quotations', [QuotationController::class, 'index']);
// Route::get('/quotations/{id}', [QuotationController::class, 'show']);
Route::get('/quotations/{id}/print', [QuotationController::class, 'print']);
// Route::get('/quotations/{id}/excel', [QuotationController::class, 'excel']);
Route::put('/quotations/{id}', [QuotationController::class, 'update']);
});
Route::get('/quotations/{id}/excel', [QuotationController::class, 'excel']);
// Route::get('enquiry-customer/by-phone/{mobile}',
//   [EnquiryCustomerController::class,'findByPhone']);

// Route::post('enquiries',
//   [EnquiryController::class,'store']);



//user routes
Route::middleware(['auth:sanctum', 'role:user'])->get('/user/dashboard', function () {
    return 'User Dashboard';
});
