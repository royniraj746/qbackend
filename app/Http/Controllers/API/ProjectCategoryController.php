<?php

namespace App\Http\Controllers\API;



use App\Http\Controllers\Controller;
use App\Models\ProjectCategory;
use Illuminate\Http\Request;
class ProjectCategoryController extends Controller
{
    //
    public function index(Request $request)
    {
        $query = ProjectCategory::with('creator:id,name');

        // Search by name or description
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        $categories = $query->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    // Store New Category
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $validated['created_by'] = auth()->id() ?? 1; // Default to authenticated user

        $category = ProjectCategory::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully!',
            'data' => $category
        ], 201);
    }

    // Update Category
    public function update(Request $request, $id)
    {
        $category = ProjectCategory::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $category->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully!',
            'data' => $category
        ]);
    }

    // Delete Category
    public function destroy($id)
    {
        $category = ProjectCategory::findOrFail($id);
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully!'
        ]);
    }
}
