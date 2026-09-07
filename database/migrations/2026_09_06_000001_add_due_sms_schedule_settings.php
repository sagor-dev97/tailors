<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sms_settings', function (Blueprint $table) {
            $table->unsignedTinyInteger('schedule_first_day')->default(5);
            $table->unsignedTinyInteger('schedule_second_day')->default(20);
            $table->text('due_sms_template')->nullable();
            $table->text('delivery_sms_template')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('sms_settings', function (Blueprint $table) {
            $table->dropColumn(['schedule_first_day', 'schedule_second_day', 'due_sms_template', 'delivery_sms_template']);
        });
    }
};
