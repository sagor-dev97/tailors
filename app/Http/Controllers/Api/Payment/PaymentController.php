<?php

namespace App\Http\Controllers\Api\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function pay(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'email'  => 'required|email',
            'phone'  => 'required|string',
            'name'   => 'nullable|string',
        ]);

        $payload = [
            'full_name'     => $request->name ?? 'Customer',
            'email_mobile'  => $request->email,           // email + mobile একসাথে
            'amount'        => $request->amount,
            'currency'      => 'BDT',
            'redirect_url'  => route('payment.success'),
            'cancel_url'    => route('payment.cancel'),
            'webhook_url'   => route('payment.webhook'),   // Uncomment করে রাখলাম
            'return_type'   => 'GET',
            'metadata'      => [
                'phone'     => $request->phone,
                'order_id'  => 'ORD-' . time(),
                'user_id'   => $request->user_id ?? null,
            ]
        ];

        try {
            $baseUrl = rtrim(env('PIPRAPAY_BASE_URL'), '/');

            $response = Http::withHeaders([
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . trim(env('PIPRAPAY_API_KEY')),   // ← Bearer added
            ])->post($baseUrl . '/create-charge', $payload);   // Clean URL ব্যবহার করলাম

            Log::info('PipraPay Request', [
    'sent_key' => 'Bearer ' . substr(env('PIPRAPAY_API_KEY'), 0, 10) . '...',
    'full_key_length' => strlen(env('PIPRAPAY_API_KEY')),
    'base_url' => env('PIPRAPAY_BASE_URL')
]);

            $data = $response->json();

            // API Error Handling
            if (!$response->successful()) {
                return response()->json([
                    'status'      => false,
                    'message'     => 'PipraPay API request failed',
                    'http_status' => $response->status(),
                    'response'    => $data
                ], $response->status());
            }

            // Success Response
            if (isset($data['status']) && $data['status'] === true && isset($data['pp_url'])) {
                return response()->json([
                    'status'       => true,
                    'message'      => 'Payment created successfully',
                    'payment_url'  => $data['pp_url'],
                    'pp_id'        => $data['pp_id'] ?? null,
                    'data'         => $data
                ]);
            }

            // Fallback if pp_url not found
            return response()->json([
                'status'   => false,
                'message'  => 'Failed to create payment link',
                'response' => $data
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Internal server error',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    // Success & Cancel Pages
    public function success(Request $request)
    {
        return response()->json([
            'status'  => true,
            'message' => 'Payment Successful',
            'data'    => $request->all()
        ]);
    }

    public function cancel(Request $request)
    {
        return response()->json([
            'status'  => false,
            'message' => 'Payment Cancelled',
            'data'    => $request->all()
        ]);
    }

    public function webhook(Request $request)
    {
        Log::info('PipraPay Webhook Received:', $request->all());

        // TODO: Verify webhook signature (future security)

        return response()->json([
            'status'  => true,
            'message' => 'Webhook received successfully'
        ]);
    }
}