<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PendingProject;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Client;
use App\Exports\PendingProjectsExport;
use App\Exports\ClosedProjectsExport;
use Maatwebsite\Excel\Facades\Excel;

class PendingProjectController extends Controller
{
    //
    public function store(Request $request)
    {
    $data = $request->all();

    $validator = Validator::make($data, [
        'fy' => 'required|string',
        'pn_no' => 'required|string',
        'client_id' => 'required|exists:clients,id',
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
        'invoice_status' => 'required|in:Paid,Pending',
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
        if (!empty($data['pn_no'])) {
            PendingProject::where('pn_no', $data['pn_no'])->update([
                'fy' => $data['fy'],
                'quarter' => $data['quarter'],
                'client_id' => $data['client_id'],
                'company_name' => $data['company_name'],
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

}
