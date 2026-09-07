<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('delivery_sms_last_sent_at')->nullable()->after('schedule_count');
            $table->timestamp('due_sms_last_sent_at')->nullable()->after('delivery_sms_last_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['delivery_sms_last_sent_at', 'due_sms_last_sent_at']);
        });
    }
};
