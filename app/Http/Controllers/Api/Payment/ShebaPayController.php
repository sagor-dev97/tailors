<?php

namespace App\Http\Controllers\Api\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ShebaPayController extends Controller
{
      public function createPayment(Request $request)
    {
        $request->validate([
            'cus_name'  => 'required|string',
            'cus_email' => 'required|email',
            'amount'    => 'required'
        ]);

        try {

            $response = Http::withHeaders([
                'app-key'    => env('SEBAPAY_APP_KEY'),
                'secret-key' => env('SEBAPAY_SECRET_KEY'),
                'host-name'  => env('SEBAPAY_HOST_NAME'),
            ])->asForm()->post(
                'http://pay.sebapay.xyz/request/payment/create',
                [
                    'cus_name'   => $request->cus_name,
                    'cus_email'  => $request->cus_email,
                    'amount'     => $request->amount,

                    // success and cancel url
                    'success_url' => url('/api/sebapay/success'),
                    'cancel_url'  => url('/api/sebapay/cancel'),
                ]
            );

            return response()->json([
                'status' => true,
                'data'   => json_decode($response->body()),
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // SUCCESS URL
    public function success(Request $request)
    {
        return response()->json([
            'message' => 'Payment Success',
            'data' => [
                'transactionId' => $request->transactionId,
                'paid_by'       => $request->paid_by,
                'paymentAmount' => $request->paymentAmount,
                'paymentFee'    => $request->paymentFee,
                'success'       => $request->success,
                'p_type'        => $request->p_type,
            ]
        ]);
    }

    // CANCEL URL
    public function cancel(Request $request)
    {
        return response()->json([
            'message' => 'Payment Failed',
            'data' => [
                'transactionId' => $request->transactionId,
                'paymentAmount' => $request->paymentAmount,
                'paymentFee'    => $request->paymentFee,
                'success'       => $request->success,
            ]
        ]);
    }

    // VERIFY PAYMENT
    public function verifyPayment(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required'
        ]);

        try {

            $response = Http::withHeaders([
                'app-key'    => 'YOUR_API_KEY',
                'secret-key' => 'YOUR_SECRET_KEY',
                'host-name'  => 'YOUR_DOMAIN.COM',
            ])->asForm()->post(
                'http://pay.sebapay.xyz/request/payment/verify',
                [
                    'transaction_id' => $request->transaction_id,
                ]
            );

            return response()->json([
                'status' => true,
                'data'   => json_decode($response->body()),
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
