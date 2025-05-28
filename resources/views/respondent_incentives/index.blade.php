@extends('layouts.master')

@section('content')
    <div class="container mt-4">
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <h4>Respondent Incentive Details</h4>
            <button class="btn btn-primary" style="background-color:#00326e; color:white;" data-bs-toggle="modal"
                data-bs-target="#addModal">Add Details</button>
        </div>

        <form method="GET" action="{{ route('respondent.index') }}" class="mb-4 row g-3 align-items-center">

            <div class="col-md-3">
                <label for="country_id" class="form-label">Country</label>
                <select name="country_id" id="country_id" class="form-select">
                    <option value="">All Countries</option>
                    @foreach ($countries as $country)
                        <option value="{{ $country->id }}" {{ request('country_id') == $country->id ? 'selected' : '' }}>
                            {{ $country->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label for="speciality" class="form-label">Speciality</label>
                <select name="speciality" id="speciality" class="form-select">
                    <option value="">All Specialities</option>
                    @foreach ($specialities as $sp)
                        <option value="{{ $sp }}" {{ request('speciality') == $sp ? 'selected' : '' }}>
                            {{ $sp }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label for="date_range" class="form-label">Date Range</label>
                <input type="text" name="date_range" id="date_range" class="form-control" placeholder="Select date range"
                    value="{{ request('date_range') }}">
            </div>

            <div class="pt-4 col-md-2">
                <button class="mt-2 btn btn-primary" type="submit" style="background-color:#00326e; color:white;">
                    Filter
                </button>
            </div>

        </form>


        <div class="gap-2 d-flex justify-content-end">
            <form id="downloadForm" method="GET" action="{{ route('respondent.download') }}"
                class="d-flex align-items-center">
                <input type="hidden" name="date_range" id="export_date_range">
                <button type="submit" class="btn btn-success" style="background-color:#00326e; color:white;">
                    <i class="bx bx-download"></i> Download
                </button>
            </form>

            <button type="button" class="mb-3 btn btn-primary" style="background-color:#00326e; color:white;"
                onclick="copyIncentiveUrl()">
                <i class="bx bx-copy"></i> Copy URL
            </button>
        </div>



        <div class="px-2 card table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>PN No</th>
                        <th>Respondent Name</th>
                        <th>Email ID</th>
                        <th>Contact Number</th>
                        <th>Speciality</th>
                        <th>Country</th>
                        <th>Incentive Amount</th>
                        <th>Payment Currency</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Payment Date</th>
                        <th>Payment Type</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="recordList">
                    @foreach ($records as $r)
                        <tr>
                            <td>{{ $r->date ? \Carbon\Carbon::parse($r->date)->format('d-m-Y') : '-' }}</td>
                            <td>{{ $r->pn_no }}</td>
                            <td>{{ $r->respondent_name }}</td>
                            <td>{{ $r->email_id }}</td>
                            <td>{{ $r->contact_number }}</td>
                            <td>{{ $r->speciality }}</td>
                            <td>{{ $r->country ? $r->country->name : '-' }}</td>
                            <td>{{ $r->incentive_amount }}</td>
                            <td>{{ $r->incentive_form }}</td>
                            <td>{{ $r->start_date }}</td>
                            <td>{{ $r->end_date }}</td>
                            <td>{{ $r->payment_date ? \Carbon\Carbon::parse($r->date)->format('d-m-Y') : '-' }}</td>
                            <td>{{ $r->payment_type }}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-warning editBtn" data-id="{{ $r->id }}"
                                    data-pn_no="{{ $r->pn_no }}" data-respondent_name="{{ $r->respondent_name }}"
                                    data-email_id="{{ $r->email_id }}" data-contact_number="{{ $r->contact_number }}"
                                    data-speciality="{{ $r->speciality }}"
                                    data-incentive_amount="{{ $r->incentive_amount }}"
                                    data-incentive_form="{{ $r->incentive_form }}" data-start_date="{{ $r->start_date }}"
                                    data-end_date="{{ $r->end_date }}" data-payment_date="{{ $r->payment_date }}"
                                    data-payment_type="{{ $r->payment_type }}" data-country_id="{{ $r->country_id }}"
                                    data-speciality="{{ $r->speciality }}" data-bs-toggle="modal"
                                    data-bs-target="#addModal">
                                    Edit
                                </button>
                                <button class="btn btn-danger btn-sm deleteBtn"
                                    data-id="{{ $r->id }}">Delete</button>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <!-- Modal -->
    <!-- Incentive Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="addForm" class="modal-content">
                @csrf
                <input type="hidden" name="record_id" id="record_id">

                <div class="text-white modal-header">
                    <h5 class="modal-title" id="modalTitle">Add Incentive Detail</h5>
                    <button type="button" class="btn-close " data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="todayDate" class="form-label">Date</label>
                            <input type="text" name="date" id="todayDate" class="form-control" readonly
                                placeholder="Date">
                        </div>

                        <div class="col-md-6">
                            <label for="pn_no" class="form-label">PN No</label>
                            <input type="text" name="pn_no" id="pn_no" class="form-control"
                                placeholder="PN No" required>
                        </div>

                        <div class="col-md-6">
                            <label for="respondent_name" class="form-label">Respondent Name</label>
                            <input type="text" name="respondent_name" id="respondent_name" class="form-control"
                                placeholder="Respondent Name" required>
                        </div>

                        <div class="col-md-6">
                            <label for="email_id" class="form-label">Email ID</label>
                            <input type="email" name="email_id" id="email_id" class="form-control"
                                placeholder="Email ID" required>
                        </div>

                        <div class="col-md-6">
                            <label for="contact_number" class="form-label">Contact Number</label>
                            <input type="text" name="contact_number" id="contact_number" class="form-control"
                                placeholder="Contact Number" required>
                        </div>

                        <div class="col-md-6">
                            <label for="speciality" class="form-label">Speciality</label>
                            <select name="speciality" id="speciality" class="form-select" required>
                                <option value="">Select Speciality</option>
                                @foreach ($specialities as $sp)
                                    <option value="{{ $sp }}">{{ $sp }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="country_id" class="form-label">Country</label>
                            <select name="country_id" id="country_id" class="form-select" required>
                                <option value="">Select Country</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="incentive_amount" class="form-label">Incentive Amount</label>
                            <input type="number" name="incentive_amount" id="incentive_amount" class="form-control"
                                placeholder="Incentive Amount" required>
                        </div>

                        <div class="col-md-6">
                            <label for="incentive_form" class="form-label">Payment Currency</label>
                            <input type="text" name="incentive_form" id="incentive_form" class="form-control"
                                placeholder="Payment Currency" required>
                        </div>

                        <div class="col-md-6">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="payment_date" class="form-label">Payment Date</label>
                            <input type="date" name="payment_date" id="payment_date" class="form-control"
                                placeholder="payment date">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Payment Type</label>
                            <select name="payment_type" id="payment_type" class="form-select" required>
                                <option value="">Select Type</option>
                                <option value="Cash">Cash</option>
                                <option value="PayPal">PayPal</option>
                                <option value="GiftVoucher">Gift Voucher</option>
                                <option value="BankTransfer">Bank Transfer</option>
                                <option value="Check">Check</option>
                                <option value="Credit">Credit</option>
                                <option value="Wise">Wise</option>
                                <option value="Others">Others</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" style="background-color:#00326e; color:white;"
                        id="submitBtn">Add Record</button>
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


    input.form-control,
    select.form-select {
        width: 100%;
        /* ✅ responsive and fills col-md-6 */
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

    .dataTables_filter {
        margin: 10px !important;
    }

    .dataTables_length {
        margin: 15px !important;
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
        let dataTable;

        $(document).ready(function() {
            dataTable = $('table').DataTable(); // Initialize once
            const today = new Date().toISOString().split('T')[0]; // yyyy-mm-dd
            $('#todayDate').val(today);

            // Submit form via AJAX
            $('#addForm').on('submit', function(e) {
                e.preventDefault();
                const form = this;
                const formData = new FormData(form);

                fetch("{{ route('respondent.store') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Success', data.message, 'success');

                            // Reset form and close modal
                            form.reset();
                            $('#addModal').modal('hide');

                            // Reload just the row list via AJAX (or re-fetch if needed)
                            // For now, simply refresh the page section
                            setTimeout(() => window.location.reload(), 1000); // Optional fallback
                        } else {
                            Swal.fire('Error', 'Something went wrong', 'error');
                            $('#addModal').modal('hide');
                        }
                    })
                    .catch(() => Swal.fire('Error', 'Unexpected error', 'error'));
                $('#addModal').modal('hide');
            });

            // Edit button pre-fill
            $('.editBtn').on('click', function() {
                const modal = $('#addModal');
                $('#modalTitle').text('Edit Incentive Detail');
                $('#submitBtn').text('Update Record');
                modal.find('[name="record_id"]').val($(this).data('id'));
                modal.find('[name="pn_no"]').val($(this).data('pn_no'));
                modal.find('[name="respondent_name"]').val($(this).data('respondent_name'));
                modal.find('[name="email_id"]').val($(this).data('email_id'));
                modal.find('[name="contact_number"]').val($(this).data('contact_number'));
                modal.find('[name="speciality"]').val($(this).data('speciality'));
                modal.find('[name="incentive_amount"]').val($(this).data('incentive_amount'));
                modal.find('[name="incentive_form"]').val($(this).data('incentive_form'));
                modal.find('[name="start_date"]').val($(this).data('start_date'));
                modal.find('[name="end_date"]').val($(this).data('end_date'));
                modal.find('[name="payment_date"]').val($(this).data('payment_date'));
                modal.find('[name="payment_type"]').val($(this).data('payment_type')).trigger('change');
                modal.find('[name="country_id"]').val($(this).data('country_id')).trigger('change');
                modal.find('[name="speciality"]').val($(this).data('speciality')).trigger('change'); // 
            });

            // Delete with confirmation
            $('.deleteBtn').on('click', function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This record will be deleted!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch("{{ route('respondent.destroy', ':id') }}".replace(':id', id), {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire('Deleted!', data.message, 'success');

                                    // Remove row from DataTable
                                    const row = $(`button[data-id="${id}"]`).closest('tr');
                                    dataTable.row(row).remove().draw();
                                } else {
                                    Swal.fire('Error', 'Delete failed', 'error');
                                }
                            });
                    }
                });
            });
        });

        $(function() {
            $('#date_range').val(''); // Clear input on reload

            $('#date_range').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear'
                },
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'This Year': [moment().startOf('year'), moment().endOf('year')]
                }
            });

            $('#date_range').on('apply.daterangepicker', function(ev, picker) {
                const value = picker.startDate.format('YYYY-MM-DD') + ' to ' + picker.endDate.format(
                    'YYYY-MM-DD');
                $(this).val(value);
                $('#export_date_range').val(value); // sync to export form
            });

            $('#date_range').on('cancel.daterangepicker', function() {
                $(this).val('');
                $('#export_date_range').val(''); // clear in export form too
            });
        });
        $('#exportForm').on('submit', function() {
            $('#export_date_range').val($('#date_range').val());
        });

        function copyIncentiveUrl() {
            const url = "{{ url('/incentive-form') }}";
            navigator.clipboard.writeText(url).then(() => {
                Swal.fire('Copied!', 'URL copied to clipboard.', 'success');
            }).catch(() => {
                Swal.fire('Failed', 'Could not copy URL.', 'error');
            });
        }
    </script>
@endpush
