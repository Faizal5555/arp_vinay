@extends('layouts.master')

@section('content')
    <div class="container mt-5">
        <div class="mb-4 border-0 shadow-sm card bg-light">
            <div class="card-body d-flex align-items-center justify-content-between">
                <h4 class="mb-0 fw-bold text-dark">
                    <i class="bx bx-time-five me-2 text-dark"></i> Pending Projects
                </h4>
            </div>
        </div>

        <div class="mb-2 d-flex justify-content-end">
            <a href="{{ route('pending_projects.download') }}" class="btn btn-primary" style="background-color:#00326e;">
                <i class="bx bx-download"></i> Download
            </a>
        </div>

        <form id="pendingProjectForm">
            @csrf
            <div class="table-responsive">
                <table class="table text-center align-middle table-bordered table-hover" id="pendingTable">
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
                            <th>Currency</th>
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
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalOriginal = 0;
                            $totalInvoice = 0;
                            $totalIncentive = 0;
                        @endphp
                        @foreach ($pendingProjects as $project)
                            @php
                                $totalOriginal += floatval($project->original_revenue);
                                $totalInvoice += floatval($project->final_invoice_amount);
                                $totalIncentive += floatval($project->total_incentives_paid);
                            @endphp
                            <tr class="project-row">
                                <td>
                                    <input type="date" name="entry_date[]" class="form-control entry-date"
                                        value="{{ isset($project->entry_date) && $project->entry_date ? \Carbon\Carbon::parse($project->entry_date)->format('Y-m-d') : '-' }}"
                                        readonly>
                                </td>
                                <td><input type="text" name="fy[]" class="form-control" value="{{ $project->fy }}">
                                </td>
                                <td><input type="text" name="quarter[]" class="form-control"
                                        value="{{ $project->quarter }}"></td>
                                <td>
                                    <select name="client_id[]" class="form-select">
                                        @foreach ($clients as $client)
                                            <option value="{{ $client->id }}"
                                                {{ $project->client_id == $client->id ? 'selected' : '' }}>
                                                {{ $client->client_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select name="company_name[]" class="form-select">
                                        @foreach (['ARP', 'HPI', 'URP'] as $company)
                                            <option value="{{ $company }}"
                                                {{ $project->company_name == $company ? 'selected' : '' }}>
                                                {{ $company }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input type="text" name="pn_no[]" class="form-control"
                                        value="{{ $project->pn_no }}">
                                </td>
                                <td><input type="text" name="email_subject[]" class="form-control"
                                        value="{{ $project->email_subject }}"></td>
                                <td><input type="date" name="commission_date[]" class="form-control"
                                        value="{{ $project->commission_date }}"></td>
                                <td><input type="text" name="currency_amount[]" class="form-control"
                                        value="{{ $project->currency_amount }}"></td>
                                <td><input type="text" name="original_revenue[]" class="form-control"
                                        value="{{ $project->original_revenue }}"></td>
                                <td><input type="text" name="margin[]" class="form-control"
                                        value="{{ $project->margin }}"></td>
                                <td><input type="text" name="final_invoice_amount[]" class="form-control"
                                        value="{{ $project->final_invoice_amount }}"></td>
                                <td><input type="text" name="comments[]" class="form-control"
                                        value="{{ $project->comments }}"></td>
                                <td><input type="text" name="supplier_name[]" class="form-control"
                                        value="{{ $project->supplier_name }}"></td>
                                <td><input type="text" name="supplier_payment_details[]" class="form-control"
                                        value="{{ $project->supplier_payment_details }}"></td>
                                <td><input type="text" name="total_incentives_paid[]" class="form-control"
                                        value="{{ $project->total_incentives_paid }}"></td>
                                <td><input type="date" name="incentive_paid_date[]" class="form-control"
                                        value="{{ $project->incentive_paid_date }}"></td>
                                <td><input type="text" name="invoice_number[]" class="form-control"
                                        value="{{ $project->invoice_number }}"></td>
                                <td>
                                    <select name="invoice_status[]" class="form-select invoiceStatusSelect">
                                        <option value="Pending"
                                            {{ $project->invoice_status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="Paid" {{ $project->invoice_status == 'Paid' ? 'selected' : '' }}>
                                            Paid</option>
                                        <option value="waveoff"
                                            {{ $project->invoice_status == 'waveoff' ? 'selected' : '' }}>Waveoff</option>
                                        <option value="partial"
                                            {{ $project->invoice_status == 'partial' ? 'selected' : '' }}>Partial Payment
                                        </option>
                                    </select>

                                    <textarea name="partial_comment[]" class="mt-2 form-control partialCommentBox fw-bold" rows="2"
                                        placeholder="Add comment for partial payment..."
                                        style="color: #212529; display: {{ $project->invoice_status == 'partial' ? 'block' : 'none' }};">{{ $project->partial_comment }}</textarea>

                                </td>
                                <td>
                                    <button type="button" class="btn btn-outline-primary btn-sm updateStatusBtn"
                                        data-id="{{ $project->id }}">
                                        <i class="bx bx-check-circle"></i> Update
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="fw-bold text-dark">
                        <tr>
                            <td colspan="9" class="text-end">Totals:</td>
                            <td class="text-start" id="totalOriginal"
                                style="background: yellow; color:#212529; font-weight:bold;">
                                {{ number_format($totalOriginal, 2) }}</td>
                            <td></td>
                            <td class="text-start" id="totalInvoice"
                                style="background: yellow; color:#212529; font-weight:bold;">
                                {{ number_format($totalInvoice, 2) }}</td>
                            <td colspan="3"></td>
                            <td class="text-start" id="totalIncentive"
                                style="background: yellow; color:#212529; font-weight:bold;">
                                {{ number_format($totalIncentive, 2) }}</td>
                            <td colspan="4"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @if ($pendingProjects->count())
                <div class="mt-3 text-end">
                    <button type="submit" class="px-4 py-2 btn btn-success fw-bold">
                        <i class="bx bx-save"></i> Save All Changes
                    </button>
                </div>
            @endif

        </form>
    </div>
@endsection
<style>
    /* Input + Select Styles */
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
        font-weight: 700 !important;
        font-size: 0.80rem !important;
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

@push('js')
    <script>
        function updateTotals() {
            let original = 0,
                invoice = 0,
                incentive = 0;

            document.querySelectorAll('input[name="original_revenue[]"]').forEach(input => {
                original += parseFloat(input.value) || 0;
            });

            document.querySelectorAll('input[name="final_invoice_amount[]"]').forEach(input => {
                invoice += parseFloat(input.value) || 0;
            });

            document.querySelectorAll('input[name="total_incentives_paid[]"]').forEach(input => {
                incentive += parseFloat(input.value) || 0;
            });

            // Set text only if the elements exist
            const originalEl = document.getElementById('totalOriginal');
            const invoiceEl = document.getElementById('totalInvoice');
            const incentiveEl = document.getElementById('totalIncentive');

            if (originalEl) originalEl.textContent = original.toFixed(2);
            if (invoiceEl) invoiceEl.textContent = invoice.toFixed(2);
            if (incentiveEl) incentiveEl.textContent = incentive.toFixed(2);
        }

        document.querySelectorAll('.updateStatusBtn').forEach(btn => {
            btn.addEventListener('click', function() {
                const row = this.closest('tr');
                const id = this.getAttribute('data-id');
                const status = row.querySelector('select[name="invoice_status[]"]').value;

                const formData = new FormData();
                formData.append('status', status);

                const commentInput = row.querySelector('textarea[name="partial_comment[]"]');
                if (commentInput) {
                    formData.append('partial_comment', commentInput.value);
                }


                fetch(`/pending-projects/update-status/${id}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            if (status === 'Paid' || status.toLowerCase() === 'waveoff') {
                                row.remove();
                            } else {
                                row.classList.add('table-success');
                                setTimeout(() => row.classList.remove('table-success'), 1500);
                            }
                            updateTotals(); // recalculate totals
                            Swal.fire('Updated', data.message, 'success');
                        } else {
                            Swal.fire('Error', data.message || 'Update failed.', 'error');
                        }
                    });
            });
        });

        document.querySelectorAll('.invoiceStatusSelect').forEach(select => {
            select.addEventListener('change', function() {
                const commentBox = this.closest('td').querySelector('.partialCommentBox');
                if (this.value === 'partial') {
                    commentBox.style.display = 'block';
                } else {
                    commentBox.style.display = 'none';
                }
            });
        });


        document.getElementById('pendingProjectForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const rows = document.querySelectorAll('.project-row');
            const projects = [];

            rows.forEach(row => {
                const data = {
                    fy: row.querySelector('[name="fy[]"]').value,
                    quarter: row.querySelector('[name="quarter[]"]').value,
                    client_id: row.querySelector('[name="client_id[]"]').value,
                    company_name: row.querySelector('[name="company_name[]"]').value,
                    pn_no: row.querySelector('[name="pn_no[]"]').value,
                    email_subject: row.querySelector('[name="email_subject[]"]').value,
                    commission_date: row.querySelector('[name="commission_date[]"]').value,
                    currency_amount: row.querySelector('[name="currency_amount[]"]').value,
                    original_revenue: row.querySelector('[name="original_revenue[]"]').value,
                    margin: row.querySelector('[name="margin[]"]').value,
                    final_invoice_amount: row.querySelector('[name="final_invoice_amount[]"]').value,
                    comments: row.querySelector('[name="comments[]"]').value,
                    supplier_name: row.querySelector('[name="supplier_name[]"]').value,
                    supplier_payment_details: row.querySelector('[name="supplier_payment_details[]"]')
                        .value,
                    total_incentives_paid: row.querySelector('[name="total_incentives_paid[]"]').value,
                    incentive_paid_date: row.querySelector('[name="incentive_paid_date[]"]').value,
                    invoice_number: row.querySelector('[name="invoice_number[]"]').value,
                    invoice_status: row.querySelector('[name="invoice_status[]"]').value,
                    partial_comment: row.querySelector('[name="partial_comment[]"]')?.value || ''
                };

                projects.push(data);
            });

            fetch(`{{ route('pending.bulkUpdate') }}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        projects
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Success', data.message, 'success');
                    } else {
                        Swal.fire('Error', data.message || 'Something went wrong.', 'error');
                    }
                })
                .catch(err => Swal.fire('Error', 'Failed to save changes.', 'error'));
        });

        function bindTotalChangeListeners() {
            const inputs = document.querySelectorAll(
                'input[name="original_revenue[]"], input[name="final_invoice_amount[]"], input[name="total_incentives_paid[]"]'
            );

            inputs.forEach(input => {
                input.addEventListener('input', updateTotals); // For typing
                input.addEventListener('change', updateTotals); // For paste/select/etc.
            });
        }

        // Call it once DOM is ready
        document.addEventListener('DOMContentLoaded', bindTotalChangeListeners);
    </script>
@endpush
