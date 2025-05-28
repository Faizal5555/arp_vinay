<?php

namespace App\Exports;

use App\Models\Country;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class IncentiveSampleExport implements FromArray, WithHeadings, WithEvents
{
    public function array(): array
    {
        $excelDate = ExcelDate::PHPToExcel(now()->startOfDay());

        return [
            [
                $excelDate, 'PN1234', 'John Doe', 'john@example.com', '9876543210',
                'India', 'Cardiology', 1500, 'NEFT',
                $excelDate, $excelDate, $excelDate, 'Cash'
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'date', 'pn_no', 'respondent_name', 'email_id', 'contact_number',
            'country', 'speciality', 'incentive_amount', 'payment_currency',
            'start_date', 'end_date', 'payment_date', 'payment_type'
        ];
    }

    public function registerEvents(): array
{
    return [
        AfterSheet::class => function (AfterSheet $event) {
            $spreadsheet = $event->sheet->getDelegate()->getParent();
            $mainSheet = $spreadsheet->getSheet(0); // Main data sheet

            // STEP 1: Create hidden 'Lists' sheet
            $listSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Lists');
            $spreadsheet->addSheet($listSheet);
            $spreadsheet->setActiveSheetIndex(0); // Keep main sheet active

            // Populate country list in hidden sheet
            $countries = Country::pluck('name')->toArray();
            foreach ($countries as $index => $country) {
                $listSheet->setCellValue('A' . ($index + 1), $country);
            }

            // Hide the list sheet
            $listSheet->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN);

            // Define named range 'CountryList'
            $countryRange = 'Lists!$A$1:$A$' . count($countries);
            $spreadsheet->addNamedRange(
                new \PhpOffice\PhpSpreadsheet\NamedRange('CountryList', $listSheet, '$A$1:$A$' . count($countries))
            );

            // STEP 2: Add Data Validation to main sheet (Column F)
            $countryValidation = new DataValidation();
            $countryValidation->setType(DataValidation::TYPE_LIST);
            $countryValidation->setErrorStyle(DataValidation::STYLE_STOP);
            $countryValidation->setAllowBlank(true);
            $countryValidation->setShowDropDown(true);
            $countryValidation->setFormula1('=CountryList');

            for ($row = 2; $row <= 100; $row++) {
                $mainSheet->getCell("F{$row}")->setDataValidation(clone $countryValidation);
            }

            // STEP 3: Date formatting
            foreach (['A', 'J', 'K', 'L'] as $col) {
                for ($row = 2; $row <= 100; $row++) {
                    $mainSheet->getStyle("{$col}{$row}")
                              ->getNumberFormat()
                              ->setFormatCode('yyyy-mm-dd');
                }
            }

            // STEP 4: Payment Type dropdown (Column M)
            $paymentValidation = new DataValidation();
            $paymentValidation->setType(DataValidation::TYPE_LIST);
            $paymentValidation->setErrorStyle(DataValidation::STYLE_STOP);
            $paymentValidation->setAllowBlank(true);
            $paymentValidation->setShowDropDown(true);
            $paymentValidation->setFormula1('"Cash,PayPal,GiftVoucher,BankTransfer,Check,Wise,CreditCard,Others"');

            for ($row = 2; $row <= 100; $row++) {
                $mainSheet->getCell("M{$row}")->setDataValidation(clone $paymentValidation);
            }
        },
    ];
}

}
