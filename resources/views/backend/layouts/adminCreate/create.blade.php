@extends('backend.app', ['title' => 'Create Prompts'])


@section('content')
<!--app-content open-->
<div class="app-content main-content mt-0">
    <div class="side-app">

        <!-- CONTAINER -->
        <div class="main-container container-fluid">

            <div class="page-header">
                <div>
                    <h1 class="page-title">Prompts</h1>

                </div>
                <div class="ms-auto pageheader-btn">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Prompts</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Create</li>
                    </ol>
                </div>
            </div>

            <div class="row" id="user-profile">
                <div class="col-lg-12">

                    <div class="tab-content">
                        <div class="tab-pane active show" id="editProfile">
                            <div class="card">
                                <div class="card-body border-0">
                                    <form class="form form-horizontal" method="post"
                                        action="{{ route('admin.adminCreate.store') }}" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row mb-4">

                                            <div class="form-group">
                                                <label for="username" class="form-label">Name:</label>
                                                <input type="text"
                                                    class="form-control @error('name') is-invalid @enderror"
                                                    name="name" placeholder="Name" id=""
                                                    value="{{ old('name') }}">
                                                @error('name')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label for="username" class="form-label">Phone Number:</label>
                                                <input type="text"
                                                    class="form-control @error('phone_number') is-invalid @enderror"
                                                    name="phone_number" placeholder="Phone Number" id=""
                                                    value="{{ old('phone_number') }}">
                                                @error('phone_number')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <!-- password field -->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="password" class="form-label">Password:</label>
                                                        <div class="input-group">
                                                            <input type="password"
                                                                class="form-control @error('password') is-invalid @enderror"
                                                                name="password" placeholder="Password" id="password">
                                                            <span class="input-group-text toggle-password" data-target="#password" style="cursor: pointer;">
                                                                <i class="fa fa-eye"></i>
                                                            </span>
                                                            @error('password')
                                                            <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="password_confirmation" class="form-label">Confirm Password:</label>
                                                        <div class="input-group">
                                                            <input type="password"
                                                                class="form-control"
                                                                name="password_confirmation" placeholder="Confirm Password" id="password_confirmation">
                                                            <span class="input-group-text toggle-password" data-target="#password_confirmation" style="cursor: pointer;">
                                                                <i class="fa fa-eye"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="address" class="form-label">Address:</label>
                                                        <textarea class="form-control @error('address') is-invalid @enderror" name="address" id="description"
                                                            placeholder="Enter here address" rows="3">{{ old('address') ?? '' }}</textarea>
                                                        @error('address')
                                                        <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <button class="submit btn btn-primary" type="submit">Submit</button>
                                            </div>

                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<!-- CONTAINER CLOSED -->
@endsection
@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#subcategory_id').select2();
    });


     $(document).ready(function() {
        $('#subcategory_id').select2();

        // Password show/hide toggle
        $('.toggle-password').on('click', function() {
            const target = $(this).data('target');
            const input = $(target);
            const icon = $(this).find('i');

            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                input.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });
    });
</script>
@endpush