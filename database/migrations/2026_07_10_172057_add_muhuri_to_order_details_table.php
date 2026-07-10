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
            $table->string('chata_jubba')->nullable()->after('arabian_jubba');
            $table->string('belly_loose')->nullable()->after('chata_jubba');
            $table->string('chest_loose')->nullable()->after('belly_loose');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropColumn('chata_jubba');
            $table->dropColumn('belly_loose');
            $table->dropColumn('chest_loose');
        });
    }
};
