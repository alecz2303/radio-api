<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('contests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('station_id')->nullable()->constrained('stations')->nullOnDelete();
            $table->string('title', 140);
            $table->text('question');
            $table->string('option_a', 255);
            $table->string('option_b', 255);
            $table->string('option_c', 255);
            $table->char('correct_option', 1);
            $table->unsignedInteger('max_winners')->default(1);
            $table->string('prize', 255)->nullable();
            $table->text('redemption_instructions')->nullable();
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->boolean('is_active')->default(false);
            $table->boolean('close_when_full')->default(true);
            $table->timestamps();
            $table->index(['is_active', 'starts_at', 'ends_at']);
        });

        Schema::create('contest_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contest_id')->constrained('contests')->cascadeOnDelete();
            $table->string('device_key', 100);
            $table->string('listener_name', 120)->nullable();
            $table->string('phone', 40)->nullable();
            $table->char('selected_option', 1);
            $table->boolean('is_correct')->default(false);
            $table->boolean('is_winner')->default(false);
            $table->string('claim_code', 20)->nullable()->unique();
            $table->dateTime('claimed_at')->nullable();
            $table->timestamps();
            $table->unique(['contest_id', 'device_key']);
            $table->index(['contest_id', 'is_winner']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contest_entries');
        Schema::dropIfExists('contests');
    }
};
