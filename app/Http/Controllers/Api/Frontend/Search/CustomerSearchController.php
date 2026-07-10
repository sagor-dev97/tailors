<?php

namespace App\Http\Controllers\Api\Frontend\Search;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderReportResource;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CustomerSearchController extends Controller
{
    // public function SearchCustomerReport(Request $request)
    // {
    //     try {

    //         $query = Order::with(['detail', 'user', 'customer']);

    //         if ($request->filled('phone')) {

    //             $phone = $request->phone;

    //             $query->whereHas('user', function ($q) use ($phone) {
    //                 $q->where('phone_number', $phone);
    //             });
    //         }
    //         if ($request->filled('order_number')) {

    //             $order_number = $request->order_number;

    //             $query->whereHas('user', function ($q) use ($order_number) {
    //                 $q->where('order_number', $order_number);
    //             });
    //         }

    //         // From Date & To Date filter
    //         if ($request->filled('from_date') && $request->filled('to_date')) {

    //             $query->whereBetween('order_date', [
    //                 $request->from_date,
    //                 $request->to_date
    //             ]);
    //         }

    //         // Only From Date
    //         elseif ($request->filled('from_date')) {

    //             $query->whereDate('order_date', '>=', $request->from_date);
    //         }

    //         // Only To Date
    //         elseif ($request->filled('to_date')) {

    //             $query->whereDate('order_date', '<=', $request->to_date);
    //         }

    //         $orders = $query->latest()->get();

    //         if ($orders->isEmpty()) {
    //             return response()->json([
    //                 'status'  => false,
    //                 'code'    => 404,
    //                 'message' => 'No orders found'
    //             ]);
    //         }

    //         return response()->json([
    //             'status' => true,
    //             'code'   => 200,
    //             'data'   => OrderReportResource::collection($orders)
    //         ]);
    //     } catch (\Exception $e) {

    //         return response()->json([
    //             'status'  => false,
    //             'message' => $e->getMessage(),
    //         ]);
    //     }
    // }

    public function SearchCustomerReport(Request $request)
{
    try {

        $query = Order::with(['detail', 'user', 'customer']);

        // Phone Filter
        if ($request->filled('phone')) {

            $phone = $request->phone;

            $query->whereHas('user', function ($q) use ($phone) {
                $q->where('phone_number', $phone);
            });
        }

        // Order Number Filter
        if ($request->filled('order_number')) {

            $query->where('order_number', $request->order_number);
        }
        if ($request->filled('cu_order_id')) {

            $query->where('cu_order_id', $request->cu_order_id);
        }

        // Custom Date Range Filter
        if ($request->filled('from_date') && $request->filled('to_date')) {

            $query->whereBetween('order_date', [
                $request->from_date,
                $request->to_date
            ]);
        }

        // Only From Date
        elseif ($request->filled('from_date')) {

            $query->whereDate('order_date', '>=', $request->from_date);
        }

        // Only To Date
        elseif ($request->filled('to_date')) {

            $query->whereDate('order_date', '<=', $request->to_date);
        }

        // Dynamic Filter
        if ($request->filled('filter')) {

            switch ($request->filter) {

                case 'today':
                    $query->whereDate('order_date', Carbon::today());
                    break;

                case 'yesterday':
                    $query->whereDate('order_date', Carbon::yesterday());
                    break;

                case 'this_month':
                    $query->whereMonth('order_date', Carbon::now()->month)
                          ->whereYear('order_date', Carbon::now()->year);
                    break;

                case 'this_year':
                    $query->whereYear('order_date', Carbon::now()->year);
                    break;
            }
        }

        $orders = $query->latest()->get();

        if ($orders->isEmpty()) {

            return response()->json([
                'status'  => false,
                'code'    => 404,
                'message' => 'No orders found'
            ]);
        }

        return response()->json([
            'status' => true,
            'code'   => 200,
            'data'   => OrderReportResource::collection($orders)
        ]);

    } catch (\Exception $e) {

        return response()->json([
            'status'  => false,
            'message' => $e->getMessage(),
        ]);
    }
}
}
