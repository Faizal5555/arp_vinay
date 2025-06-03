<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ClientsSampleExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            [
                'John Doe',         // client_name
                'India',            // client_country
                'john@example.com', // client_email
                'Manager 1',        // client_manager
                '9876543210',       // client_phoneno
                '9876543210',       // client_whatsapp
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'client_name',
            'client_country',
            'client_email',
            'client_manager',
            'client_phoneno',
            'client_whatsapp',
        ];
    }
}
