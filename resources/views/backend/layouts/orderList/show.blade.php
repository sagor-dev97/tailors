@extends('backend.app', ['title' => 'Purchase Details'])

@section('content')
<div class="app-content main-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">

            <!-- Page Header -->
            <div class="page-header d-flex align-items-center justify-content-between mb-6">
                <div>
                    <h1 class="page-title mb-2 fw-bold text-gradient">Purchase Details</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.inapp.index') }}" class="text-muted">In-App Purchases</a></li>
                            <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Order #{{ str_pad($data->id, 6, '0', STR_PAD_LEFT) }}</li>
                        </ol>
                    </nav>
                </div>
                
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.inapp.index') }}" class="btn btn-outline-light btn-icon">
                        <i class="bi bi-arrow-left me-1"></i> Back
                    </a>
                    <div class="dropdown">
                        <button class="btn btn-outline-light btn-icon" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><i class="bi bi-pencil me-2"></i>Edit Purchase</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-download me-2"></i>Export Invoice</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-trash me-2"></i>Delete Purchase</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!-- Left Column - Profile & Purchase Card -->
                <div class="col-lg-4">
                    <!-- Profile Card -->
                    <div class="card border-0 shadow-lg overflow-hidden">
                        <div class="card-header bg-gradient-primary text-white py-4 position-relative">
                            <div class="position-absolute top-0 end-0 mt-3 me-3">
                                <span class="badge bg-white text-primary rounded-pill px-3 py-2">
                                    <i class="bi bi-shield-check me-1"></i>Active
                                </span>
                            </div>
                            <div class="text-center">
                                <div class="avatar avatar-xxl border border-4 border-white shadow-lg mb-3 mx-auto">
                                    @if($data->user && $data->user->avatar)
                                        <img src="{{ asset($data->user->avatar) }}" alt="{{ $data->user->name }}" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">
                                    @else
                                        <div class="avatar-placeholder bg-white text-primary d-flex align-items-center justify-content-center rounded-circle fw-bold fs-2" style="width: 80px; height: 80px;">
                                            {{ strtoupper(substr($data->user->name ?? 'U', 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                                <h4 class="mb-1 text-white">{{ $data->user->name ?? 'User' }}</h4>
                                <p class="text-white-50 mb-2">
                                    <i class="bi bi-at me-1"></i>{{ $data->user->username ?? '@username' }}
                                </p>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-center mb-4">
                                @if($data->user && $data->user->is_verified)
                                    <span class="badge bg-success-subtle text-success border border-success rounded-pill px-3 py-2">
                                        <i class="bi bi-patch-check-fill me-1"></i>Verified Account
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger rounded-pill px-3 py-2">
                                        <i class="bi bi-exclamation-circle me-1"></i>Not Verified
                                    </span>
                                @endif
                            </div>
                            
                            <div class="stats-grid">
                                <div class="stat-item text-center">
                                    <div class="stat-icon bg-primary-subtle text-primary rounded-circle mb-2 mx-auto">
                                        <i class="bi bi-cart-check"></i>
                                    </div>
                                    <div class="stat-value fw-bold">1</div>
                                    <div class="stat-label text-muted">Purchase</div>
                                </div>
                                <div class="stat-item text-center">
                                    <div class="stat-icon bg-success-subtle text-success rounded-circle mb-2 mx-auto">
                                        <i class="bi bi-currency-dollar"></i>
                                    </div>
                                    <div class="stat-value fw-bold">${{ number_format($data->amount, 2) }}</div>
                                    <div class="stat-label text-muted">Spent</div>
                                </div>
                                <div class="stat-item text-center">
                                    <div class="stat-icon bg-info-subtle text-info rounded-circle mb-2 mx-auto">
                                        <i class="bi bi-calendar-check"></i>
                                    </div>
                                    <div class="stat-value fw-bold">{{ $data->created_at->format('d M') }}</div>
                                    <div class="stat-label text-muted">Joined</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary Card -->
                    <div class="card border-0 shadow-lg mt-4">
                        <div class="card-header bg-transparent border-bottom py-3">
                            <h5 class="mb-0">
                                <i class="bi bi-receipt me-2 text-primary"></i>Order Summary
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="order-timeline">
                                <div class="timeline-item active">
                                    <div class="timeline-dot bg-primary"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Order Placed</h6>
                                        <p class="text-muted mb-0 small">{{ $data->created_at->format('d M Y, h:i A') }}</p>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-dot bg-success"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Payment Confirmed</h6>
                                        <p class="text-muted mb-0 small">{{ $data->created_at->format('d M Y, h:i A') }}</p>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-dot bg-info"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Order Completed</h6>
                                        <p class="text-muted mb-0 small">Status: Active</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Purchase Details -->
                <div class="col-lg-8">
                    <!-- Purchase Information Card -->
                    <div class="card border-0 shadow-lg">
                        <div class="card-header bg-transparent border-bottom py-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <h5 class="mb-0">
                                    <i class="bi bi-credit-card me-2 text-success"></i>Purchase Information
                                </h5>
                                <div>
                                    @if($data->status == 'active')
                                        <span class="badge bg-success-subtle text-success border border-success rounded-pill px-3 py-2">
                                            <i class="bi bi-check-circle-fill me-1"></i> Active
                                        </span>
                                    @else
                                        <span class="badge bg-success-subtle text-success border border-success rounded-pill px-3 py-2">
                                            <i class="bi bi-clock-fill me-1"></i> Active
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="info-card">
                                        <div class="info-icon bg-primary-subtle text-primary rounded">
                                            <i class="bi bi-hash"></i>
                                        </div>
                                        <div class="info-content">
                                            <label class="info-label">Purchase ID</label>
                                            <div class="info-value fw-bold text-primary">#{{ str_pad($data->id, 6, '0', STR_PAD_LEFT) }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-card">
                                        <div class="info-icon bg-success-subtle text-success rounded">
                                            <i class="bi bi-receipt"></i>
                                        </div>
                                        <div class="info-content">
                                            <label class="info-label">Transaction ID</label>
                                            <div class="info-value fw-bold">{{ $data->purchase_id ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-card">
                                        <div class="info-icon bg-warning-subtle text-warning rounded">
                                            <i class="bi bi-box-seam"></i>
                                        </div>
                                        <div class="info-content">
                                            <label class="info-label">Product ID</label>
                                            <div class="info-value fw-bold">{{ $data->product_id ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-card">
                                        <div class="info-icon bg-danger-subtle text-danger rounded">
                                            <i class="bi bi-currency-dollar"></i>
                                        </div>
                                        <div class="info-content">
                                            <label class="info-label">Amount</label>
                                            <div class="info-value fw-bold text-success">${{ number_format($data->amount, 2) }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-card">
                                        <div class="info-icon bg-info-subtle text-info rounded">
                                            <i class="bi bi-calendar-date"></i>
                                        </div>
                                        <div class="info-content">
                                            <label class="info-label">Purchase Date</label>
                                            <div class="info-value fw-bold">{{ $data->purchase_date ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-card">
                                        <div class="info-icon bg-purple-subtle text-purple rounded">
                                            <i class="bi bi-calendar-check"></i>
                                        </div>
                                        <div class="info-content">
                                            <label class="info-label">Order Date</label>
                                            <div class="info-value fw-bold">{{ $data->created_at->format('d M Y') }}</div>
                                        </div>
                                    </div>
                                </div>
                                @if($data->verification_data)
                                <div class="col-12">
                                    <div class="info-card">
                                        <div class="info-icon bg-dark-subtle text-dark rounded">
                                            <i class="bi bi-shield-check"></i>
                                        </div>
                                        <div class="info-content">
                                            <label class="info-label">Verification Data</label>
                                            <div class="info-value">
                                                <pre class="mb-0 bg-light p-3 rounded border">{{ $data->verification_data }}</pre>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- User Details Card -->
                    <div class="card border-0 shadow-lg mt-4">
                        <div class="card-header bg-transparent border-bottom py-3">
                            <h5 class="mb-0">
                                <i class="bi bi-person-badge me-2 text-primary"></i>User Details
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                @if($data->user)
                                <div class="col-md-6">
                                    <div class="info-card">
                                        <div class="info-icon bg-primary-subtle text-primary rounded">
                                            <i class="bi bi-person"></i>
                                        </div>
                                        <div class="info-content">
                                            <label class="info-label">Full Name</label>
                                            <div class="info-value fw-bold">{{ $data->user->name }} {{ $data->user->last_name }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-card">
                                        <div class="info-icon bg-success-subtle text-success rounded">
                                            <i class="bi bi-envelope"></i>
                                        </div>
                                        <div class="info-content">
                                            <label class="info-label">Email Address</label>
                                            <div class="info-value fw-bold">{{ $data->user->email }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-card">
                                        <div class="info-icon bg-warning-subtle text-warning rounded">
                                            <i class="bi bi-phone"></i>
                                        </div>
                                        <div class="info-content">
                                            <label class="info-label">Phone Number</label>
                                            <div class="info-value fw-bold">{{ $data->user->phone_number ?? 'Not Provided' }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-card">
                                        <div class="info-icon bg-info-subtle text-info rounded">
                                            <i class="bi bi-calendar-heart"></i>
                                        </div>
                                        <div class="info-content">
                                            <label class="info-label">Date of Birth</label>
                                            <div class="info-value fw-bold">
                                                {{ $data->user->dob ? \Carbon\Carbon::parse($data->user->dob)->format('d M Y') : 'Not Set' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-card">
                                        <div class="info-icon bg-danger-subtle text-danger rounded">
                                            <i class="bi bi-at"></i>
                                        </div>
                                        <div class="info-content">
                                            <label class="info-label">Username</label>
                                            <div class="info-value fw-bold">{{ $data->user->username }}</div>
                                        </div>
                                    </div>
                                </div>
                                
                               
                                <div class="col-md-6">
                                    <div class="info-card">
                                        <div class="info-icon bg-pink-subtle text-pink rounded">
                                            <i class="bi bi-clock-history"></i>
                                        </div>
                                        <div class="info-content">
                                            <label class="info-label">Last Activity</label>
                                            <div class="info-value fw-bold">
                                                {{ $data->user->last_activity_at ? \Carbon\Carbon::parse($data->user->last_activity_at)->format('d M Y, h:i A') : 'Never' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @else
                                <div class="col-12 text-center py-5">
                                    <div class="avatar avatar-lg bg-light rounded-circle mb-3 mx-auto">
                                        <i class="bi bi-person-x text-muted fs-3"></i>
                                    </div>
                                    <h6 class="text-muted">User Not Found</h6>
                                    <p class="small text-muted mb-0">This purchase is not linked to any user account</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                   
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Custom Styles */
.text-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}

/* Avatar Styles */
.avatar-xxl {
    width: 80px;
    height: 80px;
}

.avatar-placeholder {
    width: 80px;
    height: 80px;
    font-weight: 600;
    font-size: 32px;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
}

.stat-icon {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.stat-value {
    font-size: 1.5rem;
    line-height: 1.2;
}

.stat-label {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Order Timeline */
.order-timeline {
    position: relative;
    padding-left: 30px;
}

.order-timeline::before {
    content: '';
    position: absolute;
    left: 11px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: linear-gradient(to bottom, #667eea, #764ba2);
}

.timeline-item {
    position: relative;
    margin-bottom: 1.5rem;
}

.timeline-dot {
    position: absolute;
    left: -36px;
    top: 0;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    border: 4px solid white;
    z-index: 1;
}

.timeline-item.active .timeline-dot {
    animation: pulse 2s infinite;
}

.timeline-content {
    padding-left: 10px;
}

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(102, 126, 234, 0.7);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(102, 126, 234, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(102, 126, 234, 0);
    }
}

/* Info Cards */
.info-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: #fff;
    border-radius: 10px;
    transition: all 0.3s ease;
    border: 1px solid #f0f0f0;
}

.info-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
}

.info-icon {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.info-label {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6c757d;
    margin-bottom: 0.25rem;
}

.info-value {
    font-size: 1rem;
    font-weight: 600;
}

/* Color Utilities */
.bg-primary-subtle { background-color: rgba(102, 126, 234, 0.1) !important; }
.bg-success-subtle { background-color: rgba(40, 199, 111, 0.1) !important; }
.bg-danger-subtle { background-color: rgba(234, 84, 85, 0.1) !important; }
.bg-warning-subtle { background-color: rgba(255, 193, 7, 0.1) !important; }
.bg-info-subtle { background-color: rgba(0, 201, 219, 0.1) !important; }
.bg-purple-subtle { background-color: rgba(111, 66, 193, 0.1) !important; }
.bg-teal-subtle { background-color: rgba(32, 201, 151, 0.1) !important; }
.bg-pink-subtle { background-color: rgba(231, 76, 60, 0.1) !important; }
.bg-dark-subtle { background-color: rgba(52, 58, 64, 0.1) !important; }

.text-purple { color: #6f42c1 !important; }
.text-teal { color: #20c997 !important; }
.text-pink { color: #e74c3c !important; }

/* Button Styles */
.btn-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.btn-lg {
    padding: 0.75rem 1.5rem;
    font-weight: 500;
}

/* Badge Styles */
.badge {
    font-weight: 500;
}

/* Responsive */
@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .order-timeline {
        padding-left: 20px;
    }
    
    .timeline-dot {
        left: -26px;
    }
}
</style>
@endpush