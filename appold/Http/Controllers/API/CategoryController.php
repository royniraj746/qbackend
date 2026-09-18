<?php

namespace App\Http\Controllers\Api;


use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;



use Illuminate\Support\Str;
class CategoryController extends Controller
{
    //
    public function index()
    {
        return response()->json(
            Category::latest()->get()
        );
    }

    // 🔹 STORE
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = Category::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
        ]);

        return response()->json([
            'success' => true,
            'data' => $category
        ], 201);
    }

    // 🔹 SHOW
    public function show(Category $category)
    {
        return response()->json($category);
    }

    // 🔹 UPDATE
    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'boolean'
        ]);

        $category->update([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'is_active' => $data['is_active'] ?? $category->is_active,
        ]);

        return response()->json([
            'success' => true,
            'data' => $category
        ]);
    }

    // 🔹 DELETE
    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully'
        ]);
    }
}
