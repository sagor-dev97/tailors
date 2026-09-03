<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\Order;
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
            $data = Order::with(['details', 'user'])->orderBy('id', 'desc')->get();
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
                $statuses = ['pending', 'processing', 'completed', 'canceled'];

                $colorClass = match ($data->status) {
                    'pending' => 'status-pending',
                    'processing' => 'status-processing',
                    'completed' => 'status-completed',
                    'canceled' => 'status-canceled',
                    default => 'status-pending'
                };

                $dropdown = '<div class="status-select-wrapper position-relative d-inline-block" style="min-width: 130px;">';
                $dropdown .= '<select class="form-select form-select-sm status-select change-status ' . $colorClass . '" data-id="' . $data->id . '" data-previous="' . $data->status . '">';

                foreach ($statuses as $status) {
                    $selected = $data->status === $status ? 'selected' : '';
                    $dropdown .= '<option value="' . $status . '" ' . $selected . '>' . ucfirst($status) . '</option>';
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
                    return '<div class="btn-group btn-group-sm" role="group" aria-label="Basic example">
                                <a href="#" type="button" onclick="goToOpen(' . $data->id . ')" class="btn btn-success fs-14 text-white" title="View">
                                    <i class="fe fe-eye"></i>
                                </a>
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
        $order = Order::with(['details', 'user'])->where('id', $id)->first();
        return view('backend.layouts.order.show', compact('order'));
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

    public function status(Request $request, $id)
{
    $order = Order::findOrFail($id);
    $order->status = $request->status;
    $order->save();

    return response()->json(['status' => true, 'message' => 'Order status updated']);
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
