<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Jobs\SendOrderSmsJob;

class SendMonthlyOrderSms extends Command
{
    protected $signature = 'sms:monthly-orders';
    protected $description = 'Send SMS for ready & paid orders';

   public function handle()
{
    Order::with('customer')
        ->where('status', 'ready')
        ->where('payment_status', 'paid')
        ->chunkById(200, function ($orders) {

            foreach ($orders as $order) {

                // optional: avoid duplicate SMS
                if ($order->schedule_message_status === 'sent') {
                    continue;
                }

                SendOrderSmsJob::dispatch($order)
                    ->onQueue('sms');
            }

        });

    $this->info("SMS jobs dispatched successfully.");
}
}
