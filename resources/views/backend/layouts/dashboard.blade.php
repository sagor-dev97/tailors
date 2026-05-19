@extends('backend.app')

@php
    use Illuminate\Support\Facades\DB;
@endphp

@section('content')
    <!--app-content open-->
    <div class="app-content main-content mt-0">
        <div class="side-app">
            <!-- CONTAINER -->
            <div class="main-container container-fluid">

                <!-- PAGE-HEADER -->
                <div class="page-header">
                    <div>
                        <h1 class="page-title">Dashboard</h1>
                        <p class="page-description text-muted">Welcome to your admin dashboard</p>
                    </div>
                    <div class="ms-auto pageheader-btn">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                        </ol>
                    </div>
                </div>
                <!-- PAGE-HEADER END -->

                <!-- ROW-1: Stats Cards -->
                <div class="row row-deck">
                    <!-- Total Users -->
                    <div class="col-lg-6 col-sm-12 col-md-6 col-xl-4">
                        <div class="card stats-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="stats-info">
                                        <h2 class="stats-number mb-2">
                                            {{ DB::table('users')->where('status', 'active')->count() }}</h2>
                                        <p class="stats-label mb-0">Total Users</p>
                                        <span class="badge bg-success-transparent mt-2">
                                            <i class="fa fa-arrow-up me-1"></i> Active
                                        </span>
                                    </div>
                                    <div class="stats-icon bg-success-gradient">
                                        <i class="fa fa-users"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Prompts -->
                    <div class="col-lg-6 col-sm-12 col-md-6 col-xl-4">
                        <div class="card stats-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="stats-info">
                                        <h2 class="stats-number mb-2">
                                            {{-- {{ DB::table('orders')->where('status', 'pending')->count() }}</h2>
                                             --}}
                                             {{ \App\Models\User::role('manager', 'web')->where('status', 'active')->count() }}
                                        <p class="stats-label mb-0">Total Admin</p>
                                        <span class="badge bg-primary-transparent mt-2">
                                            <i class="fa fa-chart-line me-1"></i> Admin User
                                        </span>
                                    </div>
                                    <div class="stats-icon bg-primary-gradient">
                                        <i class="fa fa-layer-group"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Questions -->
                    <div class="col-lg-6 col-sm-12 col-md-6 col-xl-4">
                        <div class="card stats-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="stats-info">
                                        <h2 class="stats-number mb-2">
                                            {{-- {{ DB::table('orders')->where('status', 'completed')->count() }}</h2> --}}
                                            {{ DB::table('users')->where('status', 'inactive')->count() }}</h2>
                                        <p class="stats-label mb-0">Total Inactive Users</p>
                                        <span class="badge bg-info-transparent mt-2">
                                            <i class="fa fa-comments me-1"></i> Inactive
                                        </span>
                                    </div>
                                    <div class="stats-icon bg-info-gradient">
                                        <i class="fa fa-question-circle"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                </div>
                <!-- END ROW-1 -->

                <!-- ROW-2: Charts -->
                <div class="row">
                    <div class="col-12">
                        <div class="card custom-card">
                            <div
                                class="card-header d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                                <div>
                                    <h3 class="card-title mb-2">Yearly Order Performance</h3>
                                    <p class="text-muted mb-0">Monthly breakdown of orders, completed counts, pending
                                        counts, and order revenue for the selected period.</p>
                                </div>
                                <form id="dashboardFiltersForm" class="row gx-2 gy-2 align-items-end" method="GET"
                                    action="{{ route('admin.dashboard') }}">
                                    <div class="col-auto">
                                        <label class="form-label mb-1">Year</label>
                                        <select id="dashboardYearSelect" class="form-control form-select" name="year">
                                            @foreach ($years as $year)
                                                <option value="{{ $year }}" @selected(!request()->filled('from_date') && $selectedYear == $year)>
                                                    {{ $year }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-auto">
                                        <label class="form-label mb-1">From</label>
                                        <input id="dashboardFromDate" type="date" class="form-control" name="from_date"
                                            value="{{ $fromDate ?? '' }}">
                                    </div>
                                    <div class="col-auto">
                                        <label class="form-label mb-1">To</label>
                                        <input id="dashboardToDate" type="date" class="form-control" name="to_date"
                                            value="{{ $toDate ?? '' }}">
                                    </div>
                                    <div class="col-auto d-flex align-items-center gap-2">
                                        <button type="button" id="dashboardFilterReset"
                                            class="btn btn-light border d-flex align-items-center justify-content-center"
                                            title="Reset filters">
                                            <span id="dashboardFilterResetIcon"><i class="fa fa-sync-alt"></i></span>
                                            <span id="dashboardFilterResetSpinner"
                                                class="spinner-border spinner-border-sm ms-1 d-none" role="status"
                                                aria-hidden="true"></span>
                                        </button>
                                        <small class="text-muted">Applied on change</small>
                                    </div>
                                </form>
                            </div>
                            <div class="card-body">
                                <div class="row gy-3 mb-4">
                                    <div class="col-lg-3 col-md-6">
                                        <div class="dashboard-summary-card bg-primary text-white p-4 rounded-3 h-100">
                                            <p class="text-white-75 mb-2">Total Orders</p>
                                            <h2 class="mb-1">{{ number_format($summary['total_orders']) }}</h2>
                                            <small class="text-white-75">Selected period</small>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6">
                                        <div class="dashboard-summary-card bg-success text-white p-4 rounded-3 h-100">
                                            <p class="text-white-75 mb-2">Completed Orders</p>
                                            <h2 class="mb-1">{{ number_format($summary['completed_orders']) }}</h2>
                                            <small class="text-white-75">Completed count</small>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6">
                                        <div class="dashboard-summary-card bg-warning text-dark p-4 rounded-3 h-100">
                                            <p class="text-dark-75 mb-2">Pending Orders</p>
                                            <h2 class="mb-1">{{ number_format($summary['pending_orders']) }}</h2>
                                            <small class="text-dark-75">Waiting for completion</small>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6">
                                        <div class="dashboard-summary-card bg-info text-white p-4 rounded-3 h-100">
                                            <p class="text-white-75 mb-2">Pending Amount</p>
                                            <h2 class="mb-1">৳{{ number_format($summary['pending_amount'], 2) }}</h2>
                                            <small class="text-white-75">Pending order revenue</small>
                                        </div>
                                    </div>
                                </div>
                                <div id="dashboardOrderChart"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END ROW-2 -->

            </div>
            <!-- CONTAINER CLOSED -->
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Stats Card Styles */
        .stats-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            overflow: hidden;
            position: relative;
            margin-bottom: 24px;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.12);
        }

        .stats-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color, #667eea), var(--secondary-color, #764ba2));
            border-radius: 12px 12px 0 0;
        }

        .stats-info {
            flex: 1;
        }

        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }

        .stats-label {
            font-size: 0.875rem;
            color: #64748b;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stats-icon {
            width: 70px;
            height: 70px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        .stats-card:hover .stats-icon {
            transform: scale(1.1);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
        }

        .stats-icon i {
            transition: all 0.3s ease;
        }

        .stats-card:hover .stats-icon i {
            animation: iconBounce 0.6s ease;
        }

        @keyframes iconBounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-5px);
            }
        }

        /* Gradient Backgrounds */
        .bg-success-gradient {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .bg-primary-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .bg-info-gradient {
            background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
        }

        .bg-warning-gradient {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        .bg-danger-gradient {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        .bg-purple-gradient {
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        }

        /* Transparent Badges */
        .bg-success-transparent {
            background-color: rgba(16, 185, 129, 0.1);
            color: #059669;
            font-size: .75rem;
            padding: 4px 10px;
            font-weight: 600;
            border-radius: 20px;
        }

        .bg-primary-transparent {
            background-color: rgba(102, 126, 234, 0.1);
            color: #667eea;
            font-size: .75rem;
            padding: 4px 10px;
            font-weight: 600;
            border-radius: 20px;
        }

        .bg-info-transparent {
            background-color: rgba(6, 182, 212, 0.1);
            color: #0891b2;
            font-size: .75rem;
            padding: 4px 10px;
            font-weight: 600;
            border-radius: 20px;
        }

        .bg-warning-transparent {
            background-color: rgba(245, 158, 11, 0.1);
            color: #d97706;
            font-size: .75rem;
            padding: 4px 10px;
            font-weight: 600;
            border-radius: 20px;
        }

        /* Custom Card */
        .custom-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .custom-card .card-header {
            background: transparent;
            border-bottom: 1px solid #f0f0f0;
            padding: 1.5rem;
        }

        .custom-card .card-body {
            padding: 1.5rem;
        }

        /* Revenue Stats */
        .revenue-item {
            padding: 0.75rem 0;
        }

        .revenue-item:not(:last-child) {
            border-bottom: 1px solid #f0f0f0;
        }

        .progress {
            border-radius: 10px;
            background-color: #f1f5f9;
        }

        .progress-bar {
            border-radius: 10px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .stats-number {
                font-size: 1.5rem;
            }

            .stats-icon {
                width: 60px;
                height: 60px;
                font-size: 1.5rem;
            }
        }

        .page-description {
            color: #64748b;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .dashboard-summary-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .dashboard-summary-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 30px rgba(15, 23, 42, 0.12);
        }

        #dashboardOrderChart {
            min-height: 380px;
        }

        .form-label {
            font-size: 0.82rem;
            font-weight: 600;
            color: #475569;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var chartOptions = {
                chart: {
                    type: 'bar',
                    height: 380,
                    toolbar: {
                        show: false
                    },
                    foreColor: '#475569'
                },
                plotOptions: {
                    bar: {
                        borderRadius: 10,
                        columnWidth: '52%',
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                series: [{
                        name: 'Total Orders',
                        data: {!! json_encode($monthlyTotals) !!}
                    },
                    {
                        name: 'Completed Orders',
                        data: {!! json_encode($monthlyCompleted) !!}
                    },
                    {
                        name: 'Pending Orders',
                        data: {!! json_encode($monthlyPending) !!}
                    }
                ],
                xaxis: {
                    categories: {!! json_encode($months) !!},
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    }
                },
                yaxis: {
                    labels: {
                        formatter: function(val) {
                            return parseInt(val, 10);
                        }
                    }
                },
                colors: ['#4f46e5', '#10b981', '#f59e0b'],
                fill: {
                    opacity: 0.9
                },
                grid: {
                    strokeDashArray: 5,
                    borderColor: '#e2e8f0'
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val + ' orders';
                        }
                    }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right',
                    markers: {
                        radius: 12
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector('#dashboardOrderChart'), chartOptions);
            chart.render();

            var form = document.getElementById('dashboardFiltersForm');
            var yearSelect = document.getElementById('dashboardYearSelect');
            var fromInput = document.getElementById('dashboardFromDate');
            var toInput = document.getElementById('dashboardToDate');
            var resetButton = document.getElementById('dashboardFilterReset');

            if (yearSelect) {
                yearSelect.addEventListener('change', function() {
                    form.submit();
                });
            }

            if (fromInput) {
                fromInput.addEventListener('change', function() {
                    if (fromInput.value && toInput.value) {
                        form.submit();
                    }
                });
                fromInput.addEventListener('click', function() {
                    this.showPicker?.();
                });
            }

            if (toInput) {
                toInput.addEventListener('change', function() {
                    if (fromInput.value && toInput.value) {
                        form.submit();
                    }
                });
                toInput.addEventListener('click', function() {
                    this.showPicker?.();
                });
            }

            if (resetButton) {
                resetButton.addEventListener('click', function() {
                    var icon = document.getElementById('dashboardFilterResetIcon');
                    var spinner = document.getElementById('dashboardFilterResetSpinner');
                    if (icon && spinner) {
                        icon.classList.add('d-none');
                        spinner.classList.remove('d-none');
                    }
                    window.location.href = '{{ route('admin.dashboard') }}';
                });
            }
        });
    </script>
@endpush

@push('scripts')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.36.3/dist/apexcharts.min.js"></script>
@endpush
