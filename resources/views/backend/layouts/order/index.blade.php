@extends('backend.app', ['title' => 'Order List'])

@push('styles')
<link href="{{ asset('default/datatable.css') }}" rel="stylesheet" />  
<style>
    .status-select {
        font-weight: 600;
        font-size: 0.8125rem;
        border-radius: 20px;
        padding: 0.35rem 1.8rem 0.35rem 0.85rem;
        cursor: pointer;
        transition: all 0.25s ease-in-out;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        text-transform: capitalize;
    }

    .status-select:focus {
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
    }

    /* Professional status color themes */
    .status-pending {
        background-color: #fff8dd !important;
        color: #b58105 !important;
        border: 1px solid #f1bc00 !important;
    }

    .status-processing {
        background-color: #e0f2fe !important;
        color: #0369a1 !important;
        border: 1px solid #bae6fd !important;
    }

    .status-completed {
        background-color: #e8fff3 !important;
        color: #50cd89 !important;
        border: 1px solid #a3edd0 !important;
    }

    .status-canceled {
        background-color: #fff5f8 !important;
        color: #f1416c !important;
        border: 1px solid #fdd7e4 !important;
    }

    /* Loading state */
    .status-select-wrapper.is-loading .status-select {
        color: transparent !important;
        opacity: 0.6;
        pointer-events: none;
    }

    .status-select-wrapper.is-loading .status-spinner {
        display: block !important;
    }
</style>
@endpush


@section('content')
<!--app-content open-->
<div class="app-content main-content mt-0">
    <div class="side-app">

        <!-- CONTAINER -->
        <div class="main-container container-fluid">


            <!-- PAGE-HEADER -->
            <div class="page-header">
                <div>
                    <h1 class="page-title">Order List</h1>
                </div>
                <div class="ms-auto pageheader-btn">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Order List</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Index</li>
                    </ol>
                </div>
            </div>
            <!-- PAGE-HEADER END -->

            <!-- ROW-4 -->
            <div class="row">
                <div class="col-12 col-sm-12">
                    <div class="card product-sales-main">
                        <div class="card-header border-bottom">
                            <h3 class="card-title mb-0">List</h3>
                        </div>
                        <div class="card-body">
                            <div class="">
                                <table class="table table-bordered text-nowrap border-bottom" id="datatable">
                                    <thead>
                                        <tr>
                                            <th class="bg-transparent border-bottom-0 wp-15">ID</th>
                                            <th class="bg-transparent border-bottom-0 wp-15">Order Number</th>
                                            <th class="bg-transparent border-bottom-0 wp-15">Customer</th>
                                            <th class="bg-transparent border-bottom-0 wp-15">Reciver</th>
                                            <th class="bg-transparent border-bottom-0 wp-15">Phone Number</th>
                                           
                                            <th class="bg-transparent border-bottom-0">Status</th>
                                            <th class="bg-transparent border-bottom-0">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div><!-- COL END -->
            </div>
            <!-- ROW-4 END -->

        </div>
    </div>
</div>
<!-- CONTAINER CLOSED -->
@endsection



@push('scripts')
<script>
    $(document).ready(function() {

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            }
        });
        if (!$.fn.DataTable.isDataTable('#datatable')) {
            let dTable = $('#datatable').DataTable({
                order: [],
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                processing: true,
                responsive: true,
                serverSide: true,

                language: {
                    processing: `<div class="text-center">
                        <img src="{{ asset('default/loader.gif') }}" alt="Loader" style="width: 50px;">
                        </div>`
                },

                scroller: {
                    loadingIndicator: false
                },
                pagingType: "full_numbers",
                dom: "<'row justify-content-between table-topbar'<'col-md-4 col-sm-3'l><'col-md-5 col-sm-5 px-0'f>>tipr",
                ajax: {
                    url: "{{ route('admin.order.index') }}",
                    type: "GET",
                },

                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'order_number',
                        name: 'order_number',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'customer',
                        name: 'customer',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'receiver',
                        name: 'receiver',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'phone_number',
                        name: 'phone_number',
                        orderable: true,
                        searchable: true
                    },
                   
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'dt-center text-center'
                    },
                ],
            });
        }
    });

    // Handle Status Change with instant color change & loading state
    $(document).on('change', '.change-status', function() {
        const $select = $(this);
        const $wrapper = $select.closest('.status-select-wrapper');
        const orderId = $select.data('id');
        const newStatus = $select.val();
        const previousStatus = $select.attr('data-previous') || newStatus;

        const statusClasses = {
            'pending': 'status-pending',
            'processing': 'status-processing',
            'completed': 'status-completed',
            'canceled': 'status-canceled'
        };

        // 1. Instant color update
        $select.removeClass('status-pending status-processing status-completed status-canceled');
        if (statusClasses[newStatus]) {
            $select.addClass(statusClasses[newStatus]);
        }

        // 2. Loading state
        $wrapper.addClass('is-loading');
        $select.prop('disabled', true);

        if (typeof NProgress !== 'undefined') {
            NProgress.start();
        }

        let url = "{{ route('admin.order.status', ':id') }}";
        url = url.replace(':id', orderId);

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                status: newStatus
            },
            success: function(resp) {
                // Update previous value reference
                $select.attr('data-previous', newStatus);
                toastr.success((resp && resp.message) ? resp.message : ('Order status updated to ' + newStatus));
            },
            error: function(err) {
                // Revert status and color on error
                $select.val(previousStatus);
                $select.removeClass('status-pending status-processing status-completed status-canceled');
                if (statusClasses[previousStatus]) {
                    $select.addClass(statusClasses[previousStatus]);
                }
                let errorMsg = (err.responseJSON && err.responseJSON.message) ? err.responseJSON.message : 'Failed to update status';
                toastr.error(errorMsg);
            },
            complete: function() {
                // Remove loading state
                $wrapper.removeClass('is-loading');
                $select.prop('disabled', false);

                if (typeof NProgress !== 'undefined') {
                    NProgress.done();
                }
            }
        });
    });


    // delete Confirm
    function showDeleteConfirm(id) {
        event.preventDefault();
        Swal.fire({
            title: 'Are you sure you want to delete this record?',
            text: 'If you delete this, it will be gone forever.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
        }).then((result) => {
            if (result.isConfirmed) {
                deleteItem(id);
            }
        });
    }

    // Delete Button
    function deleteItem(id) {
        if (typeof NProgress !== 'undefined') NProgress.start();
        let url = "{{ route('admin.order.destroy', ':id') }}";
        let csrfToken = '{{ csrf_token() }}';
        $.ajax({
            type: "DELETE",
            url: url.replace(':id', id),
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: function(resp) {
                if (typeof NProgress !== 'undefined') NProgress.done();
                toastr.success(resp.message);
                $('#datatable').DataTable().ajax.reload();
            },
            error: function(error) {
                if (typeof NProgress !== 'undefined') NProgress.done();
                toastr.error(error.message || 'Error occurred');
            }
        });
    }

    function goToOpen(id) {
        let url = "{{ route('admin.order.show', ':id') }}";
        window.location.href = url.replace(':id', id);
    }
</script>
@endpush