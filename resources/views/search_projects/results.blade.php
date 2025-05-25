<style>
    input.form-control:focus,
    select.form-select:focus {
        border-color: #5b9bd5;
        box-shadow: 0 0 0 0.2rem rgba(91, 155, 213, 0.25);
        background-color: #fff;
    }

    /* Table Header */
    .table th {
        background-color: #00326e !important;
        color: white !important;
        font-weight: 700;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        text-align: center;
        border-bottom: 2px solid #d0d7de;
        width: 200px;
        padding: 10px 12px;
    }

    /* Table Row Hover */
    .table-hover tbody tr:hover {
        background-color: #f3f9ff;
    }

    /* Success Row Feedback */
    .table-success {
        background-color: #d1e7dd !important;
        transition: background-color 0.4s ease;
    }

    /* Totals Footer */
    tfoot td {
        font-size: 1.05rem;
        background-color: #fff3cd;
        color: #333;
        font-weight: 600;
        text-align: end;
    }

    /* Button Customization */
    .btn-outline-primary {
        font-weight: 500;
        padding: 4px 12px;
        font-size: 0.9rem;
        border-radius: 0.375rem;
    }

    /* Adjust Table Cell Alignment */
    .table td,
    .table th {
        vertical-align: middle;
        padding: 8px 12px;
    }

    .badge {
        font-size: 0.9rem;
        font-weight: 600;
        border-radius: 0.5rem;
        padding: 6px 16px;
        min-width: 80px;
        min-height: 28px;
        display: inline-block;
        line-height: 1.4;
        text-align: center;
    }

    .table td:nth-child(17) {
        min-width: 140px;
    }


    /* Optional: Smaller responsive tweak */
    @media (max-width: 768px) {

        input.form-control,
        select.form-select {
            width: 100%;
        }
    }
</style>
<div class="table-responsive">
    <table class="table text-center align-middle table-bordered table-hover">
        <thead>
            <tr>
                <th>Date</th>
                <th>FY</th>
                <th>Quarter</th>
                <th>Client</th>
                <th>Company</th>
                <th>PN No</th>
                <th>Email Subject</th>
                <th>Commission Date</th>
                <th>Currency Amount</th>
                <th>Original Revenue</th>
                <th>Margin</th>
                <th>Final Invoice Amount</th>
                <th>Comments</th>
                <th>Supplier</th>
                <th>Supplier Payment Details</th>
                <th>Incentives</th>
                <th>Incentive Date</th>
                <th>Invoice Number</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalRevenue = $totalInvoice = $totalIncentive = 0;
            @endphp
            @if ($projects->isEmpty())
                <tr>
                    <td colspan="100%" class="text-center text-muted">No records found. Please use the filters to search.
                    </td>
                </tr>
            @else
                @foreach ($projects as $proj)
                    @php
                        $totalRevenue += $proj->original_revenue;
                        $totalInvoice += $proj->final_invoice_amount;
                        $totalIncentive += $proj->total_incentives_paid;
                    @endphp
                    <tr>
                        <td>
                            <input type="date" name="entry_date[]" class="form-control entry-date"
                                value="{{ isset($proj->entry_date) && $proj->entry_date ? \Carbon\Carbon::parse($proj->entry_date)->format('Y-m-d') : '-' }}"
                                readonly>
                        </td>
                        <td>{{ $proj->fy }}</td>
                        <td>{{ $proj->quarter }}</td>
                        <td>{{ $proj->client->client_name ?? '-' }}</td>
                        <td>{{ $proj->company_name }}</td>
                        <td>{{ $proj->pn_no }}</td>
                        <td>{{ $proj->email_subject }}</td>
                        <td>{{ \Carbon\Carbon::parse($proj->commission_date)->format('d-m-Y') }}</td>
                        <td class="text-end">
                            {{ number_format(is_numeric($proj->currency_amount) ? $proj->currency_amount : 0, 2) }}</td>
                        <td class="text-end">
                            {{ number_format(is_numeric($proj->original_revenue) ? $proj->original_revenue : 0, 2) }}
                        </td>
                        <td class="text-end">{{ number_format(is_numeric($proj->margin) ? $proj->margin : 0, 2) }}</td>
                        <td class="text-end">
                            {{ number_format(is_numeric($proj->final_invoice_amount) ? $proj->final_invoice_amount : 0, 2) }}
                        </td>

                        <td>{{ $proj->comments }}</td>
                        <td>{{ $proj->supplier_name }}</td>
                        <td>{{ $proj->supplier_payment_details }}</td>
                        <td class="text-end">{{ number_format($proj->total_incentives_paid, 2) }}</td>
                        <td>
                            {{ \Carbon\Carbon::parse($proj->incentive_paid_date)->format('d-m-Y') }}
                        </td>
                        <td>{{ $proj->invoice_number }}</td>
                        <td>
                            @php
                                $status = strtolower($proj->invoice_status);
                                $badgeClass = match ($status) {
                                    'paid' => 'bg-success',
                                    'waveoff' => 'bg-primary',
                                    'pending' => 'bg-warning text-dark',
                                    'partial' => 'bg-info text-dark',
                                    default => 'bg-secondary',
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }} text-uppercase px-3 py-1">
                                {{ ucfirst($status) }}
                            </span>
                        </td>

                    </tr>
                @endforeach
            @endif
        </tbody>
        {{-- <tfoot class="fw-bold text-dark">
            <tr>
                <td colspan="8" class="text-end">Totals:</td>
                <td class="text-end" style="background: #fff9c4">{{ number_format($totalRevenue, 2) }}</td>
                <td></td>
                <td class="text-end" style="background: #fff9c4">{{ number_format($totalInvoice, 2) }}</td>
                <td colspan="3"></td>
                <td class="text-end" style="background: #fff9c4">{{ number_format($totalIncentive, 2) }}</td>
                <td colspan="3"></td>
            </tr>
        </tfoot> --}}
    </table>
</div>
