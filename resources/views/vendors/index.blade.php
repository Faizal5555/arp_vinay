@extends('layouts.master')

@section('content')
    <div class="container mt-4">
        <button class="mb-3 btn btn-primary" style="background-color:#00326e; color:white;" data-bs-toggle="modal"
            data-bs-target="#vendorModal">Add Vendor</button>

        <table class="table" id="vendorTable">
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

    <!-- Vendor Modal -->
    <div class="modal fade" id="vendorModal" tabindex="-1" aria-labelledby="vendorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="vendorForm">
                @csrf
                <input type="hidden" id="vendor_id" name="vendor_id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add / Edit Vendor</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3"><label>Name</label><input type="text" name="vendor_name" class="form-control"
                                required></div>
                        <div class="mb-3"><label>Country</label><input type="text" name="vendor_country"
                                class="form-control" required></div>
                        <div class="mb-3"><label>Email</label><input type="email" name="vendor_email"
                                class="form-control" required></div>
                        <div class="mb-3"><label>Manager</label><input type="text" name="vendor_manager"
                                class="form-control" required></div>
                        <div class="mb-3"><label>Phone</label><input type="text" name="vendor_phoneno"
                                class="form-control" required></div>
                        <div class="mb-3"><label>WhatsApp</label><input type="text" name="vendor_whatsapp"
                                class="form-control" required></div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-success" type="submit">Save Vendor</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('js')
    <script>
        let vendorTable;

        $(document).ready(function() {
            vendorTable = $('#vendorTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('vendors.data') }}",
                columns: [{
                        data: 'vendor_name'
                    },
                    {
                        data: 'vendor_country'
                    },
                    {
                        data: 'vendor_email'
                    },
                    {
                        data: 'vendor_phoneno'
                    },
                    {
                        data: 'vendor_whatsapp'
                    },
                    {
                        data: 'vendor_manager'
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

            // jQuery Validation + Submit Handler
            $('#vendorForm').validate({
                rules: {
                    vendor_email: {
                        required: true,
                        email: true
                    },
                    vendor_phoneno: {
                        required: true,
                        number: true,
                        minlength: 9,
                        maxlength: 15
                    },
                    vendor_whatsapp: {
                        required: true,
                        number: true,
                        minlength: 9,
                        maxlength: 15
                    }
                },
                submitHandler: function(form) {
                    let formData = new FormData(form);
                    let id = $('#vendor_id').val();
                    let url = id ? `/vendors/${id}` : "{{ route('vendors.store') }}";

                    if (id) formData.append('_method', 'PUT');

                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(res) {
                            $('#vendorModal').modal('hide');
                            $('#vendorForm')[0].reset();
                            $('#vendor_id').val('');
                            vendorTable.ajax.reload();

                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: res.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        },
                        error: function(xhr) {
                            $('#vendorModal').modal('hide');
                            if (xhr.responseJSON?.error) {
                                let messages = Object.values(xhr.responseJSON.error).flat()
                                    .join("\n");
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Validation Error',
                                    text: messages
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Unexpected Error',
                                    text: "Something went wrong."
                                });
                            }
                        }
                    });
                }
            });

            // Edit vendor
            $(document).on('click', '.editBtn', function() {
                let id = $(this).data('id');
                $.get('/vendors/' + id + '/edit', function(data) {
                    $('#vendor_id').val(data.id);
                    $('[name="vendor_name"]').val(data.vendor_name);
                    $('[name="vendor_country"]').val(data.vendor_country);
                    $('[name="vendor_email"]').val(data.vendor_email);
                    $('[name="vendor_manager"]').val(data.vendor_manager);
                    $('[name="vendor_phoneno"]').val(data.vendor_phoneno);
                    $('[name="vendor_whatsapp"]').val(data.vendor_whatsapp);
                    $('#vendorModal').modal('show');
                });
            });

            // Delete vendor
            $(document).on('click', '.deleteBtn', function() {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This will permanently delete the vendor.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e3342f',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/vendors/' + id,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(res) {
                                Swal.fire('Deleted!', res.message, 'success').then(
                                    () => {
                                        location.reload();
                                    });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
