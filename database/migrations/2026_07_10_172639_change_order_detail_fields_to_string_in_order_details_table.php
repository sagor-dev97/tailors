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
        Schema::table('order_details', function (Blueprint $table) {
            $table->string('single_hand_punjabi')->nullable()->change();
            $table->string('double_hand_punjabi')->nullable()->change();
            $table->string('punjabi')->nullable()->change();
            $table->string('arabian_jubba')->nullable()->change();
            $table->string('kabli')->nullable()->change();
            $table->string('fatwa')->nullable()->change();
            $table->string('salwar')->nullable()->change();
            $table->string('pajama')->nullable()->change();
            $table->string('punjabi_pajama')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->boolean('single_hand_punjabi')->default(false)->change();
            $table->boolean('double_hand_punjabi')->default(false)->change();
            $table->boolean('punjabi')->default(false)->change();
            $table->boolean('arabian_jubba')->default(false)->change();
            $table->boolean('kabli')->default(false)->change();
            $table->boolean('fatwa')->default(false)->change();
            $table->boolean('salwar')->default(false)->change();
            $table->boolean('pajama')->default(false)->change();
            $table->boolean('punjabi_pajama')->default(false)->change();
        });
    }
};