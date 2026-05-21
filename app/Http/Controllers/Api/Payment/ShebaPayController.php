<?php

namespace App\Http\Controllers\Api\Payment;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ShebaPayController extends Controller
{
    // public function createPayment(Request $request)
    // {
    //     $request->validate([
    //         'cus_name'  => 'required|string',
    //         'cus_email' => 'required|email',
    //         'amount'    => 'required'
    //     ]);

    //     try {

    //         $response = Http::withHeaders([
    //             'app-key'    => env('SEBAPAY_APP_KEY'),
    //             'secret-key' => env('SEBAPAY_SECRET_KEY'),
    //             'host-name'  => env('SEBAPAY_HOST_NAME'),
    //         ])->asForm()->post(
    //             'http://pay.sebapay.xyz/request/payment/create',
    //             [
    //                 'cus_name'   => $request->cus_name,
    //                 'cus_email'  => $request->cus_email,
    //                 'amount'     => $request->amount,

    //                 // success and cancel url
    //                 'success_url' => url('/api/sebapay/success'),
    //                 'cancel_url'  => url('/api/sebapay/cancel'),
    //             ]
    //         );

    //         return response()->json([
    //             'status' => true,
    //             'data'   => json_decode($response->body()),
    //         ]);
    //     } catch (\Exception $e) {

    //         return response()->json([
    //             'status'  => false,
    //             'message' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    // public function createPayment(Request $request)
    // {
    //     $request->validate([
    //         'order_id' => 'required'
    //     ]);

    //     $order = Order::with('customer')->findOrFail($request->order_id);

    //     // only accepted order can pay
    //     if ($order->status != 'accept') {

    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Order not approved yet'
    //         ]);
    //     }

    //     // already paid check
    //     if ($order->payment_status == 'paid') {

    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Already paid'
    //         ]);
    //     }

    //     $response = Http::withHeaders([
    //         'app-key'    => env('SEBAPAY_APP_KEY'),
    //         'secret-key' => env('SEBAPAY_SECRET_KEY'),
    //         'host-name'  => env('SEBAPAY_HOST_NAME'),
    //     ])->asForm()->post(
    //         'https://pay.sebapay.xyz/request/payment/create',
    //         [
    //             'cus_name'   => $order->customer->name,
    //             'cus_email'  => 'customer@gmail.com',

    //             // amount from order
    //             'amount'     => $order->total,

    //             'success_url' => url('/api/sebapay/success?order_id=' . $order->id),

    //             'cancel_url'  => url('/api/sebapay/cancel?order_id=' . $order->id),
    //         ]
    //     );

    //     return response()->json([
    //         'status' => true,
    //         'data'   => $response->json(),
    //     ]);
    // }
    // SUCCESS URL
    // public function success(Request $request)
    // {
    //     return response()->json([
    //         'message' => 'Payment Success',
    //         'data' => [
    //             'transactionId' => $request->transactionId,
    //             'paid_by'       => $request->paid_by,
    //             'paymentAmount' => $request->paymentAmount,
    //             'paymentFee'    => $request->paymentFee,
    //             'success'       => $request->success,
    //             'p_type'        => $request->p_type,
    //         ]
    //     ]);
    // }


    // public function success(Request $request)
    // {
    //     $transactionId = $request->transactionId;

    //     $orderId = $request->order_id;

    //     // verify payment

    //     $verifyResponse = Http::withHeaders([
    //         'app-key'    => env('SEBAPAY_APP_KEY'),
    //         'secret-key' => env('SEBAPAY_SECRET_KEY'),
    //         'host-name'  => env('SEBAPAY_HOST_NAME'),
    //     ])->asForm()->post(
    //         'https://pay.sebapay.xyz/request/payment/verify',
    //         [
    //             'transaction_id' => $transactionId,
    //         ]
    //     );

    //     $verify = $verifyResponse->json();

    //     if (isset($verify['status']) && $verify['status'] == 1) {

    //         Order::where('id', $orderId)->update([

    //             'payment_status' => 'paid',

    //             'transaction_id' => $transactionId,
    //         ]);

    //         return redirect('https://your-react-site.com/payment-success');
    //     }

    //     return redirect('https://your-react-site.com/payment-failed');
    // }

    // // CANCEL URL
    // public function cancel(Request $request)
    // {
    //     return response()->json([
    //         'message' => 'Payment Failed',
    //         'data' => [
    //             'transactionId' => $request->transactionId,
    //             'paymentAmount' => $request->paymentAmount,
    //             'paymentFee'    => $request->paymentFee,
    //             'success'       => $request->success,
    //         ]
    //     ]);
    // }

    // VERIFY PAYMENT
    // public function verifyPayment(Request $request)
    // {
    //     $request->validate([
    //         'transaction_id' => 'required'
    //     ]);

    //     try {

    //         $response = Http::withHeaders([
    //             'app-key'    => 'YOUR_API_KEY',
    //             'secret-key' => 'YOUR_SECRET_KEY',
    //             'host-name'  => 'YOUR_DOMAIN.COM',
    //         ])->asForm()->post(
    //             'http://pay.sebapay.xyz/request/payment/verify',
    //             [
    //                 'transaction_id' => $request->transaction_id,
    //             ]
    //         );

    //         return response()->json([
    //             'status' => true,
    //             'data'   => json_decode($response->body()),
    //         ]);
    //     } catch (\Exception $e) {

    //         return response()->json([
    //             'status'  => false,
    //             'message' => $e->getMessage()
    //         ], 500);
    //     }
    // }




    /**
     * CREATE PAYMENT
     */
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

        // CREATE PAYMENT (DOC FIXED: multipart/form-data)
        // $response = Http::withHeaders([
        //     'app-key'    => env('SEBAPAY_APP_KEY'),
        //     'secret-key' => env('SEBAPAY_SECRET_KEY'),
        //     'host-name'  => env('SEBAPAY_HOST_NAME'),
        //     'Content-Type' => 'application/x-www-form-urlencoded',
        // ])->asMultipart()->post(
        //     'http://pay.sebapay.xyz/request/payment/create',
        //     [
        //         [
        //             'name' => 'cus_name',
        //             'contents' => $order->customer->name
        //         ],
        //         [
        //             'name' => 'cus_email',
        //             'contents' => $order->customer->email ?? 'test@gmail.com'
        //         ],
        //         [
        //             'name' => 'amount',
        //             'contents' => $amount
        //         ],
        //         [
        //             'name' => 'success_url',
        //             'contents' => url('/api/sebapay/success?order_id=' . $order->id)
        //         ],
        //         [
        //             'name' => 'cancel_url',
        //             'contents' => url('/api/sebapay/cancel?order_id=' . $order->id)
        //         ],
        //     ]
        // );

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

    /**
     * SUCCESS URL (AUTO REDIRECT FROM SEBAPAY)
     */
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
    /**
     * CANCEL
     */
    public function cancel(Request $request)
    {
        Order::where('id', $request->order_id)->update([
            'payment_status' => 'unpaid',
        ]);

        return redirect('https://your-react-site.com/payment-failed');
    }

    /**
     * VERIFY API (MANUAL TEST)
     */
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
