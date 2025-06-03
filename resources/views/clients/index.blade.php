@extends('layouts.master')

@section('content')
    <div class="container mt-4">
        <button class="mb-3 btn btn-primary" style="background-color:#00326e; color:white;" data-bs-toggle="modal"
            data-bs-target="#clientModal">Add Client</button>


        <div class="mb-2 d-flex justify-content-end">
            <a href="{{ route('clients.download') }}" class="btn btn-primary" style="background-color:#00326e;">
                <i class="bx bx-download"></i> Download
            </a>

            <button class="btn btn-primary ms-2" data-bs-toggle="modal" data-bs-target="#uploadModal"
                style="background-color:#00326e;">
                <i class="bx bx-upload"></i> Upload
            </button>
        </div>


        <table class="table" id="clientTable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Country</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Whatsapp</th>
                    <th>Manager</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>

    <!-- Modal HTML -->
    <div class="modal fade" id="clientModal" tabindex="-1" aria-labelledby="clientModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="clientForm">
                @csrf
                <div class="modal-content">
                    <input type="hidden" name="client_id" id="client_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Client</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Client Name</label>
                            <input type="text" name="client_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Country</label>
                            <input type="text" name="client_country" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="client_email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Manager</label>
                            <input type="text" name="client_manager" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Phone Number</label>
                            <input type="text" name="client_phoneno" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>WhatsApp Number</label>
                            <input type="text" name="client_whatsapp" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-success" type="submit">Save Client</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="uploadForm" enctype="multipart/form-data" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-semibold">Upload Clients</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Download Sample File</label>
                        <div>
                            <a href="{{ route('clients.download.sample') }}" class="btn btn-sm btn-primary"
                                style="background-color:#00326e;">
                                <i class="bx bx-download"></i> Download Sample XLSX
                            </a>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Upload XLSX File</label>
                        <input type="file" name="bulk_file" class="form-control w-100" accept=".xlsx" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="clientUploadBtn" class="btn btn-primary" style="background-color:#00326e;">
                        <i class="bx bx-upload"></i> Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
<style>
    .is-invalid {
        border-color: #dc3545;
    }
</style>

@push('js')
    <script>
        // Submit Form
        $(document).ready(function() {
            // Initialize DataTable
            let table = $('#clientTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('clients.data') }}",
                columns: [{
                        data: 'client_name'
                    },
                    {
                        data: 'client_country'
                    },
                    {
                        data: 'client_email'
                    },
                    {
                        data: 'client_phoneno'
                    },
                    {
                        data: 'client_whatsapp'
                    },
                    {
                        data: 'client_manager'
                    },
                    {
                        data: 'id',
                        render: function(data, type, row) {
                            return `
                            <button class="btn btn-sm btn-warning editBtn" data-id="${data}">Edit</button>
                            <button class="btn btn-sm btn-danger deleteBtn" data-id="${data}">Delete</button>
                        `;
                        },
                        orderable: false
                    }
                ]
            });

            // Setup jQuery Validation
            $("#clientForm").validate({
                rules: {
                    client_name: {
                        required: true
                    },
                    client_country: {
                        required: true
                    },
                    client_email: {
                        required: true,
                        email: true
                    },
                    client_manager: {
                        required: true
                    },
                    client_phoneno: {
                        required: true,
                        number: true,
                        minlength: 9,
                        maxlength: 15
                    },
                    client_whatsapp: {
                        required: true,
                        number: true,
                        minlength: 9,
                        maxlength: 15
                    }
                },
                errorPlacement: function(error, element) {
                    if (element.hasClass("select2-hidden-accessible")) {
                        error.insertAfter(element.siblings('span.select2'));
                    } else if (element.hasClass("floating-input")) {
                        element.closest('.form-floating-label').addClass("error-cont")
                            .append(error);
                    } else {
                        error.insertAfter(element);
                    }
                },
                submitHandler: function(form) {
                    let formData = new FormData(form);
                    let clientId = $('#client_id').val();

                    // If client_id exists, it's an update. Otherwise, it's create.
                    let url = clientId ? `clients/${clientId}` : "{{ route('clients.store') }}";
                    let method = clientId ? 'POST' : 'POST';

                    // Simulate PUT for update
                    if (clientId) {
                        formData.append('_method', 'PUT');
                    }

                    $.ajax({
                        url: url,
                        type: method,
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(res) {
                            $('#clientModal').modal('hide');
                            $('#clientForm')[0].reset();
                            $('#client_id').val('');
                            table.ajax.reload();

                            if (res.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: res.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: res.message || "Something went wrong"
                                });
                            }
                        },
                        error: function(xhr) {
                            $('#clientModal').modal('hide');
                            if (xhr.responseJSON && xhr.responseJSON.error) {
                                let errors = xhr.responseJSON.error;
                                let messages = Object.values(errors).flat().join("\n");

                                Swal.fire({
                                    icon: 'error',
                                    title: 'Validation Error',
                                    text: messages
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Unexpected Error',
                                    text: "Something went wrong. Please try again."
                                });
                            }
                        }
                    });
                }
            });
        });


        $(document).on('click', '.editBtn', function() {
            let id = $(this).data('id');

            $.get('clients/' + id + '/edit', function(data) {
                $('#client_id').val(data.id);
                $('[name="client_name"]').val(data.client_name);
                $('[name="client_country"]').val(data.client_country);
                $('[name="client_email"]').val(data.client_email);
                $('[name="client_manager"]').val(data.client_manager);
                $('[name="client_phoneno"]').val(data.client_phoneno);
                $('[name="client_whatsapp"]').val(data.client_whatsapp);

                $('#clientModal').modal('show');
            });
        });


        $(document).on('click', '.deleteBtn', function() {
            let id = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: "This will permanently delete the client.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e3342f',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'clients/' + id,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            if (res.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: res.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    // Page reload after OK
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error!', 'Something went wrong.', 'error');
                            }
                        }
                    });
                }
            });
        });

        $('#uploadForm').on('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const fileInput = this.querySelector('input[type="file"]');
            const file = fileInput.files[0];

            if (!file || !file.name.endsWith('.xlsx')) {
                Swal.fire('Invalid File', 'Please upload a valid .xlsx file.', 'error');
                return;
            }

            $.ajax({
                url: "{{ route('clients.upload') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $('#clientUploadBtn')
                        .prop('disabled', true)
                        .html('<span class="spinner-border spinner-border-sm"></span> Uploading...');
                },
                success: function(response) {
                    $('#clientUploadBtn').prop('disabled', false).html('Upload');
                    $('#uploadModal').modal('hide');

                    if (response.success) {
                        Swal.fire('Success', response.message, 'success').then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    $('#clientUploadBtn').prop('disabled', false).html('Upload');
                    $('#uploadModal').modal('hide');

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
