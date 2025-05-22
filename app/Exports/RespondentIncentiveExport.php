<?php

namespace App\Exports;

use App\Models\RespondentIncentive;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class RespondentIncentiveExport implements FromCollection, WithHeadings
{
    protected $range;

    public function __construct($dateRange = null)
    {
        $this->range = $dateRange;
    }

    public function collection()
    {
        $query = RespondentIncentive::query();
    
        if ($this->range) {
            $parts = explode(' to ', $this->range);
            if (count($parts) === 2) {
                $start = $parts[0];
                $end = $parts[1];
                $query->whereBetween('date', [$start, $end]);
            }
        }
    
        return $query->select([
            'date',
            'pn_no',
            'respondent_name',
            'email_id',
            'contact_number',
            'speciality',
            'incentive_amount',
            'incentive_form',
            'start_date',
            'end_date',
            'payment_date', 'payment_type'
        ])->get();
    }
    

    public function headings(): array
    {
        return [
            'Date',
            'PN No',
            'Respondent Name',
            'Email ID',
            'Contact Number',
            'Speciality',
            'Incentive Amount',
            'Incentive Form',
            'Start Date',
            'End Date',
             'Payment Date',
            'Payment Type'
        ];
    }
    
}