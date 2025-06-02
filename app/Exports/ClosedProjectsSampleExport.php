<?php

namespace App\Exports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ClosedProjectsSampleExport implements FromArray, WithHeadings
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
                'Project Completed Successfully',
                'XYZ Supplier',
                'Paid via bank',
                '5000',
                now()->format('Y-m-d'),
                'INV-2024-001',
                'Paid'
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
}

