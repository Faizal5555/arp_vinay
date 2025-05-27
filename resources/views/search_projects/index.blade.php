@extends('layouts.master')

@section('content')
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="container mt-4">
        <div class="mb-4 border-0 shadow-sm card">
            <div class="card-body">
                <form id="filterForm" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label>FY</label>
                        <select name="fy" class="form-select">
                            <option value="">-- Select FY --</option>
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
                            <option value="">-- Select Quarter --</option>
                            @foreach (['Q1', 'Q2', 'Q3', 'Q4'] as $q)
                                <option value="{{ $q }}">{{ $q }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label>Client</label>
                        <select name="client_id" class="form-select">
                            <option value="">-- Select Client --</option>
                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->client_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Company</label>
                        <select name="company_name" class="form-select">
                            <option value="">-- Select Company --</option>
                            <option value="ARP">ARP</option>
                            <option value="HPI">HPI</option>
                            <option value="URP">URP</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>PN No</label>
                        <input type="text" name="pn_no" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>Supplier Name</label>
                        <input type="text" name="supplier_name" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>Status</label>
                        <select name="invoice_status" class="form-select">
                            <option value="">-- Status --</option>
                            <option value="Pending">Pending</option>
                            <option value="Paid">Paid</option>
                            <option value="waveoff">Waveoff</option>
                            <option value="partial">Partial payment</option>
                        </select>
                    </div>
                    {{-- <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Search</button>
                    </div> --}}
                </form>
            </div>
        </div>
        <div class="d-flex justify-content-end">
            <form method="GET" action="{{ route('search.projects.download') }}" id="exportForm">
                <input type="hidden" name="fy">
                <input type="hidden" name="quarter">
                <input type="hidden" name="client_id">
                <input type="hidden" name="company_name">
                <input type="hidden" name="pn_no">
                <input type="hidden" name="supplier_name">
                <input type="hidden" name="invoice_status">
                <button type="submit" class="mb-3 btn btn-success float-end" style="background-color:#00326e;">
                    <i class="bx bx-download"></i> Download
                </button>
            </form>
        </div>

        <div id="resultSection">
            @include('search_projects.results', ['projects' => $projects])
        </div>
    </div>
@endsection

@push('js')
    <script>
        const form = document.getElementById('filterForm');

        function triggerSearch() {
            const formData = new FormData(form);

            fetch("{{ route('search.projects.ajax') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    document.getElementById('resultSection').innerHTML = data.html;
                });
        }

        // Attach input and select change listeners
        form.querySelectorAll('input, select').forEach(el => {
            el.addEventListener('input', () => {
                document.querySelector(`#exportForm [name="${el.name}"]`).value = el.value;
                triggerSearch();
            });
            el.addEventListener('change', () => {
                document.querySelector(`#exportForm [name="${el.name}"]`).value = el.value;
                triggerSearch();
            });
        });
    </script>
@endpush
