@php
    $totalOriginal = 0;
    $totalInvoice = 0;
    $totalIncentive = 0;
@endphp

@foreach ($results as $project)
    @php
        $totalOriginal += floatval($project->original_revenue);
        $totalInvoice += floatval($project->final_invoice_amount);
        $totalIncentive += floatval($project->total_incentives_paid);
    @endphp

    <tr class="project-row">
        <td>
            <input type="hidden" name="id[]" value="{{ $project->id }}">
            <input type="date" name="entry_date[]" class="form-control entry-date"
                value="{{ isset($project->entry_date) && $project->entry_date ? \Carbon\Carbon::parse($project->entry_date)->format('Y-m-d') : '-' }}"
                readonly>
        </td>
        <td>
            <select name="fy[]" class="form-select">
                <option value="">-- Select FY --</option>
                @for ($i = 10; $i <= 50; $i++)
                    @php
                        $fy =
                            'FY ' .
                            str_pad($i, 2, '0', STR_PAD_LEFT) .
                            '-' .
                            str_pad(($i + 1) % 100, 2, '0', STR_PAD_LEFT);
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
                    <option value="{{ $q }}"
                        {{ isset($project) && $project->quarter == $q ? 'selected' : '' }}>
                        {{ $q }}
                    </option>
                @endforeach
            </select>
        </td>

        <td>
            <select name="client_id[]" class="form-select">
                <option value="">-- Select Client --</option>
                @foreach ($clients as $client)
                    <option value="{{ $client->id }}" {{ $project->client_id == $client->id ? 'selected' : '' }}>
                        {{ $client->client_name }}
                    </option>
                @endforeach
            </select>
        </td>
        <td>
            <select name="company_name[]" class="form-select">
                <option value="">-- Select Company --</option>
                @foreach (['ARP', 'HPI', 'URP'] as $company)
                    <option value="{{ $company }}" {{ $project->company_name == $company ? 'selected' : '' }}>
                        {{ $company }}
                    </option>
                @endforeach
            </select>
        </td>
        <td><input type="text" name="pn_no[]" class="form-control" value="{{ $project->pn_no }}">
        </td>
        <td><input type="text" name="email_subject[]" class="form-control" value="{{ $project->email_subject }}">
        </td>
        <td><input type="date" name="commission_date[]" class="form-control"
                value="{{ $project->commission_date }}"></td>
        <td><input type="text" name="currency_amount[]" class="form-control"
                value="{{ $project->currency_amount }}"></td>
        <td><input type="text" name="original_revenue[]" class="form-control"
                value="{{ $project->original_revenue }}"></td>
        <td><input type="text" name="margin[]" class="form-control" value="{{ $project->margin }}">
        </td>
        <td><input type="text" name="final_invoice_amount[]" class="form-control"
                value="{{ $project->final_invoice_amount }}"></td>
        <td><input type="text" name="comments[]" class="form-control" value="{{ $project->comments }}">
        </td>
        <td><input type="text" name="supplier_name[]" class="form-control" value="{{ $project->supplier_name }}">
        </td>
        <td><input type="text" name="supplier_payment_details[]" class="form-control"
                value="{{ $project->supplier_payment_details }}"></td>
        <td><input type="text" name="total_incentives_paid[]" class="form-control"
                value="{{ $project->total_incentives_paid }}"></td>
        <td><input type="date" name="incentive_paid_date[]" class="form-control"
                value="{{ $project->incentive_paid_date }}"></td>
        <td><input type="text" name="invoice_number[]" class="form-control" value="{{ $project->invoice_number }}">
        </td>
        <td>
            <select name="invoice_status[]" class="form-select form-select-sm badge-select" style="min-width: 120px;">
                <option value="Paid" {{ $project->invoice_status == 'Paid' ? 'selected' : '' }}>
                    Closed</option>
                <option value="waveoff" {{ $project->invoice_status == 'waveoff' ? 'selected' : '' }}>Waveoff</option>
                <option value="Pending" {{ $project->invoice_status == 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="Partial" {{ $project->invoice_status == 'Partial' ? 'selected' : '' }}>Partial Payment
                </option>
                <option value="Open_Last_Quarter"
                    {{ $project->invoice_status == 'Open_Last_Quarter' ? 'selected' : '' }}>Open
                    Project Last Quarter</option>
            </select>
        </td>
        <td>

            <button type="button" class="btn btn-outline-danger btn-sm deleteProjectBtn"
                data-id="{{ $project->id }}">
                <i class="bx bx-trash"></i> Delete
            </button>

        </td>
    </tr>
@endforeach
