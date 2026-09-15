<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('push_devices', function (Blueprint $table) {
            $table->string('device_key', 191)->nullable()->unique()->after('token');
        });
    }

    public function down(): void
    {
        Schema::table('push_devices', function (Blueprint $table) {
            $table->dropUnique(['device_key']);
            $table->dropColumn('device_key');
        });
    }
};
