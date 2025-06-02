@extends('layouts.master')

@section('content')
    <div class="container mt-5">
        <div class="mb-4 border-0 shadow-sm card bg-light">
            <div class="card-body d-flex align-items-center justify-content-between">
                <h4 class="mb-0 fw-bold text-dark">
                    <i class="bx bx-calendar-week me-2 text-dark"></i> Open Projects from Last Quarter
                </h4>
            </div>
        </div>

        <div class="mb-4 col-md-12 position-relative">
            <i class="bx bx-search position-absolute top-50 start-0 translate-middle-y ps-3 text-muted fs-5"></i>
            <input type="text" id="searchKeyword" class="shadow-sm form-control ps-5 rounded-4 w-100" style="height:50px;"
                placeholder="Search PN No, Subject, Client..." autocomplete="off">
        </div>
        <div class="mb-2 d-flex justify-content-end">
            <a href="{{ route('open_quarter.download') }}" class="btn btn-primary" style="background-color:#00326e;">
                <i class="bx bx-download"></i> Download
            </a>
        </div>


        <form id="openQuarterForm">
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
                            <tr class="project-row" data-id="{{ $project->id }}">

                                <td>
                                    <input type="hidden" name="id[]" value="{{ $project->id }}">
                                    <input type="date" name="entry_date[]" class="form-control entry-date"
                                        value="{{ $project->entry_date }}" readonly>
                                </td>
                                <td><select name="fy[]" class="form-select">
                                        @for ($i = 10; $i <= 50; $i++)
                                            @php $fy = 'FY ' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-' . str_pad(($i + 1) % 100, 2, '0', STR_PAD_LEFT); @endphp
                                            <option value="{{ $fy }}" {{ $project->fy == $fy ? 'selected' : '' }}>
                                                {{ $fy }}</option>
                                        @endfor
                                    </select>
                                </td>
                                <td><select name="quarter[]" class="form-select">
                                        @foreach (['Q1', 'Q2', 'Q3', 'Q4'] as $q)
                                            <option value="{{ $q }}"
                                                {{ $project->quarter == $q ? 'selected' : '' }}>{{ $q }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><select name="client_id[]" class="form-select">
                                        <option value="">-- Select Client --</option>
                                        @foreach ($clients as $client)
                                            <option value="{{ $client->id }}"
                                                {{ $project->client_id == $client->id ? 'selected' : '' }}>
                                                {{ $client->client_name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><select name="company_name[]" class="form-select">
                                        @foreach (['ARP', 'HPI', 'URP'] as $company)
                                            <option value="{{ $company }}"
                                                {{ $project->company_name == $company ? 'selected' : '' }}>
                                                {{ $company }}</option>
                                        @endforeach
                                    </select></td>
                                <td><input type="text" name="pn_no[]" class="form-control"
                                        value="{{ $project->pn_no }}"></td>
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
                                    <select name="invoice_status[]" class="form-select">
                                        <option value="Open_Last_Quarter" selected>Open Project Last Quarter</option>
                                        {{-- <option value="Pending">Pending</option>
                                        <option value="Paid">Paid</option>
                                        <option value="partial">Partial Payment</option>
                                        <option value="waveoff">Waveoff</option> --}}
                                    </select>
                                    <textarea name="partial_comment[]" class="mt-1 form-control partialCommentBox" rows="1"
                                        style="display: {{ $project->invoice_status == 'partial' ? 'block' : 'none' }}">{{ $project->partial_comment }}</textarea>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-outline-warning btn-sm moveProjectBtn"
                                        data-id="{{ $project->id }}">
                                        <i class="bx bx-transfer"></i> Move
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

    <div class="modal fade" id="moveModal" tabindex="-1" aria-labelledby="moveModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Move Project</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="moveType" class="mb-2 fw-bold">Select Move Type</label>
                    <select id="moveType" class="form-select">
                        <option value="Pending">✓ Move to Pending</option>
                        <option value="Paid">✓ Move to Closed</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmMoveBtn">Confirm</button>
                </div>
            </div>
        </div>
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
        let selectedRow = null;
        let selectedId = null;

        // Recalculate totals for original revenue, invoice, and incentive
        function updateTotals() {
            let revenue = 0,
                invoice = 0,
                incentive = 0;

            document.querySelectorAll('input[name="original_revenue[]"]').forEach(input => {
                revenue += parseFloat(input.value) || 0;
            });
            document.querySelectorAll('input[name="final_invoice_amount[]"]').forEach(input => {
                invoice += parseFloat(input.value) || 0;
            });
            document.querySelectorAll('input[name="total_incentives_paid[]"]').forEach(input => {
                incentive += parseFloat(input.value) || 0;
            });

            document.getElementById('totalOriginal').textContent = revenue.toFixed(2);
            document.getElementById('totalInvoice').textContent = invoice.toFixed(2);
            document.getElementById('totalIncentive').textContent = incentive.toFixed(2);
        }

        // Attach change listeners to update totals in real-time
        function bindTotalChangeListeners() {
            const inputs = document.querySelectorAll(
                'input[name="original_revenue[]"], input[name="final_invoice_amount[]"], input[name="total_incentives_paid[]"]'
            );
            inputs.forEach(input => {
                input.addEventListener('input', updateTotals);
                input.addEventListener('change', updateTotals);
            });
        }

        // Bind Save All logic for form submission
        function bindSaveAll() {
            document.getElementById('openQuarterForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const rows = document.querySelectorAll('.project-row');
                const projects = [];

                rows.forEach(row => {
                    const data = {
                        id: row.querySelector('[name="id[]"]').value,
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
                        final_invoice_amount: row.querySelector('[name="final_invoice_amount[]"]')
                            .value,
                        comments: row.querySelector('[name="comments[]"]').value,
                        supplier_name: row.querySelector('[name="supplier_name[]"]').value,
                        supplier_payment_details: row.querySelector(
                            '[name="supplier_payment_details[]"]').value,
                        total_incentives_paid: row.querySelector('[name="total_incentives_paid[]"]')
                            .value,
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
                    .catch(() => Swal.fire('Error', 'Failed to save changes.', 'error'));
            });
        }

        // Named function for confirm move to prevent duplicate bindings
        function handleMoveConfirmClick() {
            if (!selectedRow || !selectedId) return;

            const status = document.getElementById('moveType').value;

            fetch(`{{ url('pending-projects/move') }}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: selectedId,
                        status
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        selectedRow.remove();
                        $('#moveModal').modal('hide');
                        updateTotals();
                        Swal.fire('Moved', data.message, 'success');
                    } else {
                        Swal.fire('Error', data.message || 'Something went wrong.', 'error');
                    }
                })
                .catch(() => Swal.fire('Error', 'Something went wrong.', 'error'));
        }

        // Binds modal open buttons and sets up confirm button (once)
        function bindMoveModal() {
            document.querySelectorAll('.moveProjectBtn').forEach(button => {
                button.addEventListener('click', function() {
                    selectedRow = this.closest('tr');
                    selectedId = this.dataset.id;

                    document.getElementById('moveType').selectedIndex = 0;
                    $('#moveModal').modal('show');
                });
            });

            // Dropdown tick update
            document.getElementById('moveType').addEventListener('change', function() {
                const opts = this.options;
                for (let i = 0; i < opts.length; i++) {
                    opts[i].text = opts[i].text.replace('✓', '').trim();
                }
                opts[this.selectedIndex].text = '✓ ' + opts[this.selectedIndex].text;
            });

            // Remove any old handlers before adding new one to avoid duplicates
            const oldBtn = document.getElementById('confirmMoveBtn');
            const newBtn = oldBtn.cloneNode(true);
            oldBtn.parentNode.replaceChild(newBtn, oldBtn);
            newBtn.addEventListener('click', handleMoveConfirmClick);
        }

        // Main initializer
        document.addEventListener('DOMContentLoaded', function() {
            updateTotals();
            bindTotalChangeListeners();
            bindMoveModal();
            bindSaveAll();
        });

        // Search functionality
        document.getElementById('searchKeyword').addEventListener('input', function() {
            const keyword = this.value.trim();

            fetch(`{{ route('openlastquarter.search') }}?keyword=${encodeURIComponent(keyword)}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    document.querySelector('#pendingTable tbody').innerHTML = html;
                    updateTotals();
                    bindTotalChangeListeners();
                    bindMoveModal(); // rebind move modal
                });
        });
    </script>
@endpush
