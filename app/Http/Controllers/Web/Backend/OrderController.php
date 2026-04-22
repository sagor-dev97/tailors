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

    // Color mapping
    $colorClass = match ($data->status) {
        'pending' => 'bg-danger text-white',
        'processing' => 'bg-warning text-dark',
        'completed' => 'bg-success text-white',
        'canceled' => 'bg-secondary text-white',
        default => 'bg-light'
    };

    $dropdown = '<select 
                    class="form-select form-select-sm change-status '.$colorClass.'" 
                    data-id="' . $data->id . '">';

    foreach ($statuses as $status) {
        $selected = $data->status === $status ? 'selected' : '';
        $dropdown .= '<option value="' . $status . '" ' . $selected . '>' . ucfirst($status) . '</option>';
    }

    $dropdown .= '</select>';

    return $dropdown;
})

                ->addColumn('customer', function ($data) {
                    return "<a href='" . route('admin.users.show', $data->user_id) . "'>" . $data->user->name . "</a>";
                })
                ->addColumn('action', function ($data) {
                    return '<div class="btn-group btn-group-sm" role="group" aria-label="Basic example">

                                <a href="#" type="button" onclick="goToOpen(' . $data->id . ')" class="btn btn-success fs-14 text-white delete-icn" title="View">
                                    <i class="fe fe-eye"></i>
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
            return response()->json([
                'status' => 't-success',
                'message' => 'Your action was successful!'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 't-error',
                'message' => $e->getMessage(),
            ]);
        }
    }
}
