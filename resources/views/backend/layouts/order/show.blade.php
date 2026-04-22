@extends('backend.app', ['title' => 'Show Order'])

@section('content')
<div class="app-content main-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">

            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h1 class="page-title">Order Details</h1>
                </div>
                <div class="ms-auto pageheader-btn">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.order.index') }}">Orders</a></li>
                        <li class="breadcrumb-item active" aria-current="page">#{{ $order->order_number }}</li>
                    </ol>
                </div>
            </div>

            <!-- Order Overview Card -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header border-bottom-0">
                            <h3 class="card-title">
                                <i class="fe fe-shopping-bag me-2"></i>Order #{{ $order->order_number }}
                            </h3>
                            <div class="card-options">
                                <a href="javascript:window.history.back()" class="btn btn-sm btn-outline-primary me-2">
                                    <i class="fe fe-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Receiver:</strong> {{ $order->receiver }}</p>
                                    <p><strong>Order Date:</strong> {{ $order->order_date }}</p>
                                    <p><strong>Delivery Date:</strong> {{ $order->delivery_date }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Status:</strong> 
                                        <span class="badge bg-warning fs-6">{{ ucfirst($order->status) }}</span>
                                    </p>
                                    <p><strong>Created:</strong> {{ $order->created_at->format('d M Y, h:i A') }}</p>
                                    <p><strong>Last Updated:</strong> {{ $order->updated_at->format('d M Y, h:i A') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Info Card -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title"><i class="fe fe-user me-2"></i>Customer Information</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4"><strong>Name:</strong> {{ $order->user->name ?? 'N/A' }}</div>
                                <div class="col-md-4"><strong>Phone:</strong> {{ $order->user->phone_number ?? 'N/A' }}</div>
                                <div class="col-md-4"><strong>Address:</strong> {{ $order->user->address ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @php
                $detail = $order->details->first();

                function yesNo($value){
                    return $value 
                        ? '<span class="badge bg-success"><i class="fe fe-check"></i> Yes</span>' 
                        : '<span class="badge bg-danger"><i class="fe fe-x"></i> No</span>';
                }
            @endphp

            @if($detail)
            <!-- Design & Style Options (Two‑Column Grid) -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title"><i class="fe fe-layers me-2"></i>Design & Style Options</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @php
                                    $booleanFields = [
                                        'single_hand_punjabi' => 'Single Hand Punjabi',
                                        'double_hand_punjabi' => 'Double Hand Punjabi',
                                        'punjabi' => 'Punjabi',
                                        'arabian_jubba' => 'Arabian Jubba',
                                        'kabli' => 'Kabli',
                                        'fatwa' => 'Fatwa',
                                        'salwar' => 'Salwar',
                                        'pajama' => 'Pajama',
                                        'punjabi_pajama' => 'Punjabi Pajama',
                                        'chest_pocket' => 'Chest Pocket',
                                        'collar_button' => 'Collar Button',
                                        'double_stitch' => 'Double Stitch',
                                        'front_button' => 'Front Button',
                                        'side_cut' => 'Side Cut',
                                        'back_pocket' => 'Back Pocket',
                                        'front_button_pocket' => 'Front Button Pocket',
                                        'single_pocket_design' => 'Single Pocket Design',
                                        'double_pocket_design' => 'Double Pocket Design',
                                    ];
                                @endphp

                                @foreach($booleanFields as $field => $label)
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex align-items-center">
                                            <span class="w-50 fw-semibold">{{ $label }}:</span>
                                            <span class="ms-2">{!! yesNo($detail->$field) !!}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Measurements Card -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title"><i class="fe fe-ruler me-2"></i>Measurements</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @php
                                    $measurementFields = [
                                        'length' => 'Length',
                                        'body' => 'Body',
                                        'belly' => 'Belly',
                                        'sleeves' => 'Sleeves',
                                        'neck' => 'Neck',
                                        'shoulder' => 'Shoulder',
                                        'cuff' => 'Cuff',
                                        'hip' => 'Hip',
                                        'bottom_length' => 'Bottom Length',
                                        'natural' => 'Natural',
                                        'waist' => 'Waist',
                                        'hi' => 'Hi',
                                        'run' => 'Run',
                                    ];
                                @endphp

                                @foreach($measurementFields as $field => $label)
                                    <div class="col-md-4 mb-3">
                                        <strong>{{ $label }}:</strong> {{ $detail->$field ?? '—' }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cost Breakdown Card (Compact Table) -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title"><i class="fe fe-dollar-sign me-2"></i>Cost Breakdown</h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Item</th>
                                        <th>Qty</th>
                                        <th>Price</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(['fabric', 'labor', 'design', 'button', 'embroidery', 'courier'] as $item)
                                    <tr>
                                        <td>{{ ucfirst($item) }}</td>
                                        <td>{{ $detail->{$item.'_qty'} }}</td>
                                        <td>{{ number_format($detail->{$item.'_price'}, 2) }}</td>
                                        <td>{{ number_format($detail->{$item.'_qty'} * $detail->{$item.'_price'}, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="3" class="text-end">Total</th>
                                        <th>{{ number_format($detail->total, 2) }}</th>
                                    </tr>
                                    <tr>
                                        <th colspan="3" class="text-end">Advance</th>
                                        <td>{{ number_format($detail->advance, 2) }}</td>
                                    </tr>
                                    <tr class="table-danger">
                                        <th colspan="3" class="text-end">Due</th>
                                        <th>{{ number_format($detail->due, 2) }}</th>
                                    </tr>
                                </tfoot>
                            </table>

                            @if($detail->note)
                            <div class="alert alert-info mt-3 mb-0">
                                <strong>Note:</strong> {{ $detail->note }}
                            </div>
                            @endif
                        </div>
                        <div class="card-footer text-muted">
                            <small>Created: {{ $detail->created_at->format('d M Y, h:i A') }} | Last updated: {{ $detail->updated_at->format('d M Y, h:i A') }}</small>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Action Buttons -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body d-flex gap-2">
                            <button class="btn btn-danger" onclick="showDeleteConfirm('{{ $order->id }}')">
                                <i class="fe fe-trash-2"></i> Delete Order
                            </button>
                            <button class="btn btn-primary" onclick="goToEdit('{{ $order->id }}')">
                                <i class="fe fe-edit"></i> Edit Order
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection


@push('scripts')
<script>

function showDeleteConfirm(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'This order will be permanently deleted.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
    }).then((result) => {
        if (result.isConfirmed) {
            deleteItem(id);
        }
    });
}

function deleteItem(id) {
    let url = "{{ route('admin.order.destroy', ':id') }}";
    let csrfToken = '{{ csrf_token() }}';

    $.ajax({
        type: "DELETE",
        url: url.replace(':id', id),
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        success: function(resp) {
            toastr.success(resp.message);
            window.location.href = "{{ route('admin.order.index') }}";
        }
    });
}



</script>
@endpush
