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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            /* ===== customer ===== */
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('phone', 20);
            $table->string('receiver')->nullable();

            $table->timestamps();
        });


        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade'); // add this
            $table->string('receiver')->nullable();
            $table->string('order_number')->unique();
            $table->date('order_date');
            $table->date('delivery_date');
            $table->enum('status', ['pending', 'processing', 'completed', 'canceled'])->default('pending');
            $table->timestamps();
        });

        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            /* ===== garments ===== */
            $table->boolean('single_hand_punjabi')->default(false);
            $table->boolean('double_hand_punjabi')->default(false);
            $table->boolean('punjabi')->default(false);
            $table->boolean('arabian_jubba')->default(false);
            $table->boolean('kabli')->default(false);
            $table->boolean('fatwa')->default(false);
            $table->boolean('salwar')->default(false);
            $table->boolean('pajama')->default(false);
            $table->boolean('punjabi_pajama')->default(false);

            /* ===== upper features ===== */
            $table->boolean('chest_pocket')->default(false);
            $table->boolean('collar_button')->default(false);
            $table->boolean('double_stitch')->default(false);
            $table->boolean('front_button')->default(false);
            $table->boolean('side_cut')->default(false);

            /* ===== bottom features ===== */
            $table->boolean('back_pocket')->default(false);
            $table->boolean('front_button_pocket')->default(false);
            $table->boolean('single_pocket_design')->default(false);
            $table->boolean('double_pocket_design')->default(false);

            /* ===== upper measurements ===== */
            $table->string('length')->nullable();
            $table->string('body')->nullable();
            $table->string('belly')->nullable();
            $table->string('sleeves')->nullable();
            $table->string('neck')->nullable();
            $table->string('shoulder')->nullable();
            $table->string('cuff')->nullable();
            $table->string('hip')->nullable();

            /* ===== bottom measurements ===== */
            $table->string('bottom_length')->nullable();
            $table->string('natural')->nullable();
            $table->string('waist')->nullable();
            $table->string('hi')->nullable();
            $table->string('run')->nullable();

            /* ===== cost ===== */
            $table->decimal('fabric_qty', 10, 2)->nullable();
            $table->decimal('fabric_price', 10, 2)->nullable();
            $table->decimal('labor_qty', 10, 2)->nullable();
            $table->decimal('labor_price', 10, 2)->nullable();
            $table->decimal('design_qty', 10, 2)->nullable();
            $table->decimal('design_price', 10, 2)->nullable();
            $table->decimal('button_qty', 10, 2)->nullable();
            $table->decimal('button_price', 10, 2)->nullable();
            $table->decimal('embroidery_qty', 10, 2)->nullable();
            $table->decimal('embroidery_price', 10, 2)->nullable();
            $table->decimal('courier_qty', 10, 2)->nullable();
            $table->decimal('courier_price', 10, 2)->nullable();

            /* ===== money ===== */
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('advance', 12, 2)->default(0);
            $table->decimal('due', 12, 2)->default(0);

            /* ===== note ===== */
            $table->text('note')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('orders');
    }
};
