<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsSender
{
    public function send(string $phone, string $message, int $orderId): bool
    {
        try {
            $phone = preg_replace('/[^0-9]/', '', $phone);
            $apiKey = env('SMS_API_KEY');
            $senderId = env('SMS_SENDER_ID');

            if (empty($apiKey) || empty($senderId)) {
                Log::warning('SMS API credentials missing from environment', [
                    'order_id' => $orderId,
                    'phone' => $phone,
                ]);
                return false;
            }

            $response = Http::timeout(10)->get('https://api.automas.com.bd/smsapiv3', [
                'apikey' => $apiKey,
                'sender' => $senderId,
                'msisdn' => $phone,
                'smstext' => $message,
            ]);

            if (!$response->successful()) {
                Log::warning('SMS API failed', [
                    'order_id' => $orderId,
                    'phone' => $phone,
                    'http_status' => $response->status(),
                ]);
                return false;
            }

            $data = $response->json();
            $status = $data['response'][0]['status'] ?? null;

            Log::info('SMS RESPONSE', [
                'order_id' => $orderId,
                'phone' => $phone,
                'status' => $status,
            ]);

            return (int) $status === 0;
        } catch (\Throwable $e) {
            $error = preg_replace('/([?&]apikey=)[^&\s]+/', '$1***', $e->getMessage());
            Log::error('SMS ERROR', [
                'order_id' => $orderId,
                'phone' => $phone ?? null,
                'error' => $error,
            ]);
            return false;
        }
    }
}
