<?php

namespace App\Http\Controllers;

use App\Models\RespondentIncentive;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RespondentIncentiveExport;

class RespondentIncentiveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $query = RespondentIncentive::query();

        if ($request->filled('date_range')) {
            [$start, $end] = explode(' to ', $request->date_range);
            $query->whereBetween('date', [$start, $end]);
        }
    
        $records = $query->latest()->get();
        return view('respondent_incentives.index', compact('records'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    { 

        $request->validate([
            'date'=>'nullable|date',
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

        $data = $request->all();

        // Add today's date in Y-m-d format
        $data['date'] = now()->format('Y-m-d');
    
        if ($request->record_id) {
            // Update
            RespondentIncentive::findOrFail($request->record_id)->update($request->except('record_id'));
            $msg = 'Updated successfully.';
        } else {
            // Create
            RespondentIncentive::create($request->all());
            $msg = 'Added successfully.';
        }
    
        return response()->json(['success' => true, 'message' => $msg]);
    }
    


    /**
     * Display the specified resource.
     */
    public function show(RespondentIncentive $respondentIncentive)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RespondentIncentive $respondentIncentive)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RespondentIncentive $respondentIncentive)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
        RespondentIncentive::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Deleted successfully.']);
    }


    public function download(Request $request)
{   
    $dateRange = $request->input('date_range'); 
    return Excel::download(new RespondentIncentiveExport(  $dateRange ), 'respondent_incentives.xlsx');
}
}
