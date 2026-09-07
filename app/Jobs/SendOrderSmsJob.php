<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\SmsSetting;
use App\Services\SmsSender;

class SendOrderSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public const DELIVERY_READY = 'delivery_ready';
    public const DUE_PAYMENT = 'due_payment';

    public function __construct(public int $orderId, public string $type)
    {
    }

    public function handle(): void
    {
        try {

            $order = Order::with(['customer', 'orderDetail'])->find($this->orderId);

            if (!$order || !$order->customer) {
                Log::warning('Order/customer not found', ['order_id' => $this->orderId]);
                return;
            }

            $sentAtColumn = $this->type === self::DUE_PAYMENT
                ? 'due_sms_last_sent_at'
                : 'delivery_sms_last_sent_at';

            if ($order->{$sentAtColumn} && $order->{$sentAtColumn}->isSameMonth(now())) {
                return;
            }

            $phone = preg_replace('/[^0-9]/', '', $order->sms_phone ?: $order->customer->phone);

            if (strlen($phone) < 10) {
                Log::warning('Invalid phone', [
                    'order_id' => $order->id,
                    'phone' => $phone
                ]);
                return;
            }

            Log::info('SCHEDULED SMS ATTEMPT', [
                'order_id' => $order->id,
                'type' => $this->type,
                'phone' => $phone,
            ]);

            $settings = SmsSetting::first();
            $dueTemplate = $settings->due_sms_template ?? null;
            $deliveryTemplate = $settings->delivery_sms_template ?? null;
            $message = $this->type === self::DUE_PAYMENT
                ? ($dueTemplate
                    ? str_replace(['{customer_name}', '{order_number}', '{due}'], [$order->customer->name, $order->order_number, $order->orderDetail->due], $dueTemplate)
                    : "প্রিয় গ্রাহক, আপনার অর্ডারের বকেয়া {$order->orderDetail->due} টাকা। অনুগ্রহ করে পরিশোধ করুন।\nঢাকা টেইলার্স")
                : ($deliveryTemplate
                    ? str_replace(['{customer_name}', '{order_number}'], [$order->customer->name, $order->order_number], $deliveryTemplate)
                    : 'প্রিয় গ্রাহক, আপনার পোশাক ডেলিভারির জন্য রেডি।\nঢাকা টেইলার্স');

            $smsSent = app(SmsSender::class)->send($phone, $message, $order->id);

            if (!$smsSent) {
                Log::error('SCHEDULED SMS NOT SENT', [
                    'order_id' => $order->id,
                    'type' => $this->type,
                    'phone' => $phone,
                ]);
                $this->release($this->backoff);
                return;
            }

            // ----------------------------
            // ✅ UPDATE ORDER FIELDS HERE
            // ----------------------------

            $order->update([
                $sentAtColumn => now(),
                'schedule_count' => $order->schedule_count + 1,
                'schedule_message_status' => 'sent',
            ]);

            Log::info('SMS SENT SUCCESS', [
                'order_id' => $order->id,
                'phone' => $phone,
                'type' => $this->type,
            ]);

        } catch (\Throwable $e) {

            $error = preg_replace('/([?&]apikey=)[^&\s]+/', '$1***', $e->getMessage());

            Log::error('SMS JOB ERROR', [
                'order_id' => $this->orderId,
                'type' => $this->type,
                'error' => $error,
            ]);

            $this->release($this->backoff);
        }
    }
}