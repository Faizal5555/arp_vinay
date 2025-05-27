<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use App\Models\RespondentIncentive;
use App\Models\Country;

class IncentiveImport implements ToCollection
{
   public function collection(Collection $rows)
{
    foreach ($rows->skip(1) as $row) {

        // Skip rows with missing mandatory data
        if (
            empty($row[0]) || empty($row[1]) || empty($row[2]) || empty($row[3]) ||
            empty($row[4]) || empty($row[5]) || empty($row[6]) || empty($row[7]) ||
            empty($row[8]) || empty($row[9]) || empty($row[10]) || empty($row[11]) || empty($row[12])
        ) {
            continue;
        }

        // Lookup country_id from country name (case-insensitive)
        $country = Country::whereRaw('LOWER(name) = ?', [strtolower(trim($row[5]))])->first();
        $countryId = $country ? $country->id : null;

        RespondentIncentive::create([
            'date' => is_numeric($row[0])
                ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[0])->format('Y-m-d')
                : \Carbon\Carbon::parse($row[0])->format('Y-m-d'),

            'pn_no' => $row[1],
            'respondent_name' => $row[2],
            'email_id' => $row[3],
            'contact_number' => $row[4],
            'speciality' => $row[6],
            'incentive_amount' => (float) $row[7],
            'incentive_form' => $row[8],

            'start_date' => is_numeric($row[9])
                ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[9])->format('Y-m-d')
                : \Carbon\Carbon::parse($row[9])->format('Y-m-d'),

            'end_date' => is_numeric($row[10])
                ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[10])->format('Y-m-d')
                : \Carbon\Carbon::parse($row[10])->format('Y-m-d'),

            'payment_date' => is_numeric($row[11])
                ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[11])->format('Y-m-d')
                : \Carbon\Carbon::parse($row[11])->format('Y-m-d'),

            'payment_type' => $row[12],
            'country_id' => $countryId,
        ]);
    }
}
}
