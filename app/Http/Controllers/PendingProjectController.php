<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PendingProject;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Client;
use App\Exports\PendingProjectsExport;
use App\Exports\ClosedProjectsExport;
use App\Exports\OpenLastQuarterExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OpenLastQuarterSampleExport;
use App\Imports\OpenLastQuarterImport;
use App\Exports\PendingProjectsSampleExport;
use App\Imports\PendingProjectsImport;
use App\Exports\ClosedProjectsSampleExport;
use App\Imports\ClosedProjectsImport;

class PendingProjectController extends Controller
{
    //
    public function store(Request $request)
    {
    $data = $request->all();

    $validator = Validator::make($data, [
        'fy' => 'nullable|string',
        'pn_no' => 'nullable|string',
        'client_id' => 'nullable|exists:clients,id',
        'company_name' => 'nullable|string',
        'pn_no' => 'nullable|string',
        'email_subject' => 'nullable|string',
        'commission_date' => 'nullable|date',
        'currency_amount' => 'nullable|string',
        'original_revenue' => 'nullable|string',
        'margin' => 'nullable|string',
        'final_invoice_amount' => 'nullable|string',
        'comments' => 'nullable|string',
        'supplier_name' => 'nullable|string',
        'supplier_payment_details' => 'nullable|string',
        'total_incentives_paid' => 'nullable|string',
        'incentive_paid_date' => 'nullable|date',
        'invoice_number' => 'nullable|string',
        'invoice_status' => 'nullable|string',
        'original_revenue_total' => 'nullable|numeric',
        'invoice_amount_total' => 'nullable|numeric',
        'incentives_paid_total' => 'nullable|numeric',
        'invoice_status' => 'required|in:Paid,Pending,Open_Last_Quarter',
        // validate rest fields if needed
    ]);

    if ($validator->fails()) {
        return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
    }

    $data['user_id'] = auth()->id();
    $data['entry_date'] = $data['entry_date'] ?? now()->toDateString();

    PendingProject::create($data);

    return response()->json([
        'success' => true,
        'message' => "Project moved to {$data['invoice_status']} list successfully."
    ]);

    }


    public function index()
    {
        $pendingProjects = PendingProject::whereIn('invoice_status', ['Pending', 'partial'])->with('client')->get();
        $clients = Client::all();
        return view('pending_projects.index', compact('pendingProjects','clients'));
    }
    public function updateStatus(Request $request, $id)
    {
        $status = $request->input('status');

        $project = PendingProject::findOrFail($id);
        $project->invoice_status = $request->status;

        if ($request->status === 'partial') {
            $project->partial_comment = $request->input('partial_comment');
        } else {
            $project->partial_comment = null; // Clear if not partial
        }
    
        $project->save();

        return response()->json([
            'success' => true,
            'message' => 'Status updated to ' . $status
        ]);
    }

public function bulkUpdate(Request $request)
{
    $projects = $request->projects;

    foreach ($projects as $data) {
        if (!empty($data['id'])) {
            $project = PendingProject::find($data['id']);
            if ($project) {
                $project->update([
                    'fy' => $data['fy'],
                    'quarter' => $data['quarter'],
                    'client_id' => $data['client_id'],
                    'company_name' => $data['company_name'],
                    'pn_no' => $data['pn_no'],
                    'email_subject' => $data['email_subject'],
                    'commission_date' => $data['commission_date'],
                    'currency_amount' => $data['currency_amount'],
                    'original_revenue' => $data['original_revenue'],
                    'margin' => $data['margin'],
                    'final_invoice_amount' => $data['final_invoice_amount'],
                    'comments' => $data['comments'],
                    'supplier_name' => $data['supplier_name'],
                    'supplier_payment_details' => $data['supplier_payment_details'],
                    'total_incentives_paid' => $data['total_incentives_paid'],
                    'incentive_paid_date' => $data['incentive_paid_date'],
                    'invoice_number' => $data['invoice_number'],
                    'invoice_status' => $data['invoice_status'],
                    'partial_comment' => $data['partial_comment'],
                ]);
            }
        }
    }

    return response()->json(['success' => true, 'message' => 'All changes updated successfully.']);
}


    public function download()
    {
        return Excel::download(new PendingProjectsExport, 'pending-projects.xlsx');
    }

    public function closedProjects()
    {
        $closedProjects = PendingProject::whereIn('invoice_status', ['waveoff', 'paid'])->with('client')->get();
        $clients = Client::all();
        return view('closed_projects.index', compact('closedProjects','clients'));
    }

    public function closedDownload()

    {
        return Excel::download(new ClosedProjectsExport, 'closed-projects.xlsx');
    }


    public function openLastQuarter()
    {
        $pendingProjects = PendingProject::with('client')
            ->where('invoice_status', 'Open_Last_Quarter')
            ->orderByDesc('entry_date')
            ->get();

        $clients = Client::select('id', 'client_name')->get();

        return view('pending_projects.open_last_quarter', compact('pendingProjects', 'clients'));
    }


    public function moveOpenQuarterProject(Request $request)
{
    $validated = $request->validate([
        'id' => 'required|exists:pending_projects,id',
        'status' => 'required|in:Pending,Paid'
    ]);

    $project = PendingProject::find($validated['id']);
    $project->invoice_status = $validated['status'];
    $project->save();

    return response()->json([
        'success' => true,
        'message' => "Project moved to {$validated['status']} successfully."
    ]);
}



public function pendingajaxSearch(Request $request)
{
    $query = PendingProject::query()
        ->whereIn('invoice_status', ['Pending', 'partial']); // Only Pending/Partial projects

    // Apply Filters
    if ($request->filled('fy')) {
        $query->where('fy', $request->fy);
    }
    if ($request->filled('quarter')) {
        $query->where('quarter', $request->quarter);
    }
    if ($request->filled('client_id')) {
        $query->where('client_id', $request->client_id);
    }
    if ($request->filled('company_name')) {
        $query->where('company_name', $request->company_name);
    }
    if ($request->filled('pn_no')) {
        $query->where('pn_no', 'like', '%' . $request->pn_no . '%');
    }

    // General Keyword Search (across all columns)
    if ($request->filled('keyword')) {
        $keyword = $request->keyword;
        $query->where(function ($q) use ($keyword) {
            $q->where('pn_no', 'like', "%{$keyword}%")
                ->orWhere('email_subject', 'like', "%{$keyword}%")
                ->orWhere('company_name', 'like', "%{$keyword}%")
                ->orWhere('fy', 'like', "%{$keyword}%")
                ->orWhere('quarter', 'like', "%{$keyword}%")
                ->orWhere('commission_date', 'like', "%{$keyword}%")
                ->orWhere('currency_amount', 'like', "%{$keyword}%")
                ->orWhere('original_revenue', 'like', "%{$keyword}%")
                ->orWhere('margin', 'like', "%{$keyword}%")
                ->orWhere('final_invoice_amount', 'like', "%{$keyword}%")
                ->orWhere('comments', 'like', "%{$keyword}%")
                ->orWhere('supplier_name', 'like', "%{$keyword}%")
                ->orWhere('supplier_payment_details', 'like', "%{$keyword}%")
                ->orWhere('total_incentives_paid', 'like', "%{$keyword}%")
                ->orWhere('incentive_paid_date', 'like', "%{$keyword}%")
                ->orWhere('invoice_number', 'like', "%{$keyword}%")
                ->orWhere('invoice_status', 'like', "%{$keyword}%")
                ->orWhere('partial_comment', 'like', "%{$keyword}%")
                ->orWhere('entry_date', 'like', "%{$keyword}%")
                ->orWhereHas('client', function ($q) use ($keyword) {
                    $q->whereRaw('TRIM(client_name) LIKE ?', ["%{$keyword}%"]);
                });
        });
    }

    $results = $query->with('client')->get();
    return view('pending_projects.pending_search', compact('results'))->render();
}


public function closedajaxSearch(Request $request)
{
    $query = PendingProject::query()
        ->whereIn('invoice_status', ['Paid', 'waveoff']); // Only Closed Projects

    // Apply Filters
    if ($request->filled('fy')) {
        $query->where('fy', $request->fy);
    }
    if ($request->filled('quarter')) {
        $query->where('quarter', $request->quarter);
    }
    if ($request->filled('client_id')) {
        $query->where('client_id', $request->client_id);
    }
    if ($request->filled('company_name')) {
        $query->where('company_name', $request->company_name);
    }
    if ($request->filled('pn_no')) {
        $query->where('pn_no', 'like', '%' . $request->pn_no . '%');
    }

    // General Keyword Search (across all columns)
    if ($request->filled('keyword')) {
        $keyword = $request->keyword;
        $query->where(function ($q) use ($keyword) {
            $q->where('pn_no', 'like', "%{$keyword}%")
                ->orWhere('email_subject', 'like', "%{$keyword}%")
                ->orWhere('company_name', 'like', "%{$keyword}%")
                ->orWhere('fy', 'like', "%{$keyword}%")
                ->orWhere('quarter', 'like', "%{$keyword}%")
                ->orWhere('commission_date', 'like', "%{$keyword}%")
                ->orWhere('currency_amount', 'like', "%{$keyword}%")
                ->orWhere('original_revenue', 'like', "%{$keyword}%")
                ->orWhere('margin', 'like', "%{$keyword}%")
                ->orWhere('final_invoice_amount', 'like', "%{$keyword}%")
                ->orWhere('comments', 'like', "%{$keyword}%")
                ->orWhere('supplier_name', 'like', "%{$keyword}%")
                ->orWhere('supplier_payment_details', 'like', "%{$keyword}%")
                ->orWhere('total_incentives_paid', 'like', "%{$keyword}%")
                ->orWhere('incentive_paid_date', 'like', "%{$keyword}%")
                ->orWhere('invoice_number', 'like', "%{$keyword}%")
                ->orWhere('invoice_status', 'like', "%{$keyword}%")
                ->orWhere('partial_comment', 'like', "%{$keyword}%")
                ->orWhere('entry_date', 'like', "%{$keyword}%")
                ->orWhereHas('client', function ($q) use ($keyword) {
                    $q->whereRaw('TRIM(client_name) LIKE ?', ["%{$keyword}%"]);
                });
        });
    }

    $results = $query->with('client')->get();
    $clients = Client::all();

    return view('closed_projects.closed_search', compact('results', 'clients'))->render();
}

    public function open_quarter_download()
    {
        return Excel::download(new OpenLastQuarterExport, 'open-quarter-projects.xlsx');
    }
   

 public function openLastQuarterAjaxSearch(Request $request)
{
    $query = PendingProject::query()
        ->where('invoice_status', 'Open_Last_Quarter'); // ✅ Only Open_Last_Quarter projects

    // Filters
    if ($request->filled('fy')) {
        $query->where('fy', $request->fy);
    }
    if ($request->filled('quarter')) {
        $query->where('quarter', $request->quarter);
    }
    if ($request->filled('client_id')) {
        $query->where('client_id', $request->client_id);
    }
    if ($request->filled('company_name')) {
        $query->where('company_name', $request->company_name);
    }
    if ($request->filled('pn_no')) {
        $query->where('pn_no', 'like', '%' . $request->pn_no . '%');
    }

    // Keyword Search
    if ($request->filled('keyword')) {
        $keyword = $request->keyword;
        $query->where(function ($q) use ($keyword) {
            $q->where('pn_no', 'like', "%{$keyword}%")
                ->orWhere('email_subject', 'like', "%{$keyword}%")
                ->orWhere('company_name', 'like', "%{$keyword}%")
                ->orWhere('fy', 'like', "%{$keyword}%")
                ->orWhere('quarter', 'like', "%{$keyword}%")
                ->orWhere('commission_date', 'like', "%{$keyword}%")
                ->orWhere('currency_amount', 'like', "%{$keyword}%")
                ->orWhere('original_revenue', 'like', "%{$keyword}%")
                ->orWhere('margin', 'like', "%{$keyword}%")
                ->orWhere('final_invoice_amount', 'like', "%{$keyword}%")
                ->orWhere('comments', 'like', "%{$keyword}%")
                ->orWhere('supplier_name', 'like', "%{$keyword}%")
                ->orWhere('supplier_payment_details', 'like', "%{$keyword}%")
                ->orWhere('total_incentives_paid', 'like', "%{$keyword}%")
                ->orWhere('incentive_paid_date', 'like', "%{$keyword}%")
                ->orWhere('invoice_number', 'like', "%{$keyword}%")
                ->orWhere('partial_comment', 'like', "%{$keyword}%")
                ->orWhere('entry_date', 'like', "%{$keyword}%")
                ->orWhereHas('client', function ($q) use ($keyword) {
                    $q->whereRaw('TRIM(client_name) LIKE ?', ["%{$keyword}%"]);
                });
        });
    }

    $results = $query->with('client')->get();
    $clients = Client::all();

    return view('pending_projects.pending_search', compact('results', 'clients'))->render();
}

public function downloadOpenLastQuarterSample()
{
    return Excel::download(new OpenLastQuarterSampleExport, 'sample_open_last_quarter.xlsx');
}



public function openLastQuarterBulkUpload(Request $request)
{
    $request->validate([
        'file' => 'required|file|mimes:xlsx'
    ]);

    try {
        Excel::import(new OpenLastQuarterImport, $request->file('file'));
        return response()->json(['success' => true, 'message' => 'Projects uploaded successfully!']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
}


public function pendingupload(Request $request)
{
    $request->validate([
        'file' => 'required|file|mimes:xlsx'
    ]);

    try {
        Excel::import(new PendingProjectsImport, $request->file('file'));
        return back()->with('success', 'Projects imported successfully!');
    } catch (\Exception $e) {
        return back()->with('error', $e->getMessage());
    }
}

public function downloadSample()
{
    return Excel::download(new PendingProjectsSampleExport, 'sample_pending_projects.xlsx');
}


public function downloadClosedSample()
{
    return Excel::download(new ClosedProjectsSampleExport, 'sample_closed_projects.xlsx');
}

// Upload Closed Projects
public function uploadClosed(Request $request)
{
    $request->validate([
        'file' => 'required|file|mimes:xlsx'
    ]);
    Excel::import(new ClosedProjectsImport, $request->file('file'));

    return response()->json(['success' => true, 'message' => 'Closed Projects Imported Successfully!']);
}


public function delete(Request $request)
{
    $project = PendingProject::findOrFail($request->id);
    $project->delete();

    return response()->json(['success' => 1, 'message' => 'Project deleted successfully.']);
}


}
