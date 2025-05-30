@extends('layouts.master')

@section('content')
    <style>
        /* Custom Header Design */
        .dashboard-header {
            background: linear-gradient(135deg, #00326e, #005f99);
            color: white;
            border-radius: 12px;
            padding: 2rem 2rem;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .dashboard-header h2 {
            font-weight: 700;
            color: white;
        }

        .dashboard-header p {
            font-size: 1rem;
            opacity: 0.9;
        }

        .profile-pic {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #fff;
        }

        /* Card Enhancements */
        .custom-card {
            transition: all 0.3s ease-in-out;
            border-radius: 12px;
        }

        .custom-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
        }

        .card-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        th {
            color: white !important;
            background-color: #00326e;
            /* Optional if all headers share this bg */
        }

        .custom-card {
            border-radius: 12px;
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            background-color: #ffffff;
        }

        .custom-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        }

        .card-icon {
            padding: 0.75rem;
            border-radius: 50%;
            background-color: #f4f6f9;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
    </style>

    <div class="mt-4 container-fluid">

        <!-- Welcome Section -->
        <div class="mb-4 row">
            <div class="col-md-12">
                <div class="flex-wrap dashboard-header d-flex align-items-center justify-content-between">
                    <div>
                        <h2>Welcome, Vinay</h2>
                        <p>Dashboard</p>
                    </div>
                    <div>
                        <img src="{{ asset('assets/img/avatars/Vinay.png') }}" alt="Profile" class="profile-pic">
                    </div>
                </div>
            </div>
        </div>

        <!-- Cards Section -->

        <form method="GET" action="{{ route('dashboard') }}" class="mb-4 row g-3 align-items-end">
            <div class="col-md-2">
                <label class="form-label fw-semibold">FY</label>
                <select name="fy" class="form-select">
                    <option value="">-- Select FY --</option>
                    @for ($i = 10; $i <= 50; $i++)
                        @php
                            $fyLabel = 'FY ' . $i . '-' . sprintf('%02d', $i + 1);
                        @endphp
                        <option value="{{ $fyLabel }}" {{ request('fy') == $fyLabel ? 'selected' : '' }}>
                            {{ $fyLabel }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-semibold">FY & Quarter</label>
                <select name="quarter" class="form-select">
                    <option value="">-- Select FY & Quarter --</option>
                    @for ($i = 10; $i <= 50; $i++)
                        @php
                            $fy = 'FY ' . $i . '-' . sprintf('%02d', $i + 1);
                        @endphp
                        @foreach (['Q1', 'Q2', 'Q3', 'Q4'] as $q)
                            @php
                                $combined = $fy . ' & ' . $q;
                            @endphp
                            <option value="{{ $combined }}" {{ request('quarter') == $combined ? 'selected' : '' }}>
                                {{ $combined }}
                            </option>
                        @endforeach
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">From FY</label>
                <select name="fy_from" class="form-select">
                    <option value="">-- From FY --</option>
                    @for ($i = 10; $i <= 50; $i++)
                        @php
                            $fromFyLabel = 'FY ' . $i . '-' . sprintf('%02d', $i + 1);
                        @endphp
                        <option value="{{ $fromFyLabel }}" {{ request('fy_from') == $fromFyLabel ? 'selected' : '' }}>
                            {{ $fromFyLabel }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label fw-semibold">To FY</label>
                <select name="fy_to" class="form-select">
                    <option value="">-- To FY --</option>
                    @for ($i = 10; $i <= 50; $i++)
                        @php $fy = 'FY ' . $i . '-' . sprintf('%02d', $i + 1); @endphp
                        <option value="{{ $fy }}" {{ request('fy_to') == $fy ? 'selected' : '' }}>
                            {{ $fy }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3 d-grid">
                <button type="submit" class="btn btn-success"><i class="bx bx-search"></i> Filter Projects</button>
            </div>
        </form>


        <div class="row">
            @php
                $stats = [
                    [
                        'title' => 'Projects',
                        'icon' => 'bx bx-folder',
                        'value' => $currentProjectCount,
                        'color' => 'primary',
                    ],
                    [
                        'title' => 'Pending Projects',
                        'icon' => 'bx bx-time',
                        'value' => $pendingProjectCount,
                        'color' => 'warning',
                    ],
                    [
                        'title' => 'Closed Projects',
                        'icon' => 'bx bx-check-circle',
                        'value' => $closedProjectCount,
                        'color' => 'success',
                    ],
                    [
                        'title' => 'Clients',
                        'icon' => 'bx bx-group',
                        'value' => $fy || $quarter || ($fyFrom && $fyTo) ? $filteredClientCount : $totalClientCount,
                        'subtext' =>
                            $fy || $quarter || ($fyFrom && $fyTo) ? "Filtered of {$totalClientCount}" : 'Total',
                        'color' => 'info',
                    ],
                ];
            @endphp

            @foreach ($stats as $stat)
                <div class="mb-4 col-md-3">
                    <div class="border-0 shadow card custom-card h-100">
                        <div class="text-center card-body">
                            <div class="mb-2 text-{{ $stat['color'] }}">
                                <i class="{{ $stat['icon'] }} card-icon" style="font-size: 2.5rem;"></i>
                            </div>
                            <h6 class="text-muted text-uppercase fw-semibold">{{ $stat['title'] }}</h6>
                            <p class="fs-3 fw-bold text-{{ $stat['color'] }} mb-0">{{ $stat['value'] }}</p>
                            @if (!empty($stat['subtext']))
                                <small class="text-muted">{{ $stat['subtext'] }}</small>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>


        @if ($fullYearMode)
            {{-- SHOW Q1 to Q4 breakdown + chart only when quarter is NOT filtered --}}
            <div class="mt-5">
                <h4 class="mb-3 fw-bold">{{ request('fy') }} Quarter-wise Summary</h4>
                <div class="table-responsive">
                    <table class="table text-center align-middle table-bordered table-striped">
                        <thead style="background-color: #00326e; color:white;">
                            <tr>
                                <th>Metric</th>
                                @foreach ($quarterWiseData->keys() as $q)
                                    <th>{{ $q }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (['Booking Revenue', 'Actual Business Margins', 'Total Invoice Value In USD'] as $metric)
                                <tr>
                                    <th style="color:black !important;">{{ $metric }}</th>
                                    @foreach ($quarterWiseData as $quarter => $data)
                                        <td>{{ number_format($data[$metric], 2) }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <canvas id="fyBarChart" height="120"></canvas>
            </div>
            <div class="row">
                <div class="mt-5 col-md-4">
                    <h5 class="fw-bold">{{ request('fy') }} - Booking Revenue (Quarter-wise)</h5>
                    <canvas id="fyRevenueChart" height="220"></canvas>
                </div>

                <div class="mt-5 col-md-4">
                    <h5 class="fw-bold">{{ request('fy') }} - Actual Business Margins (Quarter-wise)</h5>
                    <canvas id="fyMarginChart" height="220"></canvas>
                </div>

                <div class="mt-5 col-md-4">
                    <h5 class="fw-bold"> {{ request('fy') }} - Total Invoice Value In USD (Quarter-wise)</h5>
                    <canvas id="fyInvoiceChart" height="220"></canvas>
                </div>
            </div>
        @elseif($fy && $quarter)
            {{-- SHOW only selected quarter metrics --}}
            <div class="mt-5">
                <h4 class="mb-3 fw-bold">{{ request('fy') }} {{ request('quarter') }} Summary</h4>
                <div class="table-responsive">
                    <table class="table text-center align-middle table-bordered table-striped">
                        <thead style="background-color: #00326e; color:white !important;">
                            <tr>
                                {{-- <th>Total Currency</th> --}}
                                <th>Booking Revenue</th>
                                <th>Actual Business Margins</th>
                                <th>Total Invoice Value In USD</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                {{-- <td>{{ number_format($currencyTotal, 2) }}</td> --}}
                                <td>{{ number_format($originalRevenueTotal, 2) }}</td>
                                <td>{{ number_format($marginTotal, 2) }}</td>
                                <td>{{ number_format($invoiceAmountTotal, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($fy && $quarter)
                <div class="mt-4">
                    <h5 class="fw-bold">{{ request('fy') }} {{ request('quarter') }} Chart</h5>
                    <canvas id="quarterChart" height="120"></canvas>
                </div>
            @endif
        @endif
        @if ($multiYearMode)
            <div class="mt-5">
                <h4 class="mb-3 fw-bold">
                    {{ $quarterFilter ? "{$quarterFilter} Comparison" : 'FY Comparison' }} ({{ $fyFrom }} -
                    {{ $fyTo }})
                </h4>
                <div class="table-responsive">
                    <table class="table text-center align-middle table-bordered table-striped">
                        <thead style="background-color: #00326e; color:white !important;">
                            <tr>
                                <th>Metric</th>
                                @foreach ($multiYearData->keys() as $fyLabel)
                                    <th>{{ $fyLabel }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (['Booking Revenue', 'Actual Business Margins', 'Total Invoice Value In USD'] as $metric)
                                <tr>
                                    <th style="color:black !important;">{{ $metric }}</th>
                                    @foreach ($multiYearData as $fy => $data)
                                        <td>{{ number_format($data[$metric], 2) }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <canvas id="multiYearChart" height="120"></canvas>
            </div>
        @endif

        @if ($multiYearMode && $multiYearData->isNotEmpty())
            <div class="mt-4 row">
                <div class="col-md-4">
                    <h4 class="mt-4 fw-bold">Year-wise Comparison - Booking Revenue</h4>
                    <canvas id="revenueChart" height="300"></canvas>
                </div>

                <div class="mt-4 col-md-4">
                    <h4 class="mt-4 fw-bold">Year-wise Comparison - Actual Business Margins</h4>
                    <canvas id="marginChart" height="300"></canvas>
                </div>

                <div class="mt-4 col-md-4">
                    <h4 class="mt-4 fw-bold">Year-wise Comparison - Total Invoice Value In USD</h4>
                    <canvas id="invoiceChart" height="300"></canvas>
                </div>
            </div>
        @endif



    </div>
@endsection
@push('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        @if ($fullYearMode)
            const labels = {!! json_encode($quarterWiseData->keys()) !!};

            const revenue = {!! json_encode($quarterWiseData->pluck('Booking Revenue')->map(fn($v) => (float) $v)->values()) !!};
            const margin = {!! json_encode($quarterWiseData->pluck('Actual Business Margins')->map(fn($v) => (float) $v)->values()) !!};
            const invoice = {!! json_encode($quarterWiseData->pluck('Total Invoice Value In USD')->map(fn($v) => (float) $v)->values()) !!};

            const yAxisOptions = {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        // Use Number() to ensure it's numeric and avoid NaN
                        return '{{ $currencySymbol ?? "$" }}' + Number(value).toLocaleString();
                    }
                }
            };

            new Chart(document.getElementById('fyBarChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                            label: 'Booking Revenue',
                            data: revenue,
                            backgroundColor: '#ffc107'
                        },
                        {
                            label: 'Actual Business Margins',
                            data: margin,
                            backgroundColor: '#0000ff'
                        },
                        {
                            label: 'Total Invoice Value In USD',
                            data: invoice,
                            backgroundColor: '#28a745'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Quarter-wise Financial Breakdown'
                        }
                    },
                    scales: {
                        y: yAxisOptions
                    }
                }
            });

            const buildSingleMetricChart = (canvasId, title, data, color) => {
                const ctx = document.getElementById(canvasId).getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: title,
                            data: data.map(Number), // Ensure conversion to numbers
                            backgroundColor: color,
                            barThickness: 40
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: title
                            }
                        },
                        scales: {
                            y: yAxisOptions
                        }
                    }
                });
            };

            buildSingleMetricChart('fyRevenueChart', 'Booking Revenue', revenue, '#ffc107');
            buildSingleMetricChart('fyMarginChart', 'Actual Business Margins', margin, '#0000ff');
            buildSingleMetricChart('fyInvoiceChart', 'Total Invoice Value In USD', invoice, '#28a745');
        @endif
    </script>

    <script>
        @if ($fy && $quarter)
            new Chart(document.getElementById('quarterChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: ['Booking Revenue', 'Actual Business Margins', 'Total Invoice Value In USD'],
                    datasets: [{
                        label: 'Amount in {{ $currencySymbol ?? 'USD' }}',
                        data: [
                            {{ (float) $originalRevenueTotal }},
                            {{ (float) $marginTotal }},
                            {{ (float) $invoiceAmountTotal }}
                        ],
                        backgroundColor: [
                            'rgba(255, 215, 0, 0.8)', // Yellow for Revenue
                            'rgba(0, 123, 255, 0.8)', // Blue for Margin
                            'rgba(40, 167, 69, 0.8)' // Green for Invoice
                        ],
                        borderColor: [
                            'rgba(255, 215, 0, 1)',
                            'rgba(0, 123, 255, 1)',
                            'rgba(40, 167, 69, 1)'
                        ],
                        borderWidth: 1,
                        barThickness: 70
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '{{ $currencySymbol ?? "$" }}' + Number(value).toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        @endif
    </script>


    <script>
        @if ($multiYearMode && $multiYearData->isNotEmpty())
            const multiYearCtx = document.getElementById('multiYearChart');
            if (multiYearCtx) {
                new Chart(multiYearCtx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($multiYearData->keys()) !!},
                        datasets: [
                            // {
                            //     label: 'Currency',
                            //     data: {!! json_encode($multiYearData->pluck('Currency')->values()) !!},
                            //     backgroundColor: '#007bff',
                            //     barThickness: 40
                            // },
                            {
                                label: 'Booking Revenue',
                                data: {!! json_encode($multiYearData->pluck('Booking Revenue')->values()) !!},
                                backgroundColor: '#ffc107',
                                barThickness: 40
                            },
                            {
                                label: 'Actual Business Margins',
                                data: {!! json_encode($multiYearData->pluck('Actual Business Margins')->values()) !!},
                                backgroundColor: '#0000ff',
                                barThickness: 40
                            },
                            {
                                label: 'Total Invoice Value In USD',
                                data: {!! json_encode($multiYearData->pluck('Total Invoice Value In USD')->values()) !!},
                                backgroundColor: '#28a745',
                                barThickness: 40
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: '{{ $quarterFilter ? "{$quarterFilter}" : 'FY' }} Comparison Chart'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return '{{ $currencySymbol ?? "$" }}' + value.toLocaleString();
                                    }
                                }
                            }
                        }
                    }
                });
            }
        @endif


        @if ($multiYearMode && $multiYearData->isNotEmpty())
            const fiscalYears = {!! json_encode($multiYearData->keys()) !!};

            const revenueData = {!! json_encode($multiYearData->pluck('Booking Revenue')->values()) !!};
            const marginData = {!! json_encode($multiYearData->pluck('Actual Business Margins')->values()) !!};
            const invoiceData = {!! json_encode($multiYearData->pluck('Total Invoice Value In USD')->values()) !!};

            const chartOptions = (title, label, data, bgColor) => ({
                type: 'bar',
                data: {
                    labels: fiscalYears,
                    datasets: [{
                        label: label,
                        data: data,
                        backgroundColor: bgColor,
                        barThickness: 40
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: title
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '{{ $currencySymbol ?? "$" }}' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });

            new Chart(document.getElementById('revenueChart'), chartOptions("Booking Revenue Year-wise", "Booking Revenue",
                revenueData,
                '#ffc107'));
            new Chart(document.getElementById('marginChart'), chartOptions("Actual Business Margins Year-wise",
                "Actual Business Margins",
                marginData,
                '#0000ff'));
            new Chart(document.getElementById('invoiceChart'), chartOptions("Total Invoice Value In USD Year-wise",
                "Total Invoice Value In USD", invoiceData,
                '#28a745'));
        @endif

        window.addEventListener('DOMContentLoaded', function() {
            const url = new URL(window.location.href);

            // Only reset if filters are applied
            if (url.searchParams.has('fy') || url.searchParams.has('quarter') || url.searchParams.has('fy_from') ||
                url.searchParams.has('fy_to')) {
                // Reset all select fields
                document.querySelectorAll(
                        'select[name="fy"], select[name="quarter"], select[name="fy_from"], select[name="fy_to"]')
                    .forEach(select => {
                        select.selectedIndex = 0;
                    });
            }
        });
    </script>
@endpush
