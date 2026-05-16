<?php

namespace App\Http\Controllers\Api\Frontend\Search;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Http\Resources\OrderReportResource;

class CustomerSearchController extends Controller
{
   public function SearchCustomerReport(Request $request)
{
    try {

        $query = Order::with(['detail', 'user', 'customer']);

        // Phone filter
        if ($request->filled('phone')) {

            $phone = $request->phone;

            $query->whereHas('user', function ($q) use ($phone) {
                $q->where('phone_number', $phone);
            });
        }

        // From Date & To Date filter
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
