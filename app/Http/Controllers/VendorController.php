<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Vendor;

class VendorController extends Controller
{
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
        // 🚫 No validation at all
        $vendor = new Vendor($req->only([
            'vendor_name', 
            'vendor_country', 
            'vendor_email', 
            'vendor_manager', 
            'vendor_phoneno', 
            'vendor_whatsapp'
        ]));

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

        // 🚫 No validation at all
        $vendor->update($req->only([
            'vendor_name', 
            'vendor_country', 
            'vendor_email', 
            'vendor_manager', 
            'vendor_phoneno', 
            'vendor_whatsapp'
        ]));

        return response()->json(['success' => 1, 'message' => 'Vendor updated successfully']);
    }

    public function destroy($id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->delete();

        return response()->json(['success' => 1, 'message' => 'Vendor deleted successfully']);
    }
}
