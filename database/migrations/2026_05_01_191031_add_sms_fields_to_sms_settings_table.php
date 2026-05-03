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
        Schema::table('sms_settings', function (Blueprint $table) {
            $table->string('sender')->nullable()->after('api_url');

            $table->longText('sms_text')->nullable()->after('sender');

            $table->string('type')->nullable()->after('sms_text');

            $table->json('templates_json')->nullable()->after('type');
            $table->string('sms_format')->nullable()->after('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sms_settings', function (Blueprint $table) {
            $table->dropColumn(['sender', 'sms_text', 'type', 'sms_format']);
        });
    }
};
