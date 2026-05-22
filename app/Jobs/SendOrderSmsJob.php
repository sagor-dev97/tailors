<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendOrderSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $orderId;

    public function __construct(Order $orderId)
    {
        $this->order = $orderID;
    }

    public function handle(): void
    {
        try {

            $order = Order::with('customer')->find($this->order->id);

            if (!$order || !$order->customer) {
                Log::warning('Order/customer not found', ['order_id' => $this->order->id]);
                return;
            }

            $phone = preg_replace('/[^0-9]/', '', $order->customer->phone);

            if (strlen($phone) < 10) {
                Log::warning('Invalid phone', [
                    'order_id' => $order->id,
                    'phone' => $phone
                ]);
                return;
            }

            // $message = "Dear {$order->customer->name}, your order #{$order->id} is ready.";
            $message ="প্রিয় গ্রাহক আপনার পোশাক ডেলিভারির জন্য রেডি";

            // SMS API
            $response = Http::timeout(10)
                ->retry(3, 500)
                ->get('https://api.automas.com.bd/smsapiv3', [
                    'apikey'  => env('SMS_API_KEY'),
                    'sender'  => env('SMS_SENDER_ID'),
                    'msisdn'  => $phone,
                    'smstext' => $message,
                ]);

            if (!$response->successful()) {

                Log::error('SMS FAILED', [
                    'order_id' => $order->id,
                    'response' => $response->body()
                ]);

                return;
            }

            // ----------------------------
            // ✅ UPDATE ORDER FIELDS HERE
            // ----------------------------

            $order->increment('schedule_count');

            $order->update([
                'schedule_message_status' => 'sent'
            ]);

            Log::info('SMS SENT SUCCESS', [
                'order_id' => $order->id,
                'phone' => $phone
            ]);

        } catch (\Throwable $e) {

            Log::error('SMS JOB ERROR', [
                'order_id' => $this->order->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}