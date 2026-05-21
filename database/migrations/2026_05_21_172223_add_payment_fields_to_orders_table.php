<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('total_amount')->after('parent_order_id')->nullable();
            $table->string('transaction_id')->after('total_amount')->nullable();
            $table->enum('payment_status', ['unpaid','paid'])->after('transaction_id')->default('unpaid');
            $table->enum('order_status', ['pending','accept','reject','completed'])->after('payment_status')->default('pending');

            $table->string('payment_method')->after('order_status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('transaction_id');
            $table->dropColumn('payment_status');
            $table->dropColumn('order_status');
            $table->dropColumn('payment_method');
        });
    }
};
