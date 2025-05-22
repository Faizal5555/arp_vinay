<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CurrentProject;
use App\Models\PendingProject;
use App\Models\Client;
use App\Exports\FilteredProjectsExport;
use Maatwebsite\Excel\Facades\Excel;

class ProjectSearchController extends Controller
{
    //

    public function index()
{
    $clients = Client::orderBy('client_name')->get(); // to populate client dropdown
    $projects = collect(); // default load all

    return view('search_projects.index', compact('clients', 'projects'));
}

public function ajaxSearch(Request $request)
{
    if (
        !$request->filled('fy') &&
        !$request->filled('quarter') &&
        !$request->filled('client_id') &&
        !$request->filled('company_name') &&
        !$request->filled('pn_no') &&
        !$request->filled('supplier_name') &&
        !$request->filled('invoice_status')
    ) {
        return response()->json([
            'html' => view('search_projects.results', ['projects' => collect()])->render()
        ]);
    }

    $current = CurrentProject::with('client');
    $pending = PendingProject::with('client');

    // ✅ Filter only by FY if FY input is filled
    if ($request->fy) {
        $current->where('fy', 'like', "%{$request->fy}%");
        $pending->where('fy', 'like', "%{$request->fy}%");
    }

    // ✅ Quarter input filters both 'quarter' and 'fy'
    if ($request->quarter) {
        $search = $request->quarter;
        $current->where(function ($q) use ($search) {
            $q->where('quarter', 'like', "%{$search}%")
              ->orWhere('fy', 'like', "%{$search}%");
        });
        $pending->where(function ($q) use ($search) {
            $q->where('quarter', 'like', "%{$search}%")
              ->orWhere('fy', 'like', "%{$search}%");
        });
    }

    // Other exact match fields
    foreach (['client_id', 'company_name', 'invoice_status'] as $field) {
        if ($request->$field) {
            $current->where($field, $request->$field);
            $pending->where($field, $request->$field);
        }
    }

    // Partial match fields
    if ($request->pn_no) {
        $current->where('pn_no', 'like', "%{$request->pn_no}%");
        $pending->where('pn_no', 'like', "%{$request->pn_no}%");
    }

    if ($request->supplier_name) {
        $current->where('supplier_name', 'like', "%{$request->supplier_name}%");
        $pending->where('supplier_name', 'like', "%{$request->supplier_name}%");
    }

    $projects = $current->get()->merge($pending->get());

    return response()->json([
        'html' => view('search_projects.results', compact('projects'))->render()
    ]);
}

public function download(Request $request)
{
    $filters = $request->only([
        'fy', 'quarter', 'client_id', 'company_name', 'pn_no', 'supplier_name', 'invoice_status'
    ]);
    
    if (collect($filters)->filter()->isEmpty()) {
        return redirect()->back()->with('error', 'Please apply at least one filter before downloading.');
    }

    return Excel::download(new FilteredProjectsExport($filters), 'filtered-projects.xlsx');
}




}
