@extends('backend.app', ['title' => 'Due Payments'])

@section('content')
<div class="app-content main-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <div class="page-header">
                <div>
                    <h1 class="page-title">Due Payments</h1>
                    <p class="text-muted mb-0">Review customer dues and record received payments.</p>
                </div>
                <div class="ms-auto pageheader-btn">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary"><i class="fe fe-arrow-left"></i> Dashboard</a>
                </div>
            </div>

            <div class="card">
                <div class="card-body table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Paid</th>
                                <th>Due</th>
                                <th>Update Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                @php($detail = $order->orderDetail)
                                <tr>
                                    <td>{{ $order->order_number }}</td>
                                    <td>{{ $order->customer->name ?? 'N/A' }}<br><small>{{ $order->customer->phone ?? '' }}</small></td>
                                    <td>৳{{ number_format((float) $detail->total, 2) }}</td>
                                    <td>৳{{ number_format((float) $detail->advance, 2) }}</td>
                                    <td class="fw-bold text-danger">৳{{ number_format((float) $detail->due, 2) }}</td>
                                    <td>
                                        <form method="POST" action="{{ route('admin.order.payment.update', $order->id) }}" class="payment-form d-flex gap-2">
                                            @csrf
                                            <input type="number" name="advance" class="form-control" min="0" max="{{ $detail->total }}" step="0.01" value="{{ $detail->advance }}" required>
                                            <button type="submit" class="btn btn-success text-nowrap"><i class="fe fe-save"></i> Update</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-success py-4">No outstanding dues.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).on('submit', '.payment-form', function (event) {
    event.preventDefault();
    const form = $(this);
    const button = form.find('button');
    button.prop('disabled', true);

    $.post(form.attr('action'), form.serialize())
        .done(function (response) {
            toastr.success(response.message);
            window.location.reload();
        })
        .fail(function (xhr) {
            toastr.error(xhr.responseJSON?.message || 'Payment update failed.');
            button.prop('disabled', false);
        });
});
</script>
@endpush
