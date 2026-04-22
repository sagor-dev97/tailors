@extends('backend.app', ['title' => 'Support Details'])

@section('content')
<div class="app-content main-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">

            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h1 class="page-title">Support Message</h1>
                </div>
                <div class="ms-auto pageheader-btn">
                    <a href="{{ route('admin.support.index') }}" class="btn btn-primary btn-sm">
                        ← Back to List
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- LEFT: USER INFO -->
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <div class="avatar avatar-xl mb-3 bg-primary text-white rounded-circle">
                                {{ strtoupper(substr($contact->name, 0, 1)) }}
                            </div>

                            <h4 class="mb-1">{{ $contact->name }}</h4>
                            <p class="text-muted mb-2">{{ $contact->email }}</p>

                            <span class="badge bg-success">Support Message</span>
                        </div>

                        <div class="card-footer text-muted text-center">
                            <small>
                                Submitted on
                                {{ $contact->created_at->format('d M Y, h:i A') }}
                            </small>
                        </div>
                    </div>
                </div>

                <!-- RIGHT: MESSAGE DETAILS -->
                <div class="col-md-8">
                    <div class="card shadow-sm">

                        <div class="card-body">
                            <div class="support-message p-3 rounded bg-light">
                                {!! nl2br(e($contact->message)) !!}
                            </div>
                        </div>

                        <div class="card-footer d-flex justify-content-end gap-2">
                            <button class="btn btn-danger btn-sm" onclick="showDeleteConfirm('{{ $contact->id }}')">
                                <i class="fa fa-trash"></i> Delete
                            </button>
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
    .avatar {
        width: 80px;
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        font-weight: bold;
    }

    .support-message {
        min-height: 150px;
        font-size: 15px;
        line-height: 1.7;
        white-space: pre-line;
    }

</style>
@endpush

@push('scripts')
<script>
    function showDeleteConfirm(id) {
        Swal.fire({
            title: 'Delete this message?'
            , text: 'This action cannot be undone.'
            , icon: 'warning'
            , showCancelButton: true
            , confirmButtonColor: '#d33'
            , confirmButtonText: 'Yes, delete it'
        }).then((result) => {
            if (result.isConfirmed) {
                deleteItem(id);
            }
        });
    }

    function deleteItem(id) {
        let url = "{{ route('admin.support.destroy', ':id') }}";
        $.ajax({
            type: "DELETE"
            , url: url.replace(':id', id)
            , headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
            , success: function(resp) {
                toastr.success(resp.message);
                window.location.href = "{{ route('admin.support.index') }}";
            }
            , error: function() {
                toastr.error('Something went wrong');
            }
        });
    }

</script>
@endpush

