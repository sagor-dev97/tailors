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
                                    <h2 class="stats-number mb-2">{{DB::table('users')->where('status', 'active')->count()}}</h2>
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
                                    <h2 class="stats-number mb-2">{{DB::table('orders')->where('status', 'pending')->count()}}</h2>
                                    <p class="stats-label mb-0">Total Pending Orders</p>
                                    <span class="badge bg-primary-transparent mt-2">
                                        <i class="fa fa-chart-line me-1"></i> Categories
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
                                    <h2 class="stats-number mb-2">{{DB::table('orders')->where('status', 'completed')->count()}}</h2>
                                    <p class="stats-label mb-0">Total Completed Orders</p>
                                    <span class="badge bg-info-transparent mt-2">
                                        <i class="fa fa-comments me-1"></i> Completed
                                    </span>
                                </div>
                                <div class="stats-icon bg-info-gradient">
                                    <i class="fa fa-question-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Redeem Codes -->
                {{-- <div class="col-lg-6 col-sm-12 col-md-6 col-xl-4">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="stats-info">
                                    <h2 class="stats-number mb-2">50</h2>
                                    <p class="stats-label mb-0">Total Redeem Code</p>
                                    <span class="badge bg-warning-transparent mt-2">
                                        <i class="fa fa-ticket me-1"></i> Redeems
                                    </span>
                                </div>
                                <div class="stats-icon bg-warning-gradient">
                                    <i class="fa fa-gift"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> --}}

                <!-- Total Purchases -->
                {{-- <div class="col-lg-6 col-sm-12 col-md-6 col-xl-4">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="stats-info">
                                    <h2 class="stats-number mb-2">50</h2>
                                    <p class="stats-label mb-0">Total Purchases</p>
                                </div>
                                <div class="stats-icon bg-danger-gradient">
                                    <i class="fa fa-cart-shopping"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> --}}

                <!-- Total Revenue -->
                {{-- <div class="col-lg-6 col-sm-12 col-md-6 col-xl-4">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="stats-info">
                                    <h2 class="stats-number mb-2">60</h2>
                                    <p class="stats-label mb-0">Total Revenue</p>
                                </div>
                                <div class="stats-icon bg-purple-gradient">
                                    <i class="fa fa-dollar-sign"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> --}}
            </div>
            <!-- END ROW-1 -->

            <!-- ROW-2: Charts -->
            {{-- <div class="row">
                <!-- Main Chart -->
                <div class="col-xl-8">
                    <div class="card custom-card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <div>
                                <h3 class="card-title mb-0">Purchase Analytics</h3>
                                <p class="text-muted mb-0">60</p>
                            </div>
                            
                        </div>
                        
                    </div>
                </div>
               
            </div> --}}
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
        top: 0; left: 0; right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color, #667eea), var(--secondary-color, #764ba2));
        border-radius: 12px 12px 0 0;
    }
    .stats-info { flex: 1; }
    .stats-number { font-size: 2rem; font-weight: 700; color: #1e293b; margin: 0; }
    .stats-label { font-size: 0.875rem; color: #64748b; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px; }
    .stats-icon {
        width: 70px; height: 70px; border-radius: 16px; display: flex;
        align-items: center; justify-content: center; font-size: 2rem; color: white;
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
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }
    
    /* Gradient Backgrounds */
    .bg-success-gradient { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .bg-primary-gradient { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .bg-info-gradient { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); }
    .bg-warning-gradient { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
    .bg-danger-gradient { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
    .bg-purple-gradient { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); }
    
    /* Transparent Badges */
    .bg-success-transparent { background-color: rgba(16,185,129,0.1); color:#059669; font-size:.75rem; padding:4px 10px; font-weight:600; border-radius:20px; }
    .bg-primary-transparent { background-color: rgba(102,126,234,0.1); color:#667eea; font-size:.75rem; padding:4px 10px; font-weight:600; border-radius:20px; }
    .bg-info-transparent { background-color: rgba(6,182,212,0.1); color:#0891b2; font-size:.75rem; padding:4px 10px; font-weight:600; border-radius:20px; }
    .bg-warning-transparent { background-color: rgba(245,158,11,0.1); color:#d97706; font-size:.75rem; padding:4px 10px; font-weight:600; border-radius:20px; }
    
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
        .stats-number { font-size: 1.5rem; }
        .stats-icon { width: 60px; height: 60px; font-size: 1.5rem; }
    }
    
    .page-description {
        color: #64748b;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
</style>
@endpush

@push('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.36.3/dist/apexcharts.min.js"></script>

@endpush