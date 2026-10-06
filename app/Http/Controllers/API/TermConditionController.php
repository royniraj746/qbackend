<?php

// namespace App\Http\Controllers\API;

// use App\Http\Controllers\Controller;
// use App\Models\TermCondition;
// use Illuminate\Http\Request;

// class TermConditionController extends Controller
// {
//     //

//     public function index()
//     {
//         return response()->json(
//             TermCondition::latest()->get()
//         );
//     }

//     public function store(Request $request)
//     {
//         $request->validate([
//             'title'=>'required',
//             'content'=>'required'
//         ]);

//         TermCondition::query()->update([
//             'is_active'=>false
//         ]);

//         $term = TermCondition::create([
//             'title'=>$request->title,
//             'content'=>$request->content,
//             'is_active'=>true
//         ]);

//         return response()->json([
//             'message'=>'Created',
//             'data'=>$term
//         ]);
//     }

//     public function update(Request $request,$id)
//     {
//         $request->validate([
//             'title'=>'required',
//             'content'=>'required'
//         ]);

//         $term=TermCondition::findOrFail($id);

//         $term->update([
//             'title'=>$request->title,
//             'content'=>$request->content
//         ]);

//         return response()->json([
//             'message'=>'Updated'
//         ]);
//     }

//     public function show()
//     {
//         return response()->json(
//             TermCondition::where('is_active',true)->first()
//         );
//     }
// }




namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\TermCondition;
use Illuminate\Http\Request;

class TermConditionController extends Controller
{
    /**
     * Get all Terms & Conditions
     *
     * GET /admin/terms
     */
    public function index()
    {
        $terms = TermCondition::latest()->get();

        return response()->json($terms);
    }

    /**
     * Create new Terms & Conditions
     *
     * POST /admin/terms
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'content' => [
                'required',
                'string',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        $term = TermCondition::create([
            'name' => $validated['name'],
            'title' => $validated['title'],
            'content' => $validated['content'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Terms & Conditions created successfully.',
            'data' => $term,
        ], 201);
    }

    /**
     * Get single Terms & Conditions
     *
     * GET /admin/terms/{id}
     */
    public function show($id)
    {
        $term = TermCondition::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $term,
        ]);
    }

    /**
     * Update Terms & Conditions
     *
     * PUT /admin/terms/{id}
     */
    public function update(Request $request, $id)
    {
        $term = TermCondition::findOrFail($id);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'content' => [
                'required',
                'string',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        $term->update([
            'name' => $validated['name'],
            'title' => $validated['title'],
            'content' => $validated['content'],
            'is_active' => $validated['is_active'] ?? $term->is_active,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Terms & Conditions updated successfully.',
            'data' => $term->fresh(),
        ]);
    }

    /**
     * Delete Terms & Conditions
     *
     * DELETE /admin/terms/{id}
     */
    public function destroy($id)
    {
        $term = TermCondition::findOrFail($id);

        $term->delete();

        return response()->json([
            'success' => true,
            'message' => 'Terms & Conditions deleted successfully.',
        ]);
    }

    /**
     * Get active Terms & Conditions
     *
     * GET /admin/terms/active
     *
     * Used for quotation dropdown.
     */
    public function active()
    {
        $terms = TermCondition::where('is_active', true)
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'title',
            ]);

        return response()->json($terms);
    }
}
