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
        Schema::create('anti_cheat_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('exam_attempts')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->enum('event_type', [
                'tab_switch',
                'window_blur',
                'window_focus',
                'fullscreen_exit',
                'fullscreen_enter',
                'copy',
                'paste',
                'cut',
                'select_all',
                'right_click',
                'page_reload',
                'connection_lost',
                'connection_restored'
            ]);
            $table->json('event_data')->nullable();
            $table->integer('duration_seconds')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('occurred_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anti_cheat_logs');
    }
};
