<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('contests', function (Blueprint $table) {
            $table->boolean('auto_notify_winners')->default(false)->after('close_when_full');
        });

        Schema::table('contest_entries', function (Blueprint $table) {
            $table->string('push_token', 512)->nullable()->after('device_key');
            $table->dateTime('winner_notified_at')->nullable()->after('claimed_at');
        });
    }

    public function down(): void
    {
        Schema::table('contest_entries', function (Blueprint $table) {
            $table->dropColumn(['push_token', 'winner_notified_at']);
        });
        Schema::table('contests', function (Blueprint $table) {
            $table->dropColumn('auto_notify_winners');
        });
    }
};
