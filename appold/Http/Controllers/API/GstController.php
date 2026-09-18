<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Gst;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Safe\json;

class GstController extends Controller
{
    public function index()
    {
        return response()->json(Gst::latest()->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'gst_percentage' => 'required|numeric'
        ]);

        $gst = Gst::create([
            'name' => $request->name,
            'gst_percentage' => $request->gst_percentage,
            'slug' => Str::slug($request->name),
            'is_active' => true
        ]);

        return response()->json($gst, 201);
    }

    public function show($id)
    {
        return response()->json(Gst::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $gst = Gst::findOrFail($id);

        $gst->update([
            'name' => $request->name,
            'gst_percentage' => $request->gst_percentage,
            'slug' => Str::slug($request->name),
            'is_active' => true,
        ]);

        return response()->json($gst);
    }

    public function destroy($id)
    {
        Gst::destroy($id);

        return response()->json([
            "message" => "GST deleted successfully"
        ]);
    }
}
