<?php

namespace App\Exports;

use App\Models\RespondentIncentive;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class RespondentIncentiveExport implements FromCollection, WithHeadings
{
    protected $range;
    protected $countryId;
    protected $speciality;

    public function __construct($filters = [])
    {
        $this->range = $filters['date_range'] ?? null;
        $this->countryId = $filters['country_id'] ?? null;
        $this->speciality = $filters['speciality'] ?? null;
    }

    public function collection()
    {
        $query = RespondentIncentive::with('country:id,name') // eager load country name
            ->select([
                'id', // required for relation to work
                'date', 'pn_no', 'respondent_name', 'email_id', 'contact_number',
                'country_id', 'speciality', 'incentive_amount', 'incentive_form',
                'start_date', 'end_date', 'payment_date', 'payment_type'
            ]);

        if ($this->range) {
            $parts = explode(' to ', $this->range);
            if (count($parts) === 2) {
                $query->whereBetween('date', [$parts[0], $parts[1]]);
            }
        }

        if ($this->countryId) {
            $query->where('country_id', $this->countryId);
        }

        if ($this->speciality) {
            $query->where('speciality', $this->speciality);
        }

        return $query->get()->map(function ($item) {
            return [
                $item->date,
                $item->pn_no,
                $item->respondent_name,
                $item->email_id,
                $item->contact_number,
                optional($item->country)->name ?? '-', // show country name
                $item->speciality,
                $item->incentive_amount,
                $item->incentive_form,
                $item->start_date,
                $item->end_date,
                $item->payment_date,
                $item->payment_type
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Date',
            'PN No',
            'Respondent Name',
            'Email ID',
            'Contact Number',
            'Country',
            'Speciality',
            'Incentive Amount',
            'Payment Currency',
            'Start Date',
            'End Date',
            'Payment Date',
            'Payment Type'
        ];
    }
}
