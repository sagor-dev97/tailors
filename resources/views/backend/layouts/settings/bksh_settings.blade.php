@extends('backend.app')

@section('content')
<!--app-content open-->
<div class="app-content main-content mt-0">
    <div class="side-app">

        <!-- CONTAINER -->
        <div class="main-container container-fluid">

            {{-- PAGE-HEADER --}}
            <div class="page-header">
                <div>
                    <h1 class="page-title">Bkash Settings <i class="fa-solid fa-triangle-exclamation text-danger" title="Warning"></i></h1>
                </div>
                <div class="ms-auto pageheader-btn">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Settings</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Bkash Settings</li>
                    </ol>
                </div>
            </div>
            {{-- PAGE-HEADER --}}


            <div class="row">
                <div class="col-lg-12 col-xl-12 col-md-12 col-sm-12">
                    <div class="card box-shadow-0">
                        <div class="card-body">
                            <form class="form form-horizontal" method="post" action="{{ route('admin.setting.bksh.update') }}" enctype="multipart/form-data">
                                @csrf
                                @method('PATCH')
                                <div class="row mb-4">
                                    <label for="sebapay_app_key" class="col-md-3 form-label">SEBAPAY APP KEY</label>
                                    <div class="col-md-9">
                                        <input class="form-control @error('sebapay_app_key') is-invalid @enderror" id="sebapay_app_key"
                                            name="sebapay_app_key" placeholder="Enter your sebapay app key" type="text"
                                            value="{{ env('SEBAPAY_APP_KEY') ?? '' }}">
                                        @error('sebapay_app_key')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <label for="sebapay_secret_key" class="col-md-3 form-label">SEBAPAY SECRET KEY</label>
                                    <div class="col-md-9">
                                        <input class="form-control @error('sebapay_secret_key') is-invalid @enderror" id="sebapay_secret_key"
                                            name="sebapay_secret_key" placeholder="Enter your sebapay secret key" type="text"
                                            value="{{ env('SEBAPAY_SECRET_KEY') ?? '' }}">
                                        @error('sebapay_secret_key')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <label for="sebapay_host_name" class="col-md-3 form-label">SEBAPAY HOST NAME</label>
                                    <div class="col-md-9">
                                        <input class="form-control @error('sebapay_host_name') is-invalid @enderror" id="sebapay_host_name"
                                            name="sebapay_host_name" placeholder="Enter your sebapay host name" type="text"
                                            value="{{ env('SEBAPAY_HOST_NAME') ?? '' }}">
                                        @error('sebapay_host_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <label for="bkash_number" class="col-md-3 form-label">BKASH NUMBER</label>
                                    <div class="col-md-9">
                                        <input class="form-control @error('bkash_number') is-invalid @enderror" id="bkash_number"
                                            name="bkash_number" placeholder="Enter your bkash number" type="text"
                                            value="{{ env('BKASH_NUMBER') ?? '' }}">
                                        @error('bkash_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <label for="nagad_number" class="col-md-3 form-label">NAGAD NUMBER</label>
                                    <div class="col-md-9">
                                        <input class="form-control @error('nagad_number') is-invalid @enderror" id="nagad_number"
                                            name="nagad_number" placeholder="Enter your nagad number" type="text"
                                            value="{{ env('NAGAD_NUMBER') ?? '' }}">
                                        @error('nagad_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                
                                <div class="row justify-content-end">
                                    <div class="col-sm-9">
                                        <div>
                                            <button class="submit btn btn-primary" type="submit">Submit</button>
                                        </div>
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
<!-- CONTAINER CLOSED -->
@endsection



@push('scripts')
@endpush