<?php

namespace App\Http\Controllers\Web\Backend;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $selectedYear = request()->query('year', now()->year);
        $fromDate = request()->query('from_date');
        $toDate = request()->query('to_date');

        if ($fromDate && $toDate) {
            try {
                $from = Carbon::parse($fromDate)->startOfDay();
                $to = Carbon::parse($toDate)->endOfDay();
            } catch (\Exception $e) {
                $from = Carbon::now()->startOfYear();
                $to = Carbon::now()->endOfYear();
            }
        } else {
            $from = Carbon::createFromDate($selectedYear, 1, 1)->startOfDay();
            $to = Carbon::createFromDate($selectedYear, 12, 31)->endOfDay();
        }

        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $monthlyTotals = array_fill(0, 12, 0);
        $monthlyCompleted = array_fill(0, 12, 0);
        $monthlyPending = array_fill(0, 12, 0);
        $monthlyCompletedAmount = array_fill(0, 12, 0);
        $monthlyPendingAmount = array_fill(0, 12, 0);
        $monthlyAmount = array_fill(0, 12, 0);

        $report = DB::table('orders')
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->select(
                DB::raw('MONTH(orders.order_date) as month'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw("SUM(CASE WHEN orders.status = 'completed' THEN 1 ELSE 0 END) as completed_orders"),
                DB::raw("SUM(CASE WHEN orders.status = 'pending' THEN 1 ELSE 0 END) as pending_orders"),
                DB::raw("SUM(CASE WHEN orders.status = 'delivered' THEN order_details.total ELSE 0 END) as completed_amount"),
                DB::raw("SUM(CASE WHEN orders.status = 'pending' THEN order_details.total ELSE 0 END) as pending_amount"),
                DB::raw('SUM(order_details.total) as total_amount')
            )
            ->whereBetween('orders.order_date', [$from->toDateString(), $to->toDateString()])
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        foreach ($report as $row) {
            $index = max(0, min(11, $row->month - 1));
            $monthlyTotals[$index] = (int) $row->total_orders;
            $monthlyCompleted[$index] = (int) $row->completed_orders;
            $monthlyPending[$index] = (int) $row->pending_orders;
            $monthlyCompletedAmount[$index] = (float) $row->completed_amount;
            $monthlyPendingAmount[$index] = (float) $row->pending_amount;
            $monthlyAmount[$index] = (float) $row->total_amount;
        }

        $summary = [
            'total_orders' => array_sum($monthlyTotals),
            'completed_orders' => array_sum($monthlyCompleted),
            'pending_orders' => array_sum($monthlyPending),
            'completed_amount' => array_sum($monthlyCompletedAmount),
            'pending_amount' => array_sum($monthlyPendingAmount),
            'total_amount' => array_sum($monthlyAmount),
        ];

        $years = range(now()->year, now()->year - 4);

        return view('backend.layouts.dashboard', compact(
            'months',
            'monthlyTotals',
            'monthlyCompleted',
            'monthlyPending',
            'summary',
            'selectedYear',
            'years',
            'fromDate',
            'toDate'
        ));
    }
}
