<?php

namespace App\Imports;

use App\Models\Client;
use App\Models\CurrentProject;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ClosedProjectsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Skip empty rows
        if (empty(array_filter($row))) {
            return null;
        }

        // Map client name to ID
        $client = Client::where('client_name', $row['client_id'])->first();

        return new CurrentProject([
            'entry_date' => $this->formatDate($row['entry_date'] ?? null),
            'fy' => $row['fy'] ?? null,
            'quarter' => $row['quarter'] ?? null,
            'client_id' => $client ? $client->id : null,
            'company_name' => $row['company_name'] ?? null,
            'pn_no' => $row['pn_no'] ?? null,
            'email_subject' => $row['email_subject'] ?? null,
            'commission_date' => $this->formatDate($row['commission_date'] ?? null),
            'currency_amount' => $row['currency_amount'] ?? null,
            'original_revenue' => $row['original_revenue'] ?? null,
            'margin' => $row['margin'] ?? null,
            'final_invoice_amount' => $row['final_invoice_amount'] ?? null,
            'comments' => $row['comments'] ?? null,
            'supplier_name' => $row['supplier_name'] ?? null,
            'supplier_payment_details' => $row['supplier_payment_details'] ?? null,
            'total_incentives_paid' => $row['total_incentives_paid'] ?? null,
            'incentive_paid_date' => $this->formatDate($row['incentive_paid_date'] ?? null),
            'invoice_number' => $row['invoice_number'] ?? null,
            'invoice_status' => $row['invoice_status'] ?? 'Paid', // Default to Paid if not given
            'user_id' => auth()->id(),
        ]);
    }

    protected function formatDate($value)
    {
        if (!$value) return null;

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}

