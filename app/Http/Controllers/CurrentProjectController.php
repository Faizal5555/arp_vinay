<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\CurrentProject;
use App\Exports\CurrentProjectsExport;
use App\Exports\CurrentProjectSampleExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CurrentProjectImport;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CurrentProjectController extends Controller
{
    //
    public function index()
    {
        $clients = Client::all();
        $projects = CurrentProject::where('user_id', auth()->id())->get();
        return view('current_projects.index', compact('clients', 'projects'));
    }

    public function store(Request $request)
{
    $projects = $request->input('projects');

    $originalTotal = 0;
    $invoiceTotal = 0;
    $incentiveTotal = 0;

    foreach ($projects as $project) {
        // Skip empty rows
        $filtered = collect($project)->except(['entry_date'])->filter();
        if ($filtered->isEmpty()) continue;

        // Validate inputs
        $validator = Validator::make($project, [
            'id' => 'nullable|exists:current_projects,id',
            'entry_date' => 'nullable|string',
            'fy' => 'nullable|string',
            'quarter' => 'nullable|string',
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
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Normalize matching fields
        $fy = strtoupper(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', trim($project['fy'] ?? '')));
        $pnNo = strtoupper(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', trim((string) $project['pn_no'] ?? '')));
        $project['fy'] = $fy;
        $project['pn_no'] = $pnNo;

        // Defaults
        $project['user_id'] = Auth::id();
        $project['entry_date'] = $project['entry_date'] ?? now()->format('Y-m-d');
        $project['original_revenue_total'] = $originalTotal;
        $project['invoice_amount_total'] = $invoiceTotal;
        $project['incentives_paid_total'] = $incentiveTotal;

        // Totals calculation
        $originalTotal += floatval($project['original_revenue'] ?? 0);
        $invoiceTotal += floatval($project['final_invoice_amount'] ?? 0);
        $incentiveTotal += floatval($project['total_incentives_paid'] ?? 0);

        // Update if ID exists
        if (!empty($project['id'])) {
            $existing = CurrentProject::where('user_id', Auth::id())
                ->where('id', $project['id'])
                ->first();

            if ($existing) {
                $existing->fill($project)->save();
                continue;
            }
        }

        // Fallback to legacy match if no ID
        $existing = CurrentProject::where('user_id', Auth::id())
            ->whereRaw('BINARY fy = ?', [$fy])
            ->whereRaw('BINARY pn_no = ?', [$pnNo])
            ->first();

        if ($existing) {
            $existing->fill($project)->save();
        } else {
            CurrentProject::create($project);
        }
    }

    return response()->json([
        'success' => true,
        'message' => 'Projects stored/updated successfully.',
        'totals' => [
            'original_revenue_total' => $originalTotal,
            'invoice_amount_total' => $invoiceTotal,
            'incentives_paid_total' => $incentiveTotal,
        ]
    ]);
}


    public function deleteByPn(Request $request)
{
    $id = $request->id;

    $deleted = CurrentProject::where('user_id', auth()->id())
        ->where('id', $id)
        ->delete();

    return response()->json(['success' => true, 'deleted' => $deleted]);
}


    public function download()
    {
        return Excel::download(new CurrentProjectsExport, 'current-projects.xlsx');
    }


    public function downloadSample()
    {
        return Excel::download(new CurrentProjectSampleExport, 'sample_current_projects.xlsx');
    }

//     public function bulkUpload(Request $request)
// {
//     $request->validate([
//         'bulk_file' => 'required|file|mimes:xlsx'
//     ]);

//     Excel::import(new CurrentProjectImport, $request->file('bulk_file'));

//     return response()->json(['success' => true, 'message' => 'Projects imported successfully.']);
// }


public function bulkUpload(Request $request)
{
    $request->validate([
        'bulk_file' => 'required|file|mimes:xlsx'
    ]);

    try {
        $file = $request->file('bulk_file');
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();

        // Get the first row as headers
        $headers = $sheet->rangeToArray('A1:' . $sheet->getHighestColumn() . '1')[0];

        // Define the expected column headers (customize this based on your actual format)
        $expectedHeaders = ['pn_no', 'client_id', 'company_name', 'fy','quarter','currency_amount'];

        foreach ($expectedHeaders as $header) {
            if (!in_array($header, $headers)) {
                return response()->json([
                    'success' => false,
                    'message' => "Invalid file format. Missing column: {$header}"
                ]);
            }
        }

        // If headers match, proceed with the import
        Excel::import(new CurrentProjectImport, $file);

        return response()->json([
            'success' => true,
            'message' => 'Projects imported successfully.'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'File processing error: ' . $e->getMessage()
        ]);
    }
}

public function deleteById(Request $request)
{
    $project = CurrentProject::find($request->id);

    if (!$project) {
        return response()->json([
            'success' => 0,
            'message' => 'Project not found.'
        ], 404);
    }

    $project->delete();

    return response()->json([
        'success' => 1,
        'message' => 'Project deleted successfully!'
    ]);
}

}
