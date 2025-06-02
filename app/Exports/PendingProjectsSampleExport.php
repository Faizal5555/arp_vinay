<?php

namespace App\Exports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class PendingProjectsSampleExport implements FromArray, WithHeadings, WithEvents
{
    protected $clients;

    public function __construct()
    {
        $this->clients = Client::pluck('client_name')->toArray();
    }

    public function array(): array
    {
        return [
            [
                now()->format('Y-m-d'),
                'FY 24-25',
                'Q1',
                $this->clients[0] ?? 'Client A',
                'ARP',
                'PN12345',
                'Research Subject',
                now()->format('Y-m-d'),
                'USD 10000',
                '10000',
                '20%',
                '12000',
                'Initial discussion done',
                'XYZ Supplier',
                'Paid via bank transfer',
                '5000',
                now()->format('Y-m-d'),
                'INV-2024-001',
                'Pending',
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'entry_date',
            'fy',
            'quarter',
            'client_id',
            'company_name',
            'pn_no',
            'email_subject',
            'commission_date',
            'currency_amount',
            'original_revenue',
            'margin',
            'final_invoice_amount',
            'comments',
            'supplier_name',
            'supplier_payment_details',
            'total_incentives_paid',
            'incentive_paid_date',
            'invoice_number',
            'invoice_status',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Date format
                foreach (['A', 'H', 'Q', 'R'] as $col) {
                    for ($i = 2; $i <= 100; $i++) {
                        $sheet->getStyle("$col$i")->getNumberFormat()->setFormatCode('yyyy-mm-dd');
                    }
                }

                // FY Dropdown (B)
                $fyOptions = [];
                for ($i = 10; $i <= 50; $i++) {
                    $fyOptions[] = 'FY ' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-' . str_pad(($i + 1) % 100, 2, '0', STR_PAD_LEFT);
                }
                $fyList = '"' . implode(',', $fyOptions) . '"';

                $fyValidation = new DataValidation();
                $fyValidation->setType(DataValidation::TYPE_LIST)
                    ->setErrorStyle(DataValidation::STYLE_STOP)
                    ->setAllowBlank(true)
                    ->setShowDropDown(true)
                    ->setFormula1($fyList);

                for ($i = 2; $i <= 100; $i++) {
                    $sheet->getCell("B$i")->setDataValidation(clone $fyValidation);
                }

                // Quarter Dropdown (C)
                $quarters = '"Q1,Q2,Q3,Q4"';
                $quarterValidation = new DataValidation();
                $quarterValidation->setType(DataValidation::TYPE_LIST)
                    ->setErrorStyle(DataValidation::STYLE_STOP)
                    ->setAllowBlank(true)
                    ->setShowDropDown(true)
                    ->setFormula1($quarters);

                for ($i = 2; $i <= 100; $i++) {
                    $sheet->getCell("C$i")->setDataValidation(clone $quarterValidation);
                }

                // Client Dropdown (D)
                $clientNames = array_map('trim', $this->clients);
                $clientList = '"' . implode(',', array_slice($clientNames, 0, 255)) . '"';
                $clientValidation = new DataValidation();
                $clientValidation->setType(DataValidation::TYPE_LIST)
                    ->setErrorStyle(DataValidation::STYLE_STOP)
                    ->setAllowBlank(true)
                    ->setShowDropDown(true)
                    ->setFormula1($clientList);

                for ($i = 2; $i <= 100; $i++) {
                    $sheet->getCell("D$i")->setDataValidation(clone $clientValidation);
                }

                // Company Dropdown (E)
                $companies = '"ARP,HPI,URP"';
                $companyValidation = new DataValidation();
                $companyValidation->setType(DataValidation::TYPE_LIST)
                    ->setErrorStyle(DataValidation::STYLE_STOP)
                    ->setAllowBlank(true)
                    ->setShowDropDown(true)
                    ->setFormula1($companies);

                for ($i = 2; $i <= 100; $i++) {
                    $sheet->getCell("E$i")->setDataValidation(clone $companyValidation);
                }

                // Invoice Status Dropdown (S)
                $statusValidation = new DataValidation();
                $statusValidation->setType(DataValidation::TYPE_LIST)
                    ->setErrorStyle(DataValidation::STYLE_STOP)
                    ->setAllowBlank(true)
                    ->setShowDropDown(true)
                    ->setFormula1('"Pending,partial"');

                for ($i = 2; $i <= 100; $i++) {
                    $sheet->getCell("S$i")->setDataValidation(clone $statusValidation);
                }
            }
        ];
    }
}
