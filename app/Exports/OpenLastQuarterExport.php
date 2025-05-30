<?php

namespace App\Exports;

use App\Models\PendingProject;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OpenLastQuarterExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return PendingProject::with('client')
        ->whereIn('invoice_status', ['Open_Last_Quarter'])
        ->get(); // 
    }

    public function map($project): array
    {   

        $statusLabels = [
        'Paid' => 'Closed',
        'waveoff' => 'Waveoff',
        'Pending' => 'Pending',
        'Partial' => 'Partial Payment',
        'Open_Last_Quarter' => 'Open Project Last Quarter',
    ];
        return [
            $project->entry_date,
            $project->fy,
            $project->quarter,
            $project->client->client_name ?? '', // ✅ use related client name
            $project->company_name,
            $project->pn_no,
            $project->email_subject,
            $project->commission_date,
            $project->currency_amount,
            $project->original_revenue,
            $project->margin,
            $project->final_invoice_amount,
            $project->comments,
            $project->supplier_name,
            $project->supplier_payment_details,
            $project->total_incentives_paid,
            $project->incentive_paid_date,
            $project->invoice_number,
            $statusLabels[$project->invoice_status] ?? $project->invoice_status,
            
        ];
    }

    public function headings(): array
    {
        return [
            'Entry Date',
            'FY',
            'Quarter',
            'Client Name', // ✅ updated heading
            'Company Name',
            'PN No',
            'Email Subject',
            'Commission Date',
            'Currency Amount',
            'Original Revenue',
            'Margin',
            'Final Invoice Amount',
            'Comments',
            'Supplier Name',
            'Supplier Payment Details',
            'Total Incentives Paid',
            'Incentive Paid Date',
            'Invoice Number',
            'Invoice Status',
          
        ];
    }
}

