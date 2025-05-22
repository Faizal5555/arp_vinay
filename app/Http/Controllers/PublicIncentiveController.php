<?php

namespace App\Http\Controllers;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\IncentiveImport;
use App\Exports\IncentiveSampleExport;
use App\Models\RespondentIncentive;

use Illuminate\Http\Request;

class PublicIncentiveController extends Controller
{
    //
    public function showForm()
    {
        return view('employee.incentive_form');
    }

public function uploadXlsx(Request $request)
{
    $request->validate([
        'xlsx_file' => 'required|file|mimes:xlsx,xls',
    ]);

    Excel::import(new IncentiveImport, $request->file('xlsx_file'));

    return back()->with('success', 'Bulk data uploaded successfully.');
}

public function submitSingle(Request $request)
{
    $request->validate([
        'date'=>'required|date',
        'pn_no' => 'required',
        'respondent_name' => 'required',
        'email_id' => 'required|email',
        'contact_number' => 'required',
        'speciality' => 'required',
        'incentive_amount' => 'required|numeric',
        'incentive_form' => 'required',
        'start_date' => 'required|date',
        'end_date' => 'required|date',
        'payment_date' => 'required|date',
        'payment_type' => 'required|string|in:Cash,PayPal,GiftVoucher,BankTransfer,Check,Credit,Wise,Others',
    ]);

    RespondentIncentive::create($request->all());

    return back()->with('success', 'Incentive record submitted.');
}

    public function downloadSample()
    {
        return Excel::download(new IncentiveSampleExport, 'sample_incentive_upload.xlsx');
    }
}
