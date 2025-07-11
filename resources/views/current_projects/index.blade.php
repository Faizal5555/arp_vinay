@extends('layouts.master')

@section('content')
    <div class="container mt-4">
        <div class="mb-4 border-0 shadow-sm card bg-light">
            <div class="card-body d-flex align-items-center justify-content-between">
                <h4 class="mb-0 fw-bold text-dark">
                    <i class="bx bx-briefcase me-2 text-dark"></i> Current Projects
                </h4>
            </div>
        </div>
        <div class="gap-2 mb-3 d-flex justify-content-end">
            <a href="{{ route('current_projects.download') }}" class="btn btn-primary" style="background-color:#00326e;">
                <i class="bx bx-download"></i> Download
            </a>

            <button type="button" class="btn" style="background-color:#00326e; color:white;" data-bs-toggle="modal"
                data-bs-target="#uploadModal">
                <i class="bx bx-upload"></i> Upload
            </button>
        </div>

        <form id="projectForm">
            @csrf
            <div class="table-responsive">
                <table class="table text-center align-middle table-bordered table-hover" id="projectTable">
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
                            <th>Actual Currency Amount</th>
                            <th>Original Revenue</th>
                            <th>Margin</th>
                            <th>Final Invoice Amount</th>
                            <th>Comments</th>
                            <th>Supplier Name</th>
                            <th>Supplier Payment Details</th>
                            <th>Total Incentives Paid</th>
                            <th>Incentive Paid Date</th>
                            <th>Invoice No</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="projectRows">
                        @php
                            $filledCount = count($projects);
                            $blankCount = max(10 - $filledCount, 0);
                        @endphp

                        @foreach ($projects as $project)
                            <tr>
                                @include('current_projects.row', [
                                    'clients' => $clients,
                                    'project' => $project,
                                ])
                            </tr>
                        @endforeach

                        @for ($i = 0; $i < $blankCount; $i++)
                            <tr>
                                @include('current_projects.row', [
                                    'clients' => $clients,
                                    'project' => null,
                                ])
                            </tr>
                        @endfor

                    </tbody>

                    <tfoot>
                        <tr>
                            <td colspan="9" class="text-end fw-bold text-dark">Totals:</td>
                            <td id="originalRevenueTotal" name="original_revenue_total[]" class="text-black text-start"
                                style="background: yellow; color:#212529; font-weight:bold;">0.00
                            </td>
                            <td></td>
                            <td id="finalInvoiceTotal" name="invoice_amount_total[]" class="text-black fw-bold text-start"
                                style="background: yellow; color:#212529; font-weight:bold;">0.00
                            </td>
                            <td colspan="3"></td>
                            <td id="incentiveTotal" name="incentives_paid_total[]" class="text-black fw-bold text-start"
                                style="background: yellow; color:#212529; font-weight:bold;">0.00
                            </td>
                            <td colspan="4"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="text-end">
                <button type="button" class="btn btn-outline-primary" id="addRowBtn">Add More</button>
                <button type="submit" class="btn btn-success">Submit All</button>
            </div>
        </form>
    </div>

    <template id="projectRowTemplate">
        <tr>
            @include('current_projects.row', ['clients' => $clients, 'project' => null])
            <td><button type="button" class="btn btn-danger deleteRow">Remove</button></td>
        </tr>
    </template>
    <div class="modal fade" id="moveModal" tabindex="-1" aria-labelledby="moveModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Move</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body col-md-12">
                    <label class="mb-2 fw-bold">Select Move Type</label>
                    <select id="moveType" class="form-select w-100">
                        <option value="Open_Last_Quarter">✓ Open Project from Last Quarter</option>
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


    <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="bulkUploadForm" enctype="multipart/form-data" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-semibold" id="uploadModalLabel">Upload Projects</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="row">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Download Sample File</label>
                            <div>
                                <a href="{{ route('current_projects.sample') }}" class="btn btn-sm"
                                    style="background-color:#00326e; color:white;>
                                <i class="bx bx-download"></i> Download Sample XLSX
                                </a>
                            </div>
                        </div>

                        <div class="mb-3 col-md-12">
                            <label clsass="form-label fw-bold">Upload XLSX File</label>
                            <input type="file" class="w-100 form-control" name="bulk_file" accept=".xlsx" required>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" style="background-color:#00326e; color:white;"
                        id="project_upload">
                        <i class="bx bx-upload"></i>
                        Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
<style>
    #moveModal .modal-dialog {
        max-width: 380px;
        /* Adjust as needed */
        margin: 1.75rem auto;
    }

    #moveModal .modal-body select {
        font-weight: 600;
    }

    #moveType option {
        font-weight: 600;
    }
</style>

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function calculateTotals() {
            let original = 0,
                final = 0,
                incentive = 0;
            document.querySelectorAll('[name="original_revenue[]"]').forEach(input => {
                let val = parseFloat(input.value) || 0;
                original += val;
            });
            document.querySelectorAll('[name="final_invoice_amount[]"]').forEach(input => {
                let val = parseFloat(input.value) || 0;
                final += val;
            });
            document.querySelectorAll('[name="total_incentives_paid[]"]').forEach(input => {
                let val = parseFloat(input.value) || 0;
                incentive += val;
            });
            document.getElementById('originalRevenueTotal').textContent = original.toFixed(2);
            document.getElementById('finalInvoiceTotal').textContent = final.toFixed(2);
            document.getElementById('incentiveTotal').textContent = incentive.toFixed(2);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('projectForm');
            const rowContainer = document.getElementById('projectRows');
            const rowTemplate = document.getElementById('projectRowTemplate').innerHTML;

            document.getElementById('addRowBtn').addEventListener('click', function() {
                for (let i = 0; i < 10; i++) {
                    const newRow = document.createElement('tr');
                    newRow.innerHTML = rowTemplate;
                    rowContainer.appendChild(newRow);
                }

                // Only fill date for empty new rows
                const today = new Date().toISOString().split('T')[0];
                document.querySelectorAll('#projectRows tr').forEach(row => {
                    const dateInput = row.querySelector('.entry-date');
                    const fyInput = row.querySelector('[name="fy[]"]');

                    // If it's a newly added row (no FY or any content), set date
                    if (dateInput && !dateInput.value && fyInput && !fyInput.value.trim()) {
                        dateInput.value = today;
                    }
                });
            });


            rowContainer.addEventListener('input', function() {
                calculateTotals();
            });

            rowContainer.addEventListener('click', function(e) {
                if (e.target.classList.contains('addRow')) {
                    const newRow = document.createElement('tr');
                    newRow.innerHTML = rowTemplate;
                    rowContainer.appendChild(newRow);
                }
                if (e.target.classList.contains('deleteRow')) {
                    e.target.closest('tr').remove();
                    calculateTotals();
                }
            });

            const storeUrl = "{{ route('current_projects.store') }}";
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const rows = rowContainer.querySelectorAll('tr');
                const projects = [];

                rows.forEach(row => {
                    const inputs = row.querySelectorAll('[name]');
                    const data = {};
                    inputs.forEach(input => {
                        const name = input.name.replace('[]', '');
                        data[name] = input.value;
                    });
                    if (Object.values(data).some(v => v.trim() !== '')) {
                        projects.push(data);
                    }

                });

                fetch(storeUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            projects,
                            original_revenue_total: document.getElementById(
                                'originalRevenueTotal').textContent,
                            invoice_amount_total: document.getElementById('finalInvoiceTotal')
                                .textContent,
                            incentives_paid_total: document.getElementById('incentiveTotal')
                                .textContent
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Success', data.message, 'success').then(() => location
                                .reload());
                        } else {
                            Swal.fire('Validation Failed', JSON.stringify(data.errors), 'error');
                        }
                    })
                    .catch(err => Swal.fire('Error', 'Something went wrong.', 'error'));
            });

            calculateTotals();
        });


        let selectedProjectRow = null;

        document.addEventListener('DOMContentLoaded', function() {
            const modalMessage = document.getElementById('moveModalMessage');
            const moveModal = new bootstrap.Modal(document.getElementById('moveModal'));
            const moveTypeSelect = document.getElementById('moveType');
            let selectedProjectRow = null;

            // Enhance dropdown: only selected option gets ✓
            moveTypeSelect.addEventListener('change', function() {
                const options = this.querySelectorAll('option');
                options.forEach(opt => {
                    const cleanText = opt.textContent.replace('✓', '').trim();
                    opt.textContent = cleanText;
                });

                const selected = this.selectedOptions[0];
                selected.textContent = '✓ ' + selected.textContent;
            });

            // Handle row selection for move
            document.querySelectorAll('.moveRow').forEach(button => {
                button.addEventListener('click', function() {
                    const row = this.closest('tr');
                    const fy = row.querySelector('[name="fy[]"]').value;

                    if (!fy) {
                        Swal.fire('Warning', 'Please enter PN No to identify the record.',
                            'warning');
                        return;
                    }

                    selectedProjectRow = row;
                    moveTypeSelect.selectedIndex = 0; // Reset dropdown to default
                    moveTypeSelect.dispatchEvent(new Event('change')); // Force checkmark update
                    moveModal.show();
                });
            });

            // Handle move confirmation
            document.getElementById('confirmMoveBtn').addEventListener('click', function() {
                if (!selectedProjectRow) return;

                const inputs = selectedProjectRow.querySelectorAll('[name]');
                const data = {};

                inputs.forEach(input => {
                    const key = input.name.replace('[]', '');
                    data[key] = input.value;
                });

                data.invoice_status = moveTypeSelect.value; // ✅ set status from dropdown

                fetch("{{ route('pending_projects.store') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(data)
                    })
                    .then(res => res.json())
                    .then(response => {
                        if (response.success) {
                            selectedProjectRow.remove();
                            $('#moveModal').modal('hide');

                            // Delete from current_projects
                            fetch(`{{ route('projects.deleteByPn') }}`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'input[name="_token"]').value,
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    id: data.id
                                })
                            });

                            Swal.fire('Moved', response.message, 'success').then(() => location
                                .reload());
                        } else {
                            $('#moveModal').modal('hide');
                            Swal.fire('Error', response.message || 'Something went wrong.', 'error');
                        }
                    })
                    .catch(err => Swal.fire('Error', 'Something went wrong.', 'error'));
            });
        });


        $('#bulkUploadForm').on('submit', function(e) {
            e.preventDefault();

            const fileInput = this.querySelector('input[type="file"]');
            const file = fileInput.files[0];

            if (!file || !file.name.endsWith('.xlsx')) {
                Swal.fire('Invalid File', 'Please upload a valid .xlsx file.', 'error');
                return;
            }

            const formData = new FormData(this);

            $.ajax({
                url: "{{ route('current_projects.bulk_upload') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').val()
                },
                beforeSend: function() {
                    $('#project_upload')
                        .prop('disabled', true)
                        .html('<span class="spinner-border spinner-border-sm"></span> Uploading...');
                },
                success: function(response) {
                    $('#project_upload')
                        .prop('disabled', false)
                        .html('Upload');

                    $('#uploadModal').modal('hide');

                    if (response.success) {
                        Swal.fire('Success', response.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Error', response.message || 'Upload failed.', 'error');
                    }
                },
                error: function(xhr) {
                    $('#project_upload')
                        .prop('disabled', false)
                        .html('Upload');

                    $('#uploadModal').modal('hide');
                    let msg = 'Upload failed.';
                    if (xhr.responseJSON?.errors) {
                        msg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                    }
                    Swal.fire('Error', msg, 'error');
                }
            });
        });

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('deleteRow')) {
                let id = e.target.dataset.id;

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This will permanently delete the project!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e3342f',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // ✅ Only after confirm
                        fetch(`{{ route('current_projects.deleteById') }}`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    id: id
                                })
                            })
                            .then(res => res.json())
                            .then(response => {
                                if (response.success) {
                                    Swal.fire('Deleted!', response.message, 'success')
                                        .then(() => {
                                            location
                                        .reload(); // ✅ Reload the page after successful delete
                                        });
                                } else {
                                    Swal.fire('Error', response.message || 'Something went wrong.',
                                        'error');
                                }
                            })
                            .catch(err => {
                                Swal.fire('Error', 'Something went wrong.', 'error');
                            });
                    }
                    // No action on cancel
                });
            }
        });
    </script>
@endpush
