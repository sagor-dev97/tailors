<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\ReedmeCode;
use Illuminate\Http\Request;

class RedeemCodeController extends Controller
{
    // ============================
    // INDEX PAGE LOAD
    // ============================
    public function index()
    {
        $codes = ReedmeCode::orderBy('id','desc')->paginate(100);
        return view('backend.layouts.redeemcode.index', compact('codes'));
    }

    // ============================
    // STORE (AJAX)
    // ============================
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:reedme_codes,code|max:255',
        ]);

        $code = ReedmeCode::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'Code created successfully!',
            'data' => $code
        ]);
    }

    // ============================
    // UPDATE (AJAX)
    // ============================
    public function update(Request $request, $id)
    {
        $code = ReedmeCode::findOrFail($id);

        $request->validate([
            'code' => 'required|string|max:255|unique:reedme_codes,code,' . $id,
        ]);

        $code->update(['code' => $request->code]);

        return response()->json([
            'status' => true,
            'message' => 'Code updated successfully!'
        ]);
    }

    // ============================
    // DELETE (AJAX)
    // ============================
    public function destroy($id)
    {
        $code = ReedmeCode::findOrFail($id);
        $code->delete();

        return response()->json([
            'status' => true,
            'message' => 'Code deleted successfully!'
        ]);
    }
}
