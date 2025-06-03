@extends('layouts.master')

@section('content')
    <div class="container mt-4">
        <div class="mb-4 border-0 shadow-sm card bg-light">
            <div class="card-body d-flex align-items-center justify-content-between">
                <h4 class="mb-0 fw-bold text-dark">
                    <i class="bx bx-check-circle me-2 text-dark"></i> Closed Projects
                </h4>
            </div>
        </div>

        <form id="filterForm" class="mb-4 row g-3">
            <div class="row">
                <div class="col-md-3">
                    <label>FY</label>
                    <select name="fy" class="form-select">
                        <option value="">-- FY --</option>
                        @for ($i = 10; $i <= 50; $i++)
                            @php
                                $fy =
                                    'FY ' .
                                    str_pad($i, 2, '0', STR_PAD_LEFT) .
                                    '-' .
                                    str_pad(($i + 1) % 100, 2, '0', STR_PAD_LEFT);
                            @endphp
                            <option value="{{ $fy }}">{{ $fy }}</option>
                        @endfor
                    </select>
                </div>

                <div class="col-md-3">
                    <label>Quarter</label>
                    <select name="quarter" class="form-select">
                        <option value="">-- Quarter --</option>
                        @foreach (['Q1', 'Q2', 'Q3', 'Q4'] as $q)
                            <option value="{{ $q }}">{{ $q }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label> Client</label>
                    <select name="client_id" class="form-select">
                        <option value="">-- Client --</option>
                        @foreach ($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->client_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label>Company</label>
                    <select name="company_name" class="form-select">
                        <option value="">-- Company --</option>
                        <option value="ARP">ARP</option>
                        <option value="HPI">HPI</option>
                        <option value="URP">URP</option>
                    </select>
                </div>

                <div class="mt-3 col-md-2">
                    <label>PN No</label>
                    <input type="text" name="pn_no" class="form-control" placeholder="Search PN No">
                </div>
            </div>

            {{-- <div class="col-md-2">
                <input type="text" name="keyword" id="searchKeyword" class="form-control" placeholder="General Search">
            </div> --}}
        </form>


        <div class="mb-4 col-md-12 position-relative">
            <i class="bx bx-search position-absolute top-50 start-0 translate-middle-y ps-3 text-muted fs-5"></i>
            <input type="text" id="searchKeyword" class="shadow-sm form-control ps-5 rounded-4 w-100"
                style="height:50px;" placeholder="Search Subject, Client..." autocomplete="off">
        </div>

        <div class="gap-2 d-flex justify-content-end">
            <a href="{{ route('closed_projects.download') }}" class="mb-3 btn btn-primary"
                style="background-color:#00326e;">
                <i class="bx bx-download"></i> Download
            </a>
            <button type="button" class="mb-3 btn" style="background-color:#00326e; color:white;" data-bs-toggle="modal"
                data-bs-target="#uploadClosedModal">
                <i class="bx bx-upload"></i> Upload
            </button>
        </div>
        <form id="pendingProjectForm">
            @csrf
            <div class="table-responsive">
                <table class="table text-center align-middle table-bordered table-hover" id="closedProjectsTable">
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
                        @php
                            $totalOriginal = 0;
                            $totalInvoice = 0;
                            $totalIncentive = 0;
                        @endphp
                        @foreach ($closedProjects as $project)
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
                                            <option value="{{ $fy }}"
                                                {{ isset($project) && $project->fy == $fy ? 'selected' : '' }}>
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
                                            <option value="{{ $client->id }}"
                                                {{ $project->client_id == $client->id ? 'selected' : '' }}>
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
                                        value="{{ $project->margin }}">
                                </td>
                                <td><input type="text" name="final_invoice_amount[]" class="form-control"
                                        value="{{ $project->final_invoice_amount }}"></td>
                                <td><input type="text" name="comments[]" class="form-control"
                                        value="{{ $project->comments }}">
                                </td>
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
                                    <select name="invoice_status[]" class="form-select form-select-sm badge-select"
                                        style="min-width: 120px;">
                                        <option value="Paid" {{ $project->invoice_status == 'Paid' ? 'selected' : '' }}>
                                            Closed</option>
                                        <option value="waveoff"
                                            {{ $project->invoice_status == 'waveoff' ? 'selected' : '' }}>Waveoff</option>
                                        <option value="Pending"
                                            {{ $project->invoice_status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="Partial"
                                            {{ $project->invoice_status == 'Partial' ? 'selected' : '' }}>Partial Payment
                                        </option>
                                        <option value="Open_Last_Quarter"
                                            {{ $project->invoice_status == 'Open_Last_Quarter' ? 'selected' : '' }}>Open
                                            Project Last Quarter</option>
                                    </select>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold text-dark">
                            <td colspan="9" class="fw-bold text-end">Totals:</td>
                            <td id="totalOriginal" style="background: yellow; color:#212529; font-weight:bold;">
                                {{ number_format($totalOriginal, 2) }}</td>
                            <td></td>
                            <td id="totalInvoice" style="background: yellow; color:#212529; font-weight:bold;">
                                {{ number_format($totalInvoice, 2) }}</td>
                            <td colspan="3"></td>
                            <td id="totalIncentive" style="background: yellow; color:#212529; font-weight:bold;">
                                {{ number_format($totalIncentive, 2) }}</td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            @if ($closedProjects->count())
                <div class="mt-3 text-end">
                    <button type="submit" class="px-4 py-2 btn btn-success fw-bold">
                        <i class="bx bx-save"></i> Save All Changes
                    </button>
                </div>
            @endif
        </form>

    </div>

    <!-- Upload Modal -->
    <div class="modal fade" id="uploadClosedModal" tabindex="-1" aria-labelledby="uploadClosedModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form id="closedBulkUploadForm" enctype="multipart/form-data" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-semibold">Upload Closed Projects</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Download Sample File</label>
                        <div>
                            <a href="{{ route('closed_projects.download_sample') }}" class="btn btn-sm btn-primary"
                                style="background-color:#00326e;">
                                <i class="bx bx-download"></i> Download Sample XLSX
                            </a>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Upload XLSX File</label>
                        <input type="file" name="file" class="form-control w-100" accept=".xlsx" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="closedUploadBtn" class="btn btn-primary"
                        style="background-color:#00326e;">
                        <i class="bx bx-upload"></i> Upload
                    </button>
                </div>
            </form>
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
        white-space: nowrap;
    }

    .badge-select {
        font-weight: bold;
        border: none;
        color: #fff;
        padding: 2px 8px;
        border-radius: 0.5rem;
        background-color: #6c757d;
        /* default bg */
    }

    .badge-select option[value="Paid"] {
        background-color: #198754;
        /* green */
        color: #fff;
    }

    .badge-select option[value="waveoff"] {
        background-color: #0d6efd;
        /* blue */
        color: #fff;
    }

    .badge-select option[value="Pending"] {
        background-color: #ffab00;
        /* green */
        color: #fff;
    }

    .badge-select option[value="Partial"] {
        background-color: blue;
        /* blue */
        color: #fff;
    }

    .badge-select option[value="Open_Last_Quarter"] {
        background-color: blue;
        /* blue */
        color: #fff;
    }

    .badge-select.paid {
        background-color: #198754;
    }

    .badge-select.waveoff {
        background-color: #0d6efd;
    }

    .badge-select.Open_Last_Quarter {
        background-color: #0d6efd;
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
        $(document).ready(function() {

            document.getElementById('pendingProjectForm').addEventListener('submit', function(e) {
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
                        original_revenue: row.querySelector('[name="original_revenue[]"]')
                            .value,
                        margin: row.querySelector('[name="margin[]"]').value,
                        final_invoice_amount: row.querySelector(
                            '[name="final_invoice_amount[]"]').value,
                        comments: row.querySelector('[name="comments[]"]').value,
                        supplier_name: row.querySelector('[name="supplier_name[]"]').value,
                        supplier_payment_details: row.querySelector(
                                '[name="supplier_payment_details[]"]')
                            .value,
                        total_incentives_paid: row.querySelector(
                            '[name="total_incentives_paid[]"]').value,
                        incentive_paid_date: row.querySelector('[name="incentive_paid_date[]"]')
                            .value,
                        invoice_number: row.querySelector('[name="invoice_number[]"]').value,
                        invoice_status: row.querySelector('[name="invoice_status[]"]').value,
                        partial_comment: row.querySelector('[name="partial_comment[]"]')
                            ?.value || ''
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

        });

        function applyBadgeStyle(select) {
            select.classList.remove('paid', 'waveoff');
            if (select.value === 'Paid') {
                select.classList.add('paid');
            } else if (select.value === 'Pending') {
                select.classList.add('Pending');
            } else if (select.value === 'partial') {
                select.classList.add('partial');
            } else if (select.value === 'waveoff') {
                select.classList.add('waveoff');
            } else if (select.value === 'Open_Last_Quarter') {
                select.classList.add('Open_Last_Quarter');
            }
        }

        document.querySelectorAll('.badge-select').forEach(select => {
            applyBadgeStyle(select);
            select.addEventListener('change', function() {
                applyBadgeStyle(this);
            });
        });

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

            const originalEl = document.getElementById('totalOriginal');
            const invoiceEl = document.getElementById('totalInvoice');
            const incentiveEl = document.getElementById('totalIncentive');

            if (originalEl) originalEl.textContent = original.toFixed(2);
            if (invoiceEl) invoiceEl.textContent = invoice.toFixed(2);
            if (incentiveEl) incentiveEl.textContent = incentive.toFixed(2);
        }

        function bindLiveTotalListeners() {
            const inputs = document.querySelectorAll(
                'input[name="original_revenue[]"], input[name="final_invoice_amount[]"], input[name="total_incentives_paid[]"]'
            );

            inputs.forEach(input => {
                input.addEventListener('input', updateTotals);
                input.addEventListener('change', updateTotals);
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            bindLiveTotalListeners();
            updateTotals(); // Initial
        });

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('filterForm');
            const searchKeywordInput = document.getElementById('searchKeyword');

            function triggerAjaxLoad() {
                const formData = new URLSearchParams(new FormData(form)).toString();
                const keyword = searchKeywordInput.value.trim();
                const fullQuery = formData + '&keyword=' + encodeURIComponent(keyword);

                fetch(`{{ route('closed.search') }}?${fullQuery}`, {
                        method: 'GET', // ✅ Important: method GET here
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest' // to detect AJAX
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        document.querySelector('#closedProjectsTable tbody').innerHTML = html;
                        updateTotals();
                        bindLiveTotalListeners();
                        document.querySelectorAll('.badge-select').forEach(select => {
                            applyBadgeStyle(select);
                        });
                    })
                    .catch(error => console.error('Error:', error));
            }

            // Trigger when filter dropdowns change
            form.querySelectorAll('select, input[name="pn_no"]').forEach(input => {
                input.addEventListener('change', triggerAjaxLoad);
                input.addEventListener('input', triggerAjaxLoad);
            });

            // Also trigger on general search box
            searchKeywordInput.addEventListener('input', triggerAjaxLoad);
        });

        $('#closedBulkUploadForm').on('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const fileInput = this.querySelector('input[type="file"]');
            const file = fileInput.files[0];

            if (!file || !file.name.endsWith('.xlsx')) {
                Swal.fire('Invalid File', 'Please upload a valid .xlsx file.', 'error');
                return;
            }

            $.ajax({
                url: "{{ route('closed_projects.upload') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $('#closedUploadBtn')
                        .prop('disabled', true)
                        .html('<span class="spinner-border spinner-border-sm"></span> Uploading...');
                },
                success: function(response) {
                    $('#closedUploadBtn').prop('disabled', false).html('Upload');
                    $('#uploadClosedModal').modal('hide');

                    Swal.fire('Success', 'Projects imported successfully!', 'success').then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    $('#closedUploadBtn').prop('disabled', false).html('Upload');
                    $('#uploadClosedModal').modal('hide');

                    let msg = 'Upload failed.';
                    if (xhr.responseJSON?.errors) {
                        msg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                    }
                    Swal.fire('Error', msg, 'error');
                }
            });
        });
    </script>
@endpush
