<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * F20 — assistant conversationnel : paramètres par établissement, sessions et messages (quotas / traçabilité).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurant_chat_settings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_enabled')->default(false);
            $table->text('system_prompt_extra')->nullable();
            $table->unsignedSmallInteger('max_user_message_length')->default(2000);
            $table->unsignedSmallInteger('max_messages_per_session')->default(40);
            $table->unsignedSmallInteger('max_messages_per_day_per_ip')->default(80);
            $table->unsignedSmallInteger('history_tail_messages')->default(20);
            $table->string('widget_position', 32)->default('bottom-end');
            $table->timestamps();

            $table->unique('restaurant_id');
        });

        Schema::create('chat_sessions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->string('token', 64)->unique();
            $table->string('ip_hash', 64);
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();

            $table->index(['restaurant_id', 'ip_hash']);
        });

        Schema::create('chat_messages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('chat_session_id')->constrained()->cascadeOnDelete();
            $table->string('role', 16);
            $table->mediumText('content');
            $table->timestamp('created_at')->useCurrent();

            $table->index(['chat_session_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chat_sessions');
        Schema::dropIfExists('restaurant_chat_settings');
    }
};
