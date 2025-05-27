<style>
    input.form-control,
    select.form-select {
        min-height: 42px;
        border-radius: 0.5rem;
        padding: 0.4rem 0.75rem;
        width: 250px;
        border: 1px solid #d0d7de;
        border-color: lightblue;
        background-color: #fefefe;
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.04);
        transition: border-color 0.3s, box-shadow 0.3s;
        font-weight: bold;
        color: #212529;
        /* Use a dark color for better contrast */
        font-size: 1rem;
        /* Optional: increase size slightly */
        font-family: 'Segoe UI', sans-serif;
        /* Use a clear font */
        -webkit-font-smoothing: antialiased;
        /* Improve text rendering in Chrome */
        -moz-osx-font-smoothing: grayscale;/
    }

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
        font-size: 0.80rem;
        text-align: center;
        border-bottom: 2px solid #d0d7de;
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

    /* Optional: Smaller responsive tweak */
    @media (max-width: 768px) {

        input.form-control,
        select.form-select {
            width: 100%;
        }
    }
</style>
<input type="hidden" name="id[]" value="{{ $project->id ?? '' }}">
<td>
    <input type="date" name="entry_date[]" class="form-control entry-date"
        value="{{ isset($project->entry_date) ? \Carbon\Carbon::parse($project->entry_date)->format('Y-m-d') : \Carbon\Carbon::now()->format('Y-m-d') }}"
        readonly>
</td>

{{-- <td><input type="text" name="fy[]" class="form-control" placeholder="Enter here FY" value="{{ $project->fy ?? '' }}">
</td> --}}
<td>
    <select name="fy[]" class="form-select">
        <option value="">-- Select FY --</option>
        @for ($i = 10; $i <= 50; $i++)
            @php
                $fy = 'FY ' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-' . str_pad(($i + 1) % 100, 2, '0', STR_PAD_LEFT);
            @endphp
            <option value="{{ $fy }}" {{ isset($project) && $project->fy == $fy ? 'selected' : '' }}>
                {{ $fy }}
            </option>
        @endfor
    </select>
</td>

<td>
    <select name="quarter[]" class="form-select">
        <option value="">-- Select Quarter --</option>
        @foreach (['Q1', 'Q2', 'Q3', 'Q4'] as $q)
            <option value="{{ $q }}" {{ isset($project) && $project->quarter == $q ? 'selected' : '' }}>
                {{ $q }}
            </option>
        @endforeach
    </select>
</td>

<td>
    <select name="client_id[]" class="form-select">
        <option value="">-- Select Client --</option>
        @foreach ($clients as $client)
            <option value="{{ $client->id }}"
                {{ isset($project) && $project->client_id == $client->id ? 'selected' : '' }}>
                {{ $client->client_name }}
            </option>
        @endforeach
    </select>
</td>

<td>
    <select name="company_name[]" class="form-select">
        <option value="">-- Select Company --</option>
        @foreach (['ARP', 'HPI', 'URP'] as $company)
            <option value="{{ $company }}"
                {{ isset($project) && $project->company_name == $company ? 'selected' : '' }}>
                {{ $company }}
            </option>
        @endforeach
    </select>
</td>

<td><input type="text" name="pn_no[]" class="form-control" placeholder="Enter here PN no"
        value="{{ $project->pn_no ?? '' }}"></td>
<td><input type="text" name="email_subject[]" class="form-control" placeholder="Enter here email subject"
        value="{{ $project->email_subject ?? '' }}"></td>
<td><input type="date" name="commission_date[]" class="form-control" value="{{ $project->commission_date ?? '' }}">
</td>
<td><input type="text" name="currency_amount[]" class="form-control" placeholder="Enter here currency amount"
        value="{{ $project->currency_amount ?? '' }}">
</td>
<td><input type="text" name="original_revenue[]" class="form-control" placeholder="Enter here original revenue"
        value="{{ $project->original_revenue ?? '' }}"></td>
<td><input type="text" name="margin[]" class="form-control" placeholder="Enter here margin"
        value="{{ $project->margin ?? '' }}"></td>
<td><input type="text" name="final_invoice_amount[]" class="form-control"
        value="{{ $project->final_invoice_amount ?? '' }}"></td>
<td><input type="text" name="comments[]" class="form-control" placeholder="Enter here comments"
        value="{{ $project->comments ?? '' }}"></td>
<td><input type="text" name="supplier_name[]" class="form-control" placeholder="Enter here supplier name"
        value="{{ $project->supplier_name ?? '' }}"></td>
<td><input type="text" name="supplier_payment_details[]" class="form-control"
        placeholder="Enter here supplier payment" value="{{ $project->supplier_payment_details ?? '' }}"></td>
<td><input type="text" name="total_incentives_paid[]" class="form-control"
        placeholder="Enter here total incentives paid" value="{{ $project->total_incentives_paid ?? '' }}"></td>
<td><input type="date" name="incentive_paid_date[]" class="form-control"
        value="{{ $project->incentive_paid_date ?? '' }}"></td>
<td><input type="text" name="invoice_number[]" class="form-control" placeholder="Enter here invoice no"
        value="{{ $project->invoice_number ?? '' }}">
</td>

<td>
    <select name="invoice_status[]" class="form-select">
        <option value="">-- Select status --</option>
        <option value="Paid" {{ isset($project) && $project->invoice_status == 'Paid' ? 'selected' : '' }}>Paid
        </option>
        <option value="Pending" {{ isset($project) && $project->invoice_status == 'Pending' ? 'selected' : '' }}>
            Pending</option>
    </select>
</td>
@php
    $hasData = isset($project) && collect($project->getAttributes())->filter()->isNotEmpty();
@endphp

<td>
    @if ($hasData)
        <button type="button" class="btn btn-secondary moveRow">Move</button>
    @endif
</td>
