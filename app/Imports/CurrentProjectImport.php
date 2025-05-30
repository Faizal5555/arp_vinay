<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\CurrentProject;
use App\Models\Client;
use Carbon\Carbon;

class CurrentProjectImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // If the entire row is empty, skip
        if (empty(array_filter($row))) {
            return null;
        }

        // Attempt to map client name to ID, allow null if not found
        $clientId = $this->mapClient($row['client_id'] ?? null);

        return new CurrentProject([
            'entry_date' => $this->formatDate($row['entry_date'] ?? null),
            'fy' => $row['fy'] ?? null,
            'quarter' => $row['quarter'] ?? null,
            'client_id' => $clientId,
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
            'invoice_status' => $row['invoice_status'] ?? null,
            'user_id' => auth()->id(),
        ]);
    }

    protected function mapClient($name)
    {
        if (!$name) {
            return null;
        }

        $client = Client::where('client_name', $name)->first();
        return $client ? $client->id : null;
    }

    protected function formatDate($value)
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
}
