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
            $table->boolean('is_reorder')->default(false)->after('status');
            $table->unsignedBigInteger('parent_order_id')->nullable()->after('is_reorder');
            $table->foreign('parent_order_id')->references('id')->on('orders')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
                $table->dropForeign(['parent_order_id']);
                $table->dropColumn('is_reorder');
                $table->dropColumn('parent_order_id');
        });
    }
};
