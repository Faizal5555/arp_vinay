@extends('layouts.master')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="container mt-5">
        <h4 class="mb-4 text-dark fw-bold">Important Communication</h4>

        {{-- Communication Form --}}
        <div class="mb-4 col-md-11 position-relative">
            <i class="bx bx-search position-absolute top-50 start-0 translate-middle-y ps-3 text-muted fs-5"></i>
            <input type="text" id="searchKeyword" class="shadow-sm form-control ps-5 rounded-4" style="height:50px;"
                placeholder="Search by subject or message...">
        </div>

        <div id="searchResults"></div>
        <form id="communicationForm">
            @csrf
            <div id="communicationWrapper">
                {{-- Show existing records --}}
                @foreach ($communications as $i => $item)
                    <div class="mb-4 row communication-item align-items-end">
                        <input type="hidden" class="record-id" name="communications[{{ $i }}][id]"
                            value="{{ $item->id }}">
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Subject</label>
                            <textarea class="shadow-sm form-control rounded-3" rows="3" name="communications[{{ $i }}][subject]">{{ $item->subject }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Communication</label>
                            <div class="gap-2 d-flex align-items-start">
                                <textarea class="shadow-sm form-control rounded-3" rows="3" name="communications[{{ $i }}][message]">{{ $item->message }}</textarea>
                                @if ($loop->last)
                                    <button type="button"
                                        class="mt-4 shadow-sm btn btn-sm btn-success addMore rounded-circle"
                                        style="width: 35px; height: 35px;">+</button>
                                @else
                                    <button type="button"
                                        class="mt-4 shadow-sm btn btn-sm btn-danger removeItem rounded-circle"
                                        style="width: 35px; height: 35px;">-</button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach


                {{-- If no record exists, show one empty row --}}
                @if ($communications->isEmpty())
                    <div class="mb-4 row communication-item align-items-end">
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Subject</label>
                            <textarea class="shadow-sm form-control rounded-3" rows="3" name="communications[0][subject]"
                                placeholder="Enter subject..."></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Communication</label>
                            <div class="gap-2 d-flex align-items-start">
                                <textarea class="shadow-sm form-control rounded-3" rows="3" name="communications[0][message]"
                                    placeholder="Enter communication..."></textarea>
                                <button type="button" class="mt-4 shadow-sm btn btn-sm btn-success addMore rounded-circle"
                                    style="width: 35px; height: 35px;">+</button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="text-center">
                <button type="submit" class="px-4 py-2 btn btn-primary">Save</button>
            </div>
        </form>
    </div>
@endsection
@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let index = {{ $communications->count() ?: 1 }};

        // Setup CSRF for AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Add new text area group
        $(document).on('click', '.addMore', function() {
            const newItem = `
        <div class="mb-4 row communication-item align-items-end">
            <div class="col-md-5">
                <label class="form-label fw-semibold">Subject</label>
                <textarea class="shadow-sm form-control rounded-3" rows="3" name="communications[${index}][subject]" placeholder="Enter subject..."></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Communication</label>
                <div class="gap-2 d-flex align-items-start">
                    <textarea class="shadow-sm form-control rounded-3" rows="3" name="communications[${index}][message]" placeholder="Enter communication..."></textarea>
                    <button type="button" class="mt-4 shadow-sm btn btn-sm btn-danger removeItem rounded-circle" style="width: 35px; height: 35px;">-</button>
                </div>
            </div>
        </div>`;
            $('#communicationWrapper').append(newItem);
            index++;
        });

        // Remove communication item
        // $(document).on('click', '.removeItem', function() {
        //     $(this).closest('.communication-item').remove();
        // });

        // Submit form via AJAX
        $('#communicationForm').submit(function(e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('communication.store') }}",
                method: "POST",
                data: $(this).serialize(),
                success: function(response) {
                    Swal.fire('Success', response.success, 'success').then(() => {
                        location.reload();
                    });
                },
                error: function() {
                    Swal.fire('Error', 'Failed to save communications.', 'error');
                }
            });
        });


        $(document).on('click', '.removeItem', function() {
            const row = $(this).closest('.communication-item');
            const recordId = row.find('.record-id').val();

            if (recordId) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This communication will be deleted.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        $.ajax({
                            url: "{{ route('communication.destroy', ':id') }}".replace(':id',
                                recordId),
                            type: 'DELETE',
                            success: function(res) {
                                Swal.fire('Deleted!', res.success, 'success');
                                row.remove();
                            },
                            error: function() {
                                Swal.fire('Error', 'Failed to delete communication.', 'error');
                            }
                        });
                    }
                });
            } else {
                row.remove();
            }
        });


        // Live Search
        // Live Search
        $('#searchKeyword').on('input', function() {
            const keyword = $(this).val().trim();

            // Hide wrapper if typing
            if (keyword.length > 0) {
                $('#communicationForm').fadeOut(100);

                $.ajax({
                    url: "{{ route('communication.search') }}",
                    method: "GET",
                    data: {
                        keyword: keyword
                    },
                    success: function(response) {
                        const currentKeyword = $('#searchKeyword').val().trim();

                        // Ensure search box is still filled when AJAX completes
                        if (currentKeyword.length === 0) {
                            $('#searchResults').empty().hide();
                            $('#communicationWrapper').fadeIn(150);
                            return;
                        }

                        let resultHtml =
                            `<div class="mb-3 fw-semibold text-muted">Search Results</div>`;
                        if (response.length > 0) {
                            response.forEach((item) => {
                                resultHtml += `
                        <div class="p-3 mb-3 border rounded col-md-11 bg-light">
                            <p><strong>Subject:</strong> <strong>${item.subject}</strong></p>
                            <p><strong>Message:</strong> <strong>${item.message}</strong></p>
                        </div>`;
                            });
                        } else {
                            resultHtml =
                                `<div class="alert alert-warning">No results found for "<strong>${currentKeyword}</strong>".</div>`;
                        }

                        $('#searchResults').html(resultHtml).fadeIn(150);
                    },
                    error: function() {
                        $('#searchResults').html(
                            '<div class="alert alert-danger">Search failed. Please try again.</div>'
                        ).fadeIn(150);
                    }
                });

            } else {
                // Fully cleared manually or via backspace: reset state
                $('#searchResults').fadeOut(100, function() {
                    $(this).empty().show();
                });
                $('#communicationForm').fadeIn(150);
            }
        });
    </script>
@endpush
