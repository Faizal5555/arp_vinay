<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Respondent Incentive Upload</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        body {
            background: url('{{ asset('assets/img/avatars/bg-pattern.png') }}') repeat;
            font-family: 'Segoe UI', sans-serif;
        }

        .logo {
            max-height: 80px;
            margin-bottom: 20px;
        }

        .upload-card {
            max-width: 600px;
            margin: 60px auto;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            background-color: white;
        }

        .form-label {
            font-weight: 600;
        }

        .upload-btn {
            background-color: #0061f2;
            color: white;
            font-weight: 500;
        }

        .upload-btn:hover {
            background-color: #004ec2;
        }

        .download-btn {
            font-size: 14px;
            font-weight: 500;
        }

        .download-btn i {
            margin-right: 6px;
        }

        .file-input {
            border: 1px solid #dcdfe6;
            padding: 10px;
            border-radius: 6px;
            background-color: #f9fafb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .file-input input[type="file"] {
            border: none;
            background: transparent;
        }

        .file-info {
            font-size: 14px;
            color: #555;
            margin-top: 6px;
        }
    </style>
</head>

<body>
    <div class="container text-center">
        <img src="{{ asset('assets/img/avatars/logo-4.png') }}" class="logo" alt="Company Logo">
        <h2 class="mb-4 fw-bold">Respondent Incentive Details Upload</h2>

        <div class="upload-card">
            <form id="uploadForm" enctype="multipart/form-data">
                @csrf

                <div class="mb-3 text-start">
                    <label class="form-label">Upload XLSX File</label>
                    <div class="file-input">
                        <input type="file" class="form-control" name="xlsx_file" id="xlsx_file" required>
                    </div>
                    <div class="file-info" id="fileName">No file chosen</div>
                </div>

                <div class="gap-2 mb-3 d-grid">
                    <button type="submit" class="btn upload-btn">Upload XLSX</button>
                </div>

                <div class="text-center">
                    <a href="{{ url('/incentive-form/sample') }}" class="btn btn-outline-secondary download-btn">
                        <i class="bi bi-download"></i>Download Sample XLSX
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Optional: Bootstrap Icons CDN for download icon -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <script>
        $(document).ready(function() {
            $('#xlsx_file').on('change', function() {
                const file = this.files[0];
                $('#fileName').text(file ? file.name : 'No file chosen');
            });

            $('#uploadForm').on('submit', function(e) {
                e.preventDefault();
                let formData = new FormData(this);

                $.ajax({
                    url: "{{ url('/incentive-form/upload') }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Upload Complete!',
                            text: 'Thank you. Redirecting...',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = "{{ url('/thank-you') }}";
                        });

                        $('#uploadForm')[0].reset();
                        $('#fileName').text('No file chosen');
                    },
                    error: function(xhr) {
                        let msg = 'Upload failed. Please check your file.';
                        if (xhr.responseJSON?.errors) {
                            msg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                        }
                        Swal.fire('Error', msg, 'error');
                    }
                });
            });
        });
    </script>
</body>

</html>
