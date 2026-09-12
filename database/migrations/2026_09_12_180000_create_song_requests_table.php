<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('song_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('station_id')->constrained()->cascadeOnDelete();
            $table->foreignId('channel_id')->constrained()->cascadeOnDelete();
            $table->string('listener_name', 100);
            $table->string('song', 150);
            $table->string('artist', 150);
            $table->text('dedication')->nullable();
            $table->string('status', 20)->default('new');
            $table->timestamps();

            $table->index(['channel_id', 'status']);
            $table->index(['station_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('song_requests');
    }
};
