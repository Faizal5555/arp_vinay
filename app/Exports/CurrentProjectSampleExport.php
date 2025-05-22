<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use App\Models\Client;

class CurrentProjectSampleExport implements FromArray, WithHeadings, WithEvents
{
    protected $clients;

    public function __construct()
    {
        $this->clients = Client::pluck('client_name')->toArray(); // Fetch client names
    }

    public function array(): array
    {
        return [
            [
                now()->format('Y-m-d'), // entry_date
                '2024-25',
                'Q1',
                $this->clients[0] ?? 'Client A', // Use client name, not ID
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
    
                // Format date columns
                foreach (['A', 'H', 'Q', 'R'] as $col) {
                    for ($i = 2; $i <= 100; $i++) {
                        $sheet->getStyle("$col$i")->getNumberFormat()->setFormatCode('yyyy-mm-dd');
                    }
                }
    
                // Invoice Status dropdown – column S
                $statusValidation = $sheet->getCell('S2')->getDataValidation();
                $statusValidation->setType(DataValidation::TYPE_LIST)
                    ->setErrorStyle(DataValidation::STYLE_STOP)
                    ->setAllowBlank(true)
                    ->setShowInputMessage(true)
                    ->setShowErrorMessage(true)
                    ->setShowDropDown(true)
                    ->setFormula1('"Pending,Paid,Canceled"');
    
                for ($i = 2; $i <= 100; $i++) {
                    $sheet->getCell("S$i")->setDataValidation(clone $statusValidation);
                }
    
                // Client Name dropdown – column D (client_id)
                $clientNames = array_map('trim', $this->clients);
                $clientList = '"' . implode(',', array_slice($clientNames, 0, 255)) . '"';
    
                $clientValidation = new DataValidation();
                $clientValidation->setType(DataValidation::TYPE_LIST)
                    ->setErrorStyle(DataValidation::STYLE_STOP)
                    ->setAllowBlank(false)
                    ->setShowInputMessage(true)
                    ->setShowErrorMessage(true)
                    ->setShowDropDown(true)
                    ->setFormula1($clientList);
    
                for ($i = 2; $i <= 100; $i++) {
                    $sheet->getCell("D$i")->setDataValidation(clone $clientValidation);
                }
    
                // Company Name dropdown – column E
                $companies = ['ARP', 'HPI', 'URP'];
                $companyList = '"' . implode(',', $companies) . '"';
    
                $companyValidation = new DataValidation();
                $companyValidation->setType(DataValidation::TYPE_LIST)
                    ->setErrorStyle(DataValidation::STYLE_STOP)
                    ->setAllowBlank(false)
                    ->setShowInputMessage(true)
                    ->setShowErrorMessage(true)
                    ->setShowDropDown(true)
                    ->setFormula1($companyList);
    
                for ($i = 2; $i <= 100; $i++) {
                    $sheet->getCell("E$i")->setDataValidation(clone $companyValidation);
                }
            }
        ];
    }
}
