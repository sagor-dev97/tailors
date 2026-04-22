@extends('backend.app', ['title' => 'User Details'])

@section('content')
    <div class="app-content main-content mt-0">
        <div class="side-app">
            <div class="main-container container-fluid">

                <!-- Page Header -->
                <div class="page-header d-flex align-items-center justify-content-between">
                    <h1 class="page-title mb-0">User Details</h1>

                    <a href="{{ route('admin.userlist.index') }}" class="btn btn-outline-primary btn-sm">
                        ← Back to User List
                    </a>
                </div>

                <div class="row mt-4">

                    <!-- LEFT: PROFILE CARD -->
                    <div class="col-lg-4">
                        <div class="card shadow-sm text-center">

                            <div class="card-body">
                                <div class="avatar avatar-xl mx-auto mb-3 bg-primary text-white rounded-circle">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>

                                <h4 class="mb-1">
                                    {{ $user->name }} {{ $user->last_name }}
                                </h4>

                                <p class="text-muted mb-2">
                                    {{ $user->username }}
                                </p>

                                <span class="badge bg-success">
                                    Active User
                                </span>
                            </div>

                            <div class="card-footer text-muted">
                                <small>
                                    Joined on {{ $user->created_at->format('d M Y') }}
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT: USER INFO -->
                    <div class="col-lg-8">
                        <div class="card shadow-sm">

                            <div class="card-header">
                                <h5 class="mb-0">User Information</h5>
                            </div>

                            <div class="card-body">
                                <div class="row">

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted">Email</label>
                                        <div class="fw-semibold">
                                            {{ $user->email }}
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted">Phone Number</label>
                                        <div class="fw-semibold">
                                            {{ $user->phone_number ?? '—' }}
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted">Date of Birth</label>
                                        <div class="fw-semibold">
                                            {{ $user->created_at->format('d M Y') }}
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted">Is Verified</label>
                                        <div class="fw-semibold">
                                            <span class="badge {{ $user->is_verified ? 'bg-success' : 'bg-danger' }}">
                                                {{ $user->is_verified ? 'Verified' : 'Not Verified' }}
                                            </span>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
@endsection
