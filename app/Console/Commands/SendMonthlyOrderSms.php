<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Jobs\SendOrderSmsJob;
use App\Models\SmsSetting;
use Illuminate\Support\Facades\Log;

class SendMonthlyOrderSms extends Command
{
    protected $signature = 'sms:monthly-orders {--force : Run even when today is not a configured schedule day} {--dry-run : Show matching orders without dispatching SMS jobs}';
    protected $description = 'Send scheduled delivery-ready and due-payment SMS';

    public function handle(): int
    {
        $settings = SmsSetting::first();
        $firstDay = (int) ($settings->schedule_first_day ?? 5);
        $secondDay = (int) ($settings->schedule_second_day ?? 20);

        if (!$this->option('force') && !$this->option('dry-run') && !in_array((int) now()->day, [$firstDay, $secondDay], true)) {
            $this->info('Today is not a configured SMS schedule day.');
            return self::SUCCESS;
        }

        $deliveryQuery = Order::with('customer')
            ->where('status', 'ready')
            ->where(function ($query) {
                $query->whereNull('delivery_sms_last_sent_at')
                    ->orWhere('delivery_sms_last_sent_at', '<', now()->startOfMonth());
            });

        $dueQuery = Order::with('customer')
            ->whereHas('orderDetail', function ($query) {
                $query->where('due', '>', 0);
            })
            ->where('status', '!=', 'canceled')
            ->where(function ($query) {
                $query->whereNull('due_sms_last_sent_at')
                    ->orWhere('due_sms_last_sent_at', '<', now()->startOfMonth());
            });

        $this->info('Delivery-ready orders: ' . $deliveryQuery->count());
        $this->info('Due-payment orders: ' . $dueQuery->count());

        if ($this->option('dry-run')) {
            return self::SUCCESS;
        }

        $deliveryQuery->chunkById(200, function ($orders) {
                foreach ($orders as $order) {
                    SendOrderSmsJob::dispatch($order->id, SendOrderSmsJob::DELIVERY_READY)
                        ->onQueue('sms');
                    Log::info('SCHEDULED SMS JOB DISPATCHED', [
                        'order_id' => $order->id,
                        'type' => SendOrderSmsJob::DELIVERY_READY,
                        'phone' => $order->sms_phone ?: $order->customer?->phone,
                    ]);
                }
            });

        $dueQuery->chunkById(200, function ($orders) {
                foreach ($orders as $order) {
                    SendOrderSmsJob::dispatch($order->id, SendOrderSmsJob::DUE_PAYMENT)
                        ->onQueue('sms');
                    Log::info('SCHEDULED SMS JOB DISPATCHED', [
                        'order_id' => $order->id,
                        'type' => SendOrderSmsJob::DUE_PAYMENT,
                        'phone' => $order->sms_phone ?: $order->customer?->phone,
                    ]);
                }
            });

        $this->info('Scheduled SMS jobs dispatched successfully.');

        return self::SUCCESS;
    }
}
