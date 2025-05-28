<?php

namespace App\Http\Controllers;

use App\Models\RespondentIncentive;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RespondentIncentiveExport;
use App\Models\Country;
use App\Models\DoctorSpeciality;
use Illuminate\Support\Facades\DB;

class RespondentIncentiveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function index(Request $request)
{
    $query = RespondentIncentive::query();

    if ($request->filled('date_range')) {
        [$start, $end] = explode(' to ', $request->date_range);
        $query->whereBetween('date', [$start, $end]);
    }

    if ($request->filled('country_id')) {
        $query->where('country_id', $request->country_id);
    }

    if ($request->filled('speciality')) {
        $query->where('speciality', $request->speciality);
    }

    $records = $query->latest()->get();

    // Load countries for filter
    $countries = Country::orderBy('name')->get();

    // Load unique specialities from existing data
    // $specialities = RespondentIncentive::select('speciality')
    //     ->distinct()
    //     ->orderBy('speciality')
    //     ->pluck('speciality');
    $specialities = DoctorSpeciality::pluck('speciality'); 


    return view('respondent_incentives.index', compact('records', 'countries', 'specialities'));
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
            'speciality' => 'required|string',
            'incentive_amount' => 'required|numeric',
            'incentive_form' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'payment_date' => 'required|date',
            'payment_type' => 'required|string|in:Cash,PayPal,GiftVoucher,BankTransfer,Check,Credit,Wise,Others',
            'country_id' => 'required|exists:country,id', 
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
     return Excel::download(
        new RespondentIncentiveExport($request->only(['date_range', 'country_id', 'speciality'])),
        'respondent_incentives.xlsx'
    );
}
}
