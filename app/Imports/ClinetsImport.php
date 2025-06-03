<?php

namespace App\Imports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ClientsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (empty(array_filter($row))) {
            return null; // skip empty rows
        }

        return new Client([
            'client_name'     => $row['client_name'] ?? null,
            'client_country'  => $row['client_country'] ?? null,
            'client_email'    => $row['client_email'] ?? null,
            'client_manager'  => $row['client_manager'] ?? null,
            'client_phoneno'  => $row['client_phoneno'] ?? null,
            'client_whatsapp' => $row['client_whatsapp'] ?? null,
            'user_id'         => auth()->id(), // Always set current user id
        ]);
    }
}
