<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Note;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    //
    public function index()
    {
        return response()->json(Note::latest()->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable'
        ]);

        $note = Note::create($request->all());

        return response()->json([
            'message' => 'Created Successfully',
            'data' => $note
        ],201);
    }

    public function show($id)
    {
        return response()->json(Note::findOrFail($id));
    }

    public function update(Request $request,$id)
    {
        $request->validate([
            'title'=>'required|max:255',
            'description'=>'nullable'
        ]);

        $note=Note::findOrFail($id);

        $note->update($request->all());

        return response()->json([
            'message'=>'Updated Successfully',
            'data'=>$note
        ]);
    }

    public function destroy($id)
    {
        Note::findOrFail($id)->delete();

        return response()->json([
            'message'=>'Deleted Successfully'
        ]);
    }
}
