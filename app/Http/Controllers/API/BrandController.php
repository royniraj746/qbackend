<?php

namespace App\Http\Controllers\Api;

use App\Models\Brand;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BrandController extends Controller
{

    public function index()
    {
        return response()->json(
            Brand::latest()->get()
        );
    }

    // 🔹 STORE
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $brand = Brand::create([
            'name' => $data['name'],
        ]);

        return response()->json([
            'success' => true,
            'data' => $brand
        ], 201);
    }

    // 🔹 SHOW
    public function show(Brand $brand)
    {
        return response()->json($brand);
    }

    // 🔹 UPDATE
    public function update(Request $request, Brand $brand)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'boolean'
        ]);

        $brand->update($data);

        return response()->json([
            'success' => true,
            'data' => $brand
        ]);
    }

    // 🔹 DELETE
    public function destroy(Brand $brand)
    {
        $brand->delete();

        return response()->json([
            'success' => true,
            'message' => 'Brand deleted successfully'
        ]);
    }
    //
}
