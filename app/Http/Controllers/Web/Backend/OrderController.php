<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\SmsSetting;
use App\Services\SmsSender;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;


class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // $data = Order::with(['details', 'user'])->orderBy('id', 'desc')->get();
        // dd($data);
        if ($request->ajax()) {
            $data = Order::with(['details', 'orderDetail', 'user', 'customer'])->orderBy('id', 'desc')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                // ->addColumn('product', function ($data) {
                //     $title = $data->product->title ? Str::limit($data->product->title, 20) : '-';
                //     return "<a href='" . route('admin.product.show', $data->product_id) . "'>" . $title . "</a>";
                // })
                ->addColumn('customer', function ($data) {
                    return "<a href='" . route('admin.users.show', $data->user_id) . "'>" . $data->user->name . "</a>";
                })
                ->addColumn('phone_number', function ($data) {
                    return "<a href='" . route('admin.users.show', $data->user_id) . "'>" . $data->user->phone_number . "</a>";
                })
            ->addColumn('status', function ($data) {
                $statuses = [
                    'pending' => 'Pending',
                    'processing' => 'Processing',
                    'completed' => 'Completed',
                    'canceled' => 'Canceled',
                    'in_courier' => 'Shipped to Courier',
                    'courier_payment_not' => 'Courier, Payment Due',
                    'payment_not' => 'Payment Due',
                ];

                $colorClass = match ($data->status) {
                    'pending' => 'status-pending',
                    'processing' => 'status-processing',
                    'completed' => 'status-completed',
                    'canceled' => 'status-canceled',
                    'in_courier' => 'status-curier',
                    'courier_payment_not' => 'status-curier-not-payment',
                    'payment_not' => 'delivered-not-payment',
                    default => 'status-pending'
                };

                $dropdown = '<div class="status-select-wrapper position-relative d-inline-block" style="min-width: 130px;">';
                $dropdown .= '<select class="form-select form-select-sm status-select change-status ' . $colorClass . '" data-id="' . $data->id . '" data-previous="' . $data->status . '">';

                foreach ($statuses as $status => $label) {
                    $selected = $data->status === $status ? 'selected' : '';
                    $dropdown .= '<option value="' . $status . '" ' . $selected . '>' . $label . '</option>';
                }

                $dropdown .= '</select>';
                $dropdown .= '<div class="status-spinner spinner-border spinner-border-sm text-primary position-absolute top-50 start-50 translate-middle d-none" style="width: 1.1rem; height: 1.1rem; z-index: 5;" role="status"><span class="visually-hidden">Loading...</span></div>';
                $dropdown .= '</div>';

                return $dropdown;
            })

                ->addColumn('customer', function ($data) {
                    return "<a href='" . route('admin.users.show', $data->user_id) . "'>" . $data->user->name . "</a>";
                })
                ->addColumn('action', function ($data) {
                    $detail = $data->orderDetail;
                    $due = (float) ($detail->due ?? 0);
                    $dueButton = $due > 0
                        ? '<button type="button" class="btn btn-warning text-dark update-due" data-id="' . $data->id . '" data-total="' . ($detail->total ?? 0) . '" data-paid="' . ($detail->advance ?? 0) . '" data-due="' . $due . '" title="Update due payment"><i class="fe fe-dollar-sign"></i></button>'
                        : '<button type="button" class="btn btn-secondary" disabled title="No due"><i class="fe fe-check"></i></button>';

                    return '<div class="btn-group btn-group-sm" role="group" aria-label="Basic example">
                                <a href="#" type="button" onclick="goToOpen(' . $data->id . ')" class="btn btn-success fs-14 text-white" title="View">
                                    <i class="fe fe-eye"></i>
                                </a>
                                ' . $dueButton . '
                                <a href="#" type="button" onclick="showDeleteConfirm(' . $data->id . ')" class="btn btn-danger fs-14 text-white ms-1" title="Delete">
                                    <i class="fe fe-trash"></i>
                                </a>
                            </div>';
                })
                ->rawColumns(['customer', 'phone_number', 'status', 'action'])
                ->make();
        }
        return view("backend.layouts.order.index");
    }

    public function show(int $id)
    {
        $order = Order::with(['details', 'orderDetail', 'user', 'customer'])->where('id', $id)->first();
        return view('backend.layouts.order.show', compact('order'));
    }

    public function dueIndex()
    {
        $orders = Order::with(['orderDetail', 'customer'])
            ->whereHas('orderDetail', fn ($query) => $query->where('due', '>', 0))
            ->where('status', '!=', 'canceled')
            ->latest()
            ->get();

        return view('backend.layouts.order.due', compact('orders'));
    }

    // public function status(int $id): JsonResponse
    // {
    //     $data = Order::findOrFail($id);
    //     if (!$data) {
    //         return response()->json([
    //             'status' => 't-error',
    //             'message' => 'Item not found.',
    //         ]);
    //     }
    //     $data->status = $data->status === 'accept' ? 'reject' : 'accept';
    //     $data->save();
    //     return response()->json([
    //         'status' => 't-success',
    //         'message' => 'Your action was successful!',
    //     ]);
    // }

    public function status(Request $request, int $id): JsonResponse
    {
        $allowedStatuses = ['pending', 'processing', 'completed', 'canceled', 'in_courier', 'courier_payment_not', 'payment_not'];
        $request->validate(['status' => ['required', 'string', 'in:' . implode(',', $allowedStatuses)]]);

        $order = Order::with(['customer', 'orderDetail'])->findOrFail($id);
        if ($order->status === $request->status) {
            return response()->json(['status' => true, 'message' => 'Order status is already up to date.']);
        }

        $order->update(['status' => $request->status]);
        $smsWarning = $this->sendStatusChangeSms($order, $request->status);

        return response()->json(['status' => true, 'message' => $smsWarning ?? 'Order status updated and SMS sent.']);
    }

    /** Send one SMS after an actual order-status change. */
    private function sendStatusChangeSms(Order $order, string $status): ?string
    {
        $settings = SmsSetting::first();
        if (!$settings || !$settings->service_status) {
            return 'Order status updated. SMS service is not enabled.';
        }

        $phone = $order->sms_phone ?: $order->customer?->phone;
        if (!$phone) {
            return 'Order status updated, but customer phone number was not found.';
        }

        $statusLabels = [
            'pending' => 'অপেক্ষমান',
            'processing' => 'প্রক্রিয়াধীন',
            'shipped' => 'পাঠানো হয়েছে',
            'delivered' => 'ডেলিভারি হয়েছে',
            'completed' => 'সম্পন্ন হয়েছে',
            'canceled' => 'বাতিল করা হয়েছে',
            'cancelled' => 'বাতিল করা হয়েছে',
            'ready' => 'ডেলিভারির জন্য রেডি',
            'in_courier' => 'কুরিয়ার সার্ভিসে ডেলিভারি দেওয়া হয়েছে',
            'courier_payment_not' => 'ডেলিভারি দেওয়া হয়েছে টাকা কিন্তু বাকি আছে',
            'payment_not' => 'টাকা বাকি আছে',
        ];
        $defaultTemplates = [
            'pending' => 'Dear {customer_name}, your order {order_number} is pending.',
            'processing' => 'Dear {customer_name}, your order {order_number} is being processed.',
            'completed' => 'Dear {customer_name}, your order {order_number} is completed.',
            'canceled' => 'Dear {customer_name}, your order {order_number} has been canceled.',
            'in_courier' => 'Dear {customer_name}, your order {order_number} has been shipped to the courier.',
            'courier_payment_not' => 'Dear {customer_name}, your order {order_number} has been shipped to the courier. Payment is due: {due}.',
            'payment_not' => 'Dear {customer_name}, payment is due for your order {order_number}. Due amount: {due}.',
        ];

        $templates = $settings->templates_json ?? [];
        $template = $templates[$status] ?? $templates['default'] ?? $defaultTemplates[$status];
        $detail = $order->orderDetail;
        $message = str_replace(
            ['{company}', '{order_number}', '{status}', '{bangla_status}', '{customer_name}', '{customer_phone}', '{order_date}', '{total_amount}', '{payment_method}', '{delivery_address}', '{due}'],
            [$settings->sender ?? config('app.name'), $order->order_number ?? $order->id, $status, $settings->status_labels[$status] ?? $statusLabels[$status], $order->customer?->name ?? 'Customer', $phone, optional($order->created_at)->format('d/m/Y') ?? now()->format('d/m/Y'), $detail?->total ?? 0, $order->payment_method ?? 'Cash On Delivery', $order->delivery_address ?? '', $detail?->due ?? 0],
            strip_tags($template)
        );

        return app(SmsSender::class)->send($phone, $message, $order->id)
            ? null
            : 'Order status updated, but SMS sending failed.';
    }
    public function updatePayment(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'advance' => ['nullable', 'numeric', 'min:0'],
            'payment_amount' => ['nullable', 'numeric', 'min:0'],
        ]);

        $order = Order::with('orderDetail')->findOrFail($id);
        $orderDetail = $order->orderDetail;

        if (!$orderDetail) {
            return response()->json([
                'status' => false,
                'message' => 'Order details not found.',
            ], 422);
        }

        $total = (float) $orderDetail->total;
        $currentAdvance = (float) $orderDetail->advance;
        $currentDue = max(0, $total - $currentAdvance);

        if ($request->filled('payment_amount')) {
            $paymentAmount = (float) $request->input('payment_amount');

            if ($paymentAmount > $currentDue) {
                return response()->json([
                    'status' => false,
                    'message' => 'Payment cannot be greater than current due amount.',
                ], 422);
            }

            $advance = $currentAdvance + $paymentAmount;
        } else {
            // Backward-compatible: advance is treated as the final paid total.
            $advance = (float) $request->input('advance');
        }

        if ($advance > $total) {
            return response()->json([
                'status' => false,
                'message' => 'Advance cannot be greater than total amount.',
            ], 422);
        }

        $due = $total - $advance;

        $orderDetail->update([
            'advance' => $advance,
            'due' => $due,
        ]);

        $order->update([
            'payment_status' => $due <= 0 ? 'paid' : 'unpaid',
            'payment_paid_at' => $due <= 0 ? now() : null,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Payment and due updated successfully.',
            'data' => [
                'total' => $total,
                'advance' => $advance,
                'due' => $due,
                'payment_status' => $order->payment_status,
            ],
        ]);
    }

    public function destroy(string $id)
    {
        try {
            $data = Order::findOrFail($id);
            if ($data->details()) {
                $data->details()->delete();
            }
            $data->delete();
            return response()->json([
                'status' => 't-success',
                'message' => 'Order deleted successfully!'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 't-error',
                'message' => $e->getMessage(),
            ]);
        }
    }
}
