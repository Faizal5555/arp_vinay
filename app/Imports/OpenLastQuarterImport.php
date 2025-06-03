<?php

namespace App\Imports;

use App\Models\PendingProject;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class OpenLastQuarterImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (empty(array_filter($row))) {
            return null; // Skip empty rows
        }

        return new PendingProject([
            'entry_date' => $this->nullableDate($row['entry_date'] ?? null),
            'fy' => $row['fy'] ?? null,
            'quarter' => $row['quarter'] ?? null,
            'client_id' => $this->mapClient($row['client_id'] ?? null), // If client mapping is needed
            'company_name' => $row['company_name'] ?? null,
            'pn_no' => $row['pn_no'] ?? null,
            'email_subject' => $row['email_subject'] ?? null,
            'commission_date' => $this->nullableDate($row['commission_date'] ?? null),
            'currency_amount' => $row['currency_amount'] ?? null,
            'original_revenue' => $row['original_revenue'] ?? null,
            'margin' => $row['margin'] ?? null,
            'final_invoice_amount' => $row['final_invoice_amount'] ?? null,
            'comments' => $row['comments'] ?? null,
            'supplier_name' => $row['supplier_name'] ?? null,
            'supplier_payment_details' => $row['supplier_payment_details'] ?? null,
            'total_incentives_paid' => $row['total_incentives_paid'] ?? null,
            'incentive_paid_date' => $this->nullableDate($row['incentive_paid_date'] ?? null),
            'invoice_number' => $row['invoice_number'] ?? null,
            'invoice_status' => $row['invoice_status'] ?? 'Open_Last_Quarter',
            'user_id' => auth()->id(), // Optional: link upload to user
        ]);
    }

    protected function nullableDate($value)
    {
        if (!$value) {
            return null;
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function mapClient($name)
    {
        if (!$name) {
            return null;
        }

        $client = \App\Models\Client::where('client_name', $name)->first();
        return $client ? $client->id : null;
    }
}
