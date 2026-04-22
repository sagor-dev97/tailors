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
            /* ===== bottom features ===== */
            $table->string('botam_no')->after('punjabi')->nullable();
            $table->string('metal_botam_no')->after('botam_no')->nullable();
            $table->string('isnaf_botam_no')->after('metal_botam_no')->nullable();
            $table->string('tira')->after('isnaf_botam_no')->nullable();
            $table->string('serowani_kolar')->after('tira')->nullable();
            $table->string('band_kolar')->after('serowani_kolar')->nullable();
            $table->string('shirt_kolar')->after('band_kolar')->nullable();
            $table->boolean('book_pocket')->after('shirt_kolar')->default(false);
            $table->boolean('book_pocket_sticker')->after('book_pocket')->default(false);
            $table->boolean('two_pack_ring')->after('book_pocket_sticker')->default(false);
            $table->boolean('kof_hand')->after('two_pack_ring')->default(false);
            $table->boolean('koflin_hand')->after('kof_hand')->default(false);
            $table->boolean('kolar_black_sticker')->after('koflin_hand')->default(false);
            $table->boolean('koflin_hand_pocket')->after('kolar_black_sticker')->default(false);
            $table->boolean('koflin_hand_pocket_sticker')->after('koflin_hand_pocket')->default(false);
            $table->boolean('koflin_hand_kolar')->after('koflin_hand_pocket_sticker')->default(false);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            //
        });
    }
};
