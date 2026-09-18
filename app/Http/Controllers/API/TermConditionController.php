<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\TermCondition;
use Illuminate\Http\Request;

class TermConditionController extends Controller
{
    //

    public function index()
    {
        return response()->json(
            TermCondition::latest()->get()
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'=>'required',
            'content'=>'required'
        ]);

        TermCondition::query()->update([
            'is_active'=>false
        ]);

        $term = TermCondition::create([
            'title'=>$request->title,
            'content'=>$request->content,
            'is_active'=>true
        ]);

        return response()->json([
            'message'=>'Created',
            'data'=>$term
        ]);
    }

    public function update(Request $request,$id)
    {
        $request->validate([
            'title'=>'required',
            'content'=>'required'
        ]);

        $term=TermCondition::findOrFail($id);

        $term->update([
            'title'=>$request->title,
            'content'=>$request->content
        ]);

        return response()->json([
            'message'=>'Updated'
        ]);
    }

    public function show()
    {
        return response()->json(
            TermCondition::where('is_active',true)->first()
        );
    }
}
