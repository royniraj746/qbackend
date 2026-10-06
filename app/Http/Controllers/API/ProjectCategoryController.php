<?php

// namespace App\Http\Controllers\API;



// use App\Http\Controllers\Controller;
// use App\Models\ProjectCategory;
// use Illuminate\Http\Request;
// class ProjectCategoryController extends Controller
// {
//     //
//     public function index(Request $request)
//     {
//         $query = ProjectCategory::with('creator:id,name');

//         // Search by name or description
//         if ($request->has('search') && !empty($request->search)) {
//             $search = $request->search;
//             $query->where(function ($q) use ($search) {
//                 $q->where('name', 'like', "%{$search}%")
//                   ->orWhere('description', 'like', "%{$search}%");
//             });
//         }

//         if ($request->has('status') && !empty($request->status)) {
//             $query->where('status', $request->status);
//         }

//         $categories = $query->latest()->get();

//         return response()->json([
//             'success' => true,
//             'data' => $categories
//         ]);
//     }

//     // Store New Category
//     public function store(Request $request)
//     {
//         $validated = $request->validate([
//             'name' => 'required|string|max:255',
//             'description' => 'nullable|string',
//             'status' => 'required|in:active,inactive',
//         ]);

//         $validated['created_by'] = auth()->id() ?? 1; // Default to authenticated user

//         $category = ProjectCategory::create($validated);

//         return response()->json([
//             'success' => true,
//             'message' => 'Category created successfully!',
//             'data' => $category
//         ], 201);
//     }

//     // Update Category
//     public function update(Request $request, $id)
//     {
//         $category = ProjectCategory::findOrFail($id);

//         $validated = $request->validate([
//             'name' => 'required|string|max:255',
//             'description' => 'nullable|string',
//             'status' => 'required|in:active,inactive',
//         ]);

//         $category->update($validated);

//         return response()->json([
//             'success' => true,
//             'message' => 'Category updated successfully!',
//             'data' => $category
//         ]);
//     }

//     // Delete Category
//     public function destroy($id)
//     {
//         $category = ProjectCategory::findOrFail($id);
//         $category->delete();

//         return response()->json([
//             'success' => true,
//             'message' => 'Category deleted successfully!'
//         ]);
//     }
// }





namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ProjectCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectCategoryController extends Controller
{
    // Get Project Categories
    public function index(Request $request)
    {
        $query = ProjectCategory::with([
            'creator:id,name',
            'products'
        ]);

        // Search by name or description
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        $categories = $query->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }


    // Create New Project Category + Assign Products
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',

            // Products selected while creating category
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'integer|exists:products,id',
        ]);

        DB::beginTransaction();

        try {

            $validated['created_by'] = auth()->id() ?? 1;

            // Create Category
            $category = ProjectCategory::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'status' => $validated['status'],
                'created_by' => $validated['created_by'],
            ]);

            // Assign Products
            if (!empty($validated['product_ids'])) {
                $category->products()->sync($validated['product_ids']);
            }

            DB::commit();

            // Return category with products
            $category->load([
                'creator:id,name',
                'products'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Category created and products assigned successfully!',
                'data' => $category
            ], 201);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create category.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    // Update Project Category + Products
    public function update(Request $request, $id)
    {
        $category = ProjectCategory::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',

            // Products selected for category
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'integer|exists:products,id',
        ]);

        DB::beginTransaction();

        try {

            // Update Category
            $category->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'status' => $validated['status'],
            ]);

            // Update Product Assignment
            $category->products()->sync(
                $validated['product_ids'] ?? []
            );

            DB::commit();

            $category->load([
                'creator:id,name',
                'products'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Category and products updated successfully!',
                'data' => $category
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to update category.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    // Delete Project Category
    public function destroy($id)
    {
        $category = ProjectCategory::findOrFail($id);

        DB::beginTransaction();

        try {

            // Remove product assignments first
            $category->products()->detach();

            // Delete category
            $category->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully!'
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete category.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
