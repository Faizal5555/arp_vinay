<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class IncentiveSampleExport implements FromArray, WithHeadings, WithEvents
{
    public function array(): array
    {
        return [
            [
                now()->format('Y-m-d'), // Sample date
                'PN1234',
                'John Doe',
                'john@example.com',
                '9876543210',
                'Cardiology',
                '1500',
                'NEFT',
                now()->format('Y-m-d'),
                now()->format('Y-m-d'),
                now()->format('Y-m-d'),
                'Cash'
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'date', 'pn_no', 'respondent_name', 'email_id', 'contact_number',
            'speciality', 'incentive_amount', 'incentive_form',
            'start_date', 'end_date', 'payment_date', 'payment_type'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Columns for dates: A, I, J, K => Excel Index 1, 9, 10, 11
                $dateColumns = ['A', 'I', 'J', 'K'];
                foreach ($dateColumns as $col) {
                    for ($row = 2; $row <= 100; $row++) {
                        $sheet->getStyle($col . $row)
                            ->getNumberFormat()
                            ->setFormatCode('yyyy-mm-dd');
                    }
                }

                // Dropdown for column 'L' (payment_type)
                $validation = $sheet->getCell('L2')->getDataValidation();
                $validation->setType(DataValidation::TYPE_LIST);
                $validation->setErrorStyle(DataValidation::STYLE_STOP);
                $validation->setAllowBlank(true);
                $validation->setShowInputMessage(true);
                $validation->setShowErrorMessage(true);
                $validation->setShowDropDown(true);
                $validation->setFormula1('"Cash,PayPal,GiftVoucher,BankTransfer,Check,Wise,CreditCard,Others"');

                // Apply dropdown to more rows if needed
                for ($i = 3; $i <= 100; $i++) {
                    $sheet->getCell("L$i")->setDataValidation(clone $validation);
                }
            }
        ];
    }
}
