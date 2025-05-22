<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Vendor;
use Illuminate\Support\Facades\Validator;

class VendorController extends Controller
{
    //
    public function index()
{
    return view('vendors.index');
}

public function getVendors()
{
    return DataTables::of(Vendor::with('user')->latest())->make(true);
}


public function store(Request $req)
{
    $validator = Validator::make($req->all(), [
        'vendor_name' => 'required',
        'vendor_country' => 'required',
        'vendor_email' => 'required|email|unique:vendors,vendor_email',
        'vendor_manager' => 'required',
        'vendor_phoneno' => 'required|numeric|digits_between:9,15|unique:vendors,vendor_phoneno',
        'vendor_whatsapp' => 'required|numeric|digits_between:9,15|unique:vendors,vendor_whatsapp',
    ]);

    if ($validator->fails()) {
        return response()->json(['success' => 0, 'error' => $validator->errors()], 422);
    }

    $vendor = new Vendor($req->all());
    $vendor->user_id = auth()->id();
    $vendor->save();

    return response()->json(['success' => 1, 'message' => 'Vendor created successfully']);
}

public function edit($id)
{
    return Vendor::findOrFail($id);
}

public function update(Request $req, $id)
{
    $vendor = Vendor::findOrFail($id);

    $validator = Validator::make($req->all(), [
        'vendor_name' => 'required',
        'vendor_country' => 'required',
        'vendor_email' => 'required|email|unique:vendors,vendor_email,' . $id,
        'vendor_manager' => 'required',
        'vendor_phoneno' => 'required|numeric|digits_between:9,15|unique:vendors,vendor_phoneno,' . $id,
        'vendor_whatsapp' => 'required|numeric|digits_between:9,15|unique:vendors,vendor_whatsapp,' . $id,
    ]);

    if ($validator->fails()) {
        return response()->json(['success' => 0, 'error' => $validator->errors()], 422);
    }

    $vendor->update($req->all());

    return response()->json(['success' => 1, 'message' => 'Vendor updated successfully']);
}

public function destroy($id)
{
    $vendor = Vendor::findOrFail($id);
    $vendor->delete();

    return response()->json(['success' => 1, 'message' => 'Vendor deleted successfully']);
}




}
