<table>
    <thead>
        <tr>
            <th>Entry Date</th>
            <th>FY</th>
            <th>Quarter</th>
            <th>Client Name</th>
            <th>Company</th>
            <th>PN No</th>
            <th>Email Subject</th>
            <th>Commission Date</th>
            <th>Currency Amount</th>
            <th>Original Revenue</th>
            <th>Margin</th>
            <th>Final Invoice</th>
            <th>Comments</th>
            <th>Supplier</th>
            <th>Supplier Payment</th>
            <th>Incentives Paid</th>
            <th>Incentive Date</th>
            <th>Invoice No</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($projects as $proj)
            <tr>
                <td>{{ $proj->entry_date ?? '-' }}</td>
                <td>{{ $proj->fy }}</td>
                <td>{{ $proj->quarter }}</td>
                <td>{{ $proj->client->client_name ?? '-' }}</td>
                <td>{{ $proj->company_name }}</td>
                <td>{{ $proj->pn_no }}</td>
                <td>{{ $proj->email_subject }}</td>
                <td>{{ $proj->commission_date }}</td>
                <td>{{ $proj->currency_amount }}</td>
                <td>{{ $proj->original_revenue }}</td>
                <td>{{ $proj->margin }}</td>
                <td>{{ $proj->final_invoice_amount }}</td>
                <td>{{ $proj->comments }}</td>
                <td>{{ $proj->supplier_name }}</td>
                <td>{{ $proj->supplier_payment_details }}</td>
                <td>{{ $proj->total_incentives_paid }}</td>
                <td>{{ $proj->incentive_paid_date }}</td>
                <td>{{ $proj->invoice_number }}</td>
                <td>{{ $proj->invoice_status }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
