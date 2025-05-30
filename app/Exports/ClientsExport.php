<?php

namespace App\Exports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ClientsExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * Fetch all clients from the database.
     */
    public function collection()
    {
        return Client::all();
    }

    /**
     * Map each client record to a row in the spreadsheet.
     */
    public function map($client): array
    {
        return [
            $client->client_name,
            $client->client_country,
            $client->client_email,
            $client->client_phoneno,
            $client->client_whatsapp,
            $client->client_manager,
        ];
    }

    /**
     * Define column headings for the Excel sheet.
     */
    public function headings(): array
    {
        return [
            'Client Name',
            'Country',
            'Email',
            'Phone',
            'WhatsApp',
            'Manager',
        ];
    }
}
