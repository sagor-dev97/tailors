<?php

namespace App\Http\Controllers\Api\Payment;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ShebaPayController extends Controller
{
    public function createPayment(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        $order = Order::with(['customer', 'detail'])
            ->findOrFail($request->order_id);

        if ($order->order_status != 'accept') {
            return response()->json([
                'status' => false,
                'message' => 'Order not approved yet',
            ]);
        }
        if ($order->payment_status == 'paid') {
            return response()->json([
                'status' => false,
                'message' => 'Already paid',
            ]);
        }

        $amount = $order->total_amount ?? 0;

        if (!$amount) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid amount',
            ]);
        }

        $response = Http::withHeaders([
            'app-key'    => env('SEBAPAY_APP_KEY'),
            'secret-key' => env('SEBAPAY_SECRET_KEY'),
            'host-name'  => env('SEBAPAY_HOST_NAME'),
        ])->asForm()->post(
            'https://pay.sebapay.xyz/request/payment/create',
            [
                'cus_name'   => $order->customer->name,
                'cus_email'  => $order->customer->email ?? 'test@gmail.com',
                'amount'     => $amount,

                'success_url' => url('/api/sebapay/success?order_id=' . $order->id),
                'cancel_url'  => url('/api/sebapay/cancel?order_id=' . $order->id),
            ]
        );

        $responseData = $response->json();

        // Store payment transaction ID for later verification
        if (isset($responseData['transactionId'])) {
            $order->update([
                'transaction_id' => $responseData['transactionId']
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => $responseData,
            'raw'  => $response->body(),
        ]);
    }

    
    public function success(Request $request)
    {
        $orderId = $request->order_id;

        if (!$orderId) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid success callback - missing order_id',
            ]);
        }

        // Get order and transaction ID from database
        $order = Order::findOrFail($orderId);
        $transactionId = $order->transaction_id;

        if (!$transactionId) {
            return response()->json([
                'status' => false,
                'message' => 'Transaction ID not found',
            ]);
        }

        // VERIFY (DOC FIXED)
        $verify = Http::withHeaders([
            'app-key'    => env('SEBAPAY_APP_KEY'),
            'secret-key' => env('SEBAPAY_SECRET_KEY'),
            'host-name'  => env('SEBAPAY_HOST_NAME'),
        ])->asForm()->post(
            'https://pay.sebapay.xyz/request/payment/verify',
            [
                'transactionId' => $transactionId,
            ]
        )->json();

        if (($verify['status'] ?? 0) == 1) {

            $order->update([
                'payment_status' => 'paid',
                'status' => 'payment_done',
                'transaction_id' => $transactionId,
            ]);

            return redirect('https://your-react-site.com/payment-success');
        }

        return redirect('https://your-react-site.com/payment-failed');
    }
   
    public function cancel(Request $request)
    {
        Order::where('id', $request->order_id)->update([
            'payment_status' => 'unpaid',
        ]);

        return redirect('https://your-react-site.com/payment-failed');
    }


    public function verifyPayment(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required'
        ]);

        $response = Http::withHeaders([
            'app-key'    => env('SEBAPAY_APP_KEY'),
            'secret-key' => env('SEBAPAY_SECRET_KEY'),
            'host-name'  => env('SEBAPAY_HOST_NAME'),
        ])->asForm()->post(
            'https://pay.sebapay.xyz/request/payment/verify',
            [
                'transaction_id' => $request->transaction_id
            ]
        );

        return response()->json([
            'status' => true,
            'data' => $response->json(),
            'raw' => $response->body(),
        ]);
    }
}
