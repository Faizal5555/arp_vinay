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

        if (empty($row['fy']) || empty($row['pn_no'])) {
            return null; // skip this row silently
        }
    
        // Attempt to map client name to ID
        $clientId = $this->mapClient($row['client_id']);
        if (!$clientId) {
            throw new \Exception("Client '{$row['client_id']}' not found.");
        }
        return new CurrentProject([
            'entry_date' => Carbon::parse($row['entry_date'])->format('Y-m-d'),
            'fy' => $row['fy'],
            'quarter' => $row['quarter'],
            'client_id' => $this->mapClient($row['client_id']),
            'company_name' => $row['company_name'],
            'pn_no' => $row['pn_no'],
            'email_subject' => $row['email_subject'],
            'commission_date' => Carbon::parse($row['commission_date'])->format('Y-m-d'),
            'currency_amount' => $row['currency_amount'],
            'original_revenue' => $row['original_revenue'],
            'margin' => $row['margin'],
            'final_invoice_amount' => $row['final_invoice_amount'],
            'comments' => $row['comments'],
            'supplier_name' => $row['supplier_name'],
            'supplier_payment_details' => $row['supplier_payment_details'],
            'total_incentives_paid' => $row['total_incentives_paid'],
            'incentive_paid_date' => Carbon::parse($row['incentive_paid_date'])->format('Y-m-d'),
            'invoice_number' => $row['invoice_number'],
            'invoice_status' => $row['invoice_status'],
            'user_id' => auth()->id(),
        ]);
    }

    protected function mapClient($name)
    {
        $client = Client::where('client_name', $name)->first();
        return $client ? $client->id : null;
    }
}