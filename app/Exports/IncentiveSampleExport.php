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
                $sheet = $event->sheet->getDelegate();

                // Date formatting
                foreach (['A', 'J', 'K', 'L'] as $col) {
                    for ($row = 2; $row <= 100; $row++) {
                        $sheet->getStyle("{$col}{$row}")
                              ->getNumberFormat()
                              ->setFormatCode('yyyy-mm-dd');
                    }
                }

                // Payment Type dropdown (Column M)
                $paymentValidation = new DataValidation();
                $paymentValidation->setType(DataValidation::TYPE_LIST);
                $paymentValidation->setErrorStyle(DataValidation::STYLE_STOP);
                $paymentValidation->setAllowBlank(true);
                $paymentValidation->setShowDropDown(true);
                $paymentValidation->setFormula1('"Cash,PayPal,GiftVoucher,BankTransfer,Check,Wise,CreditCard,Others"');
                for ($row = 2; $row <= 100; $row++) {
                    $sheet->getCell("M{$row}")->setDataValidation(clone $paymentValidation);
                }

                // Country Dropdown (Column F) from DB
                $countries = Country::pluck('name')->toArray();
                $countryList = implode(',', array_map('trim', $countries));

                // Excel DataValidation list string must not exceed 255 characters
                if (strlen($countryList) > 255) {
                    $countryList = implode(',', array_slice($countries, 0, 20)); // Limit to 20 countries as fallback
                }

                $countryValidation = new DataValidation();
                $countryValidation->setType(DataValidation::TYPE_LIST);
                $countryValidation->setErrorStyle(DataValidation::STYLE_STOP);
                $countryValidation->setAllowBlank(true);
                $countryValidation->setShowDropDown(true);
                $countryValidation->setFormula1('"' . $countryList . '"');

                for ($row = 2; $row <= 100; $row++) {
                    $sheet->getCell("F{$row}")->setDataValidation(clone $countryValidation);
                }
            },
        ];
    }
}
